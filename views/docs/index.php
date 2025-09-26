<?php
/**
 * Dynamic Document Print Editor - Single file PHP implementation
 * - Rich text editor (contenteditable) with toolbar
 * - Placeholder insertion and data-driven preview
 * - Page settings: size, orientation, margins
 * - Save/Load/Delete templates persisted as JSON on filesystem
 * - Print-ready preview with print styles
 *
 * Requirements: PHP 7.2+
 */

declare(strict_types=1);

// ------------------------------
// Configuration
// ------------------------------

// Storage directory for templates JSON. Relative to this file.
$STORAGE_DIR = __DIR__ . '/storage';
$TEMPLATES_FILE = $STORAGE_DIR . '/templates.json';

// Maximum request body size for JSON payloads (approx) to avoid abuse (~2MB)
$MAX_JSON_BYTES = 2 * 1024 * 1024;

// ------------------------------
// Utilities
// ------------------------------

/**
 * Ensure the storage directory and templates file exist.
 */
function ensureStorage(string $dir, string $file): void {
	if (!is_dir($dir)) {
		@mkdir($dir, 0775, true);
	}
	if (!file_exists($file)) {
		$initial = json_encode(['templates' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		file_put_contents($file, $initial, LOCK_EX);
	}
}

/**
 * Read all templates from storage.
 * @return array{id:string,name:string,content:string,settings:array,lastModified:string}[]
 */
function readTemplates(string $file): array {
	if (!file_exists($file)) {
		return [];
	}
	$raw = (string)file_get_contents($file);
	if ($raw === '') {
		return [];
	}
	$data = json_decode($raw, true);
	if (!is_array($data) || !isset($data['templates']) || !is_array($data['templates'])) {
		return [];
	}
	return $data['templates'];
}

/**
 * Persist templates array to storage with file lock.
 */
function writeTemplates(string $file, array $templates): void {
	$tmp = json_encode(['templates' => array_values($templates)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	$fp = fopen($file, 'c+');
	if ($fp === false) {
		throw new RuntimeException('Unable to open storage file for writing.');
	}
	try {
		if (!flock($fp, LOCK_EX)) {
			throw new RuntimeException('Unable to acquire file lock.');
		}
		ftruncate($fp, 0);
		rewind($fp);
		fwrite($fp, (string)$tmp);
		fflush($fp);
		flock($fp, LOCK_UN);
	} finally {
		fclose($fp);
	}
}

/**
 * Generate a unique ID for templates.
 */
function generateId(): string {
	return bin2hex(random_bytes(8));
}

/**
 * Send a JSON response and exit.
 */
function jsonResponse(array $payload, int $status = 200): void {
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($payload, JSON_UNESCAPED_UNICODE);
	exit;
}

/**
 * Parse JSON body from request safely.
 */
function getJsonInput(int $maxBytes): array {
	$len = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
	if ($len > $maxBytes) {
		jsonResponse(['ok' => false, 'error' => 'Payload too large'], 413);
	}
	$raw = file_get_contents('php://input');
	if ($raw === false) {
		return [];
	}
	$raw = trim($raw);
	if ($raw === '') {
		return [];
	}
	$decoded = json_decode($raw, true);
	if (!is_array($decoded)) {
		jsonResponse(['ok' => false, 'error' => 'Invalid JSON body'], 400);
	}
	return $decoded;
}

/**
 * Normalize and validate template settings.
 */
function normalizeSettings(?array $settings): array {
	$default = [
		'pageSize' => 'A4', // A4, Letter, Legal
		'orientation' => 'portrait', // portrait, landscape
		'margins' => [
			'top' => '20mm',
			'right' => '15mm',
			'bottom' => '20mm',
			'left' => '15mm',
		],
		'fontFamily' => 'Times New Roman, serif',
		'fontSize' => '12pt',
	];
	if (!is_array($settings)) {
		return $default;
	}
	// Shallow merge
	$merged = array_merge($default, $settings);
	if (!isset($merged['margins']) || !is_array($merged['margins'])) {
		$merged['margins'] = $default['margins'];
	} else {
		$merged['margins'] = array_merge($default['margins'], $merged['margins']);
	}
	$allowedSizes = ['A4', 'Letter', 'Legal'];
	$allowedOrient = ['portrait', 'landscape'];
	if (!in_array($merged['pageSize'], $allowedSizes, true)) {
		$merged['pageSize'] = 'A4';
	}
	if (!in_array($merged['orientation'], $allowedOrient, true)) {
		$merged['orientation'] = 'portrait';
	}
	return $merged;
}

/**
 * Extract placeholders from HTML content (format: {{placeholder_name}})
 * @return string[] unique placeholder names
 */
function extractPlaceholders(string $html): array {
	$matches = [];
	preg_match_all('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', $html, $matches);
	if (!isset($matches[1])) {
		return [];
	}
	return array_values(array_unique($matches[1]));
}

// ------------------------------
// Router for AJAX actions
// ------------------------------

ensureStorage($STORAGE_DIR, $TEMPLATES_FILE);

$action = $_GET['action'] ?? $_POST['action'] ?? null;
if (is_string($action) && $action !== '') {
	try {
		$templates = readTemplates($TEMPLATES_FILE);
		switch ($action) {
			case 'list': {
				$brief = array_map(function ($t) {
					return [
						'id' => (string)$t['id'],
						'name' => (string)$t['name'],
						'lastModified' => (string)($t['lastModified'] ?? ''),
					];
				}, $templates);
				jsonResponse(['ok' => true, 'templates' => $brief]);
			}
			case 'load': {
				$id = (string)($_GET['id'] ?? $_POST['id'] ?? '');
				if ($id === '') {
					jsonResponse(['ok' => false, 'error' => 'Missing id'], 400);
				}
				$found = null;
				foreach ($templates as $t) {
					if ((string)$t['id'] === $id) { $found = $t; break; }
				}
				if ($found === null) {
					jsonResponse(['ok' => false, 'error' => 'Template not found'], 404);
				}
				jsonResponse(['ok' => true, 'template' => $found]);
			}
			case 'save': {
				$body = getJsonInput($MAX_JSON_BYTES);
				$name = trim((string)($body['name'] ?? 'Untitled'));
				$content = (string)($body['content'] ?? '');
				$settings = normalizeSettings(isset($body['settings']) && is_array($body['settings']) ? $body['settings'] : null);
				$id = isset($body['id']) ? (string)$body['id'] : '';
				if ($name === '') { $name = 'Untitled'; }
				$nowIso = date('c');
				$placeholders = extractPlaceholders($content);

				$updated = false;
				for ($i = 0; $i < count($templates); $i++) {
					if ($id !== '' && (string)$templates[$i]['id'] === $id) {
						$templates[$i]['name'] = $name;
						$templates[$i]['content'] = $content;
						$templates[$i]['settings'] = $settings;
						$templates[$i]['placeholders'] = $placeholders;
						$templates[$i]['lastModified'] = $nowIso;
						$updated = true;
						break;
					}
				}
				if (!$updated) {
					$id = generateId();
					$templates[] = [
						'id' => $id,
						'name' => $name,
						'content' => $content,
						'settings' => $settings,
						'placeholders' => $placeholders,
						'lastModified' => $nowIso,
					];
				}
				writeTemplates($TEMPLATES_FILE, $templates);
				jsonResponse(['ok' => true, 'id' => $id]);
			}
			case 'delete': {
				$body = getJsonInput($MAX_JSON_BYTES);
				$id = (string)($body['id'] ?? '');
				if ($id === '') {
					jsonResponse(['ok' => false, 'error' => 'Missing id'], 400);
				}
				$before = count($templates);
				$templates = array_values(array_filter($templates, function ($t) use ($id) {
					return (string)$t['id'] !== $id;
				}));
				if ($before === count($templates)) {
					jsonResponse(['ok' => false, 'error' => 'Template not found'], 404);
				}
				writeTemplates($TEMPLATES_FILE, $templates);
				jsonResponse(['ok' => true]);
			}
			default: {
				jsonResponse(['ok' => false, 'error' => 'Unknown action'], 400);
			}
		}
	} catch (Throwable $e) {
		jsonResponse(['ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()], 500);
	}
}

// If no action, render the app UI below
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Document Print Editor</title>
<style>
	:root {
		--bg: #0f172a;
		--panel: #111827;
		--muted: #1f2937;
		--text: #e5e7eb;
		--text-dim: #9ca3af;
		--accent: #22d3ee;
		--accent-2: #93c5fd;
		--danger: #f87171;
	}
	* { box-sizing: border-box; }
	body {
		margin: 0;
		font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
		background: var(--bg);
		color: var(--text);
	}
	.app {
		display: grid;
		grid-template-columns: 280px 1fr 360px;
		height: 100vh;
	}
	.sidebar, .rightbar {
		background: var(--panel);
		border-right: 1px solid var(--muted);
		overflow: auto;
		padding: 12px;
	}
	.rightbar { border-right: none; border-left: 1px solid var(--muted); }
	.main {
		display: flex;
		flex-direction: column;
		overflow: hidden;
	}
	.toolbar {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		padding: 10px;
		border-bottom: 1px solid var(--muted);
		background: #0b1220;
		position: sticky;
		top: 0;
		z-index: 5;
	}
	.toolbar button, .toolbar select, .toolbar input[type="text"] {
		background: #0a0f1a;
		color: var(--text);
		border: 1px solid var(--muted);
		padding: 6px 10px;
		border-radius: 6px;
		cursor: pointer;
	}
	.toolbar button:hover { border-color: var(--accent); }
	.toolbar .spacer { flex: 1; }
	.editor-wrap { flex: 1; overflow: auto; padding: 16px; }
	.editor-page {
		margin: 0 auto;
		background: white;
		color: #111827;
		box-shadow: 0 10px 30px rgba(0,0,0,0.35);
		padding: 20mm 15mm;
		min-height: 1000px;
		font-family: Times New Roman, serif;
		font-size: 12pt;
	}
	.editor-page[contenteditable="true"]:focus { outline: 2px solid var(--accent-2); }
	.section-title { font-weight: 600; color: var(--text-dim); margin: 8px 0; }
	.list { display: flex; flex-direction: column; gap: 6px; }
	.list-item {
		padding: 8px 10px;
		background: #0b1220;
		border: 1px solid var(--muted);
		border-radius: 6px;
		cursor: pointer;
	}
	.list-item:hover { border-color: var(--accent); }
	.input, .select {
		width: 100%;
		padding: 8px 10px;
		border: 1px solid var(--muted);
		border-radius: 6px;
		background: #0b1220;
		color: var(--text);
	}
	.small { font-size: 12px; color: var(--text-dim); }
	.row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
	.kv { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
	.btn-danger { border-color: var(--danger) !important; color: var(--danger) !important; }
	.badge { display: inline-block; padding: 2px 6px; border-radius: 999px; background: #0a0f1a; border: 1px solid var(--muted); font-size: 12px; color: var(--text-dim); }
	.hr { height: 1px; background: var(--muted); margin: 10px 0; }

	/* Print preview container */
	.preview-wrap { display: none; flex: 1; overflow: auto; padding: 16px; background: #060b13; }
	.preview-page { margin: 0 auto; background: white; color: #111827; box-shadow: 0 10px 30px rgba(0,0,0,0.35); min-height: 1000px; }

	/* Dynamic sizing */
	.page-A4 { width: 210mm; }
	.page-Letter { width: 8.5in; }
	.page-Legal { width: 8.5in; height: 14in; }

	/* Print styles */
	@media print {
		.app, .toolbar, .sidebar, .rightbar { display: none !important; }
		.printable { display: block !important; margin: 0 !important; box-shadow: none !important; }
		.printable .page { width: auto !important; height: auto !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; }
		body { background: white; color: black; }
	}
</style>
</head>
<body>
<div class="app">
	<div class="sidebar">
		<div class="section-title">Templates</div>
		<div class="row">
			<input id="templateName" class="input" type="text" placeholder="Template name" />
			<button id="btnSave" title="Save template">💾 Save</button>
		</div>
		<div style="height:8px"></div>
		<div class="row">
			<select id="templateList" class="select"></select>
			<button id="btnLoad" title="Load template">📂 Load</button>
		</div>
		<div style="height:8px"></div>
		<button id="btnDelete" class="btn-danger" title="Delete template">🗑️ Delete</button>
		<div class="hr"></div>
		<div class="section-title">Placeholders</div>
		<div id="placeholders" class="list"></div>
		<div class="small">Click to insert at cursor. Format: {{placeholder}}</div>
	</div>
	<div class="main">
		<div class="toolbar">
			<button data-cmd="bold" title="Bold"><b>B</b></button>
			<button data-cmd="italic" title="Italic"><i>I</i></button>
			<button data-cmd="underline" title="Underline"><u>U</u></button>
			<button data-cmd="insertUnorderedList" title="Bullets">• List</button>
			<button data-cmd="insertOrderedList" title="Numbered list">1. List</button>
			<button data-cmd="justifyLeft" title="Align left">⟸</button>
			<button data-cmd="justifyCenter" title="Align center">≡</button>
			<button data-cmd="justifyRight" title="Align right">⟹</button>
			<select id="fontSize">
				<option value="10pt">10</option>
				<option value="11pt">11</option>
				<option value="12pt" selected>12</option>
				<option value="14pt">14</option>
				<option value="16pt">16</option>
				<option value="18pt">18</option>
				<option value="24pt">24</option>
			</select>
			<select id="fontFamily">
				<option>Times New Roman, serif</option>
				<option>Georgia, serif</option>
				<option>Arial, Helvetica, sans-serif</option>
				<option>Calibri, sans-serif</option>
				<option>Cambria, serif</option>
			</select>
			<div class="spacer"></div>
			<button id="btnPreview">👁️ Preview</button>
			<button id="btnPrint">🖨️ Print</button>
		</div>
		<div class="editor-wrap" id="editorWrap">
			<div id="editorPage" class="editor-page page-A4" contenteditable="true" spellcheck="false"></div>
		</div>
		<div class="preview-wrap" id="previewWrap">
			<div id="previewPage" class="preview-page page-A4"></div>
		</div>
	</div>
	<div class="rightbar">
		<div class="section-title">Page Settings</div>
		<div class="row">
			<label class="small">Size</label>
			<select id="pageSize" class="select">
				<option value="A4" selected>A4</option>
				<option value="Letter">Letter</option>
				<option value="Legal">Legal</option>
			</select>
		</div>
		<div class="row" style="margin-top:6px">
			<label class="small">Orientation</label>
			<select id="orientation" class="select">
				<option value="portrait" selected>Portrait</option>
				<option value="landscape">Landscape</option>
			</select>
		</div>
		<div class="section-title" style="margin-top:8px">Margins</div>
		<div class="kv">
			<label class="small">Top</label><input id="marginTop" class="input" value="20mm" />
			<label class="small">Right</label><input id="marginRight" class="input" value="15mm" />
			<label class="small">Bottom</label><input id="marginBottom" class="input" value="20mm" />
			<label class="small">Left</label><input id="marginLeft" class="input" value="15mm" />
		</div>
		<div class="section-title" style="margin-top:8px">Data for Preview</div>
		<div id="dataForm" class="kv"></div>
		<button id="btnApplyData" style="margin-top:8px">Apply Data</button>
	</div>
</div>

<script>
"use strict";
(function(){
	const apiBase = window.location.pathname;
	const q = (sel) => document.querySelector(sel);
	const byId = (id) => document.getElementById(id);

	const editorPage = byId('editorPage');
	const previewPage = byId('previewPage');
	const editorWrap = byId('editorWrap');
	const previewWrap = byId('previewWrap');

	const templateName = byId('templateName');
	const templateList = byId('templateList');
	const btnSave = byId('btnSave');
	const btnLoad = byId('btnLoad');
	const btnDelete = byId('btnDelete');
	const btnPreview = byId('btnPreview');
	const btnPrint = byId('btnPrint');

	const pageSize = byId('pageSize');
	const orientation = byId('orientation');
	const marginTop = byId('marginTop');
	const marginRight = byId('marginRight');
	const marginBottom = byId('marginBottom');
	const marginLeft = byId('marginLeft');
	const fontSize = byId('fontSize');
	const fontFamily = byId('fontFamily');
	const placeholdersEl = byId('placeholders');
	const dataForm = byId('dataForm');
	const btnApplyData = byId('btnApplyData');

	let currentTemplateId = '';

	// Toolbar commands
	for (const btn of document.querySelectorAll('.toolbar button[data-cmd]')) {
		btn.addEventListener('click', () => {
			document.execCommand(btn.getAttribute('data-cmd'));
			editorPage.focus();
		});
	}
	fontSize.addEventListener('change', () => {
		editorPage.style.fontSize = fontSize.value;
	});
	fontFamily.addEventListener('change', () => {
		editorPage.style.fontFamily = fontFamily.value;
	});

	// Page settings
	function applyPageSettings(el) {
		const sizeCls = 'page-' + pageSize.value;
		el.classList.remove('page-A4','page-Letter','page-Legal');
		el.classList.add(sizeCls);
		el.style.paddingTop = marginTop.value;
		el.style.paddingRight = marginRight.value;
		el.style.paddingBottom = marginBottom.value;
		el.style.paddingLeft = marginLeft.value;
		el.style.fontFamily = fontFamily.value;
		el.style.fontSize = fontSize.value;
	}
	[pageSize, marginTop, marginRight, marginBottom, marginLeft, fontSize, fontFamily].forEach(el => el.addEventListener('input', () => {
		applyPageSettings(editorPage);
		applyPageSettings(previewPage);
	}));

	// Orientation affects preview scaling (simple rotate by swapping width/height using CSS transform on container)
	orientation.addEventListener('change', () => {
		const landscape = orientation.value === 'landscape';
		[editorPage, previewPage].forEach(el => {
			el.style.transformOrigin = 'top left';
			if (landscape) {
				el.style.transform = 'rotate(90deg) translateY(-100%)';
			} else {
				el.style.transform = 'none';
			}
		});
	});

	// Placeholder insertion and data capture
	function extractPlaceholders(html){
		const re = /\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/g;
		const set = new Set();
		let m;
		while ((m = re.exec(html)) !== null) set.add(m[1]);
		return Array.from(set);
	}
	function rebuildPlaceholdersList() {
		const names = extractPlaceholders(editorPage.innerHTML);
		placeholdersEl.innerHTML = '';
		if (names.length === 0) {
			const el = document.createElement('div');
			el.className = 'small';
			el.textContent = 'No placeholders detected.';
			placeholdersEl.appendChild(el);
		}
		for (const name of names) {
			const item = document.createElement('div');
			item.className = 'list-item';
			item.innerHTML = `{{${name}}}`;
			item.addEventListener('click', () => insertAtCursor(`{{${name}}}`));
			placeholdersEl.appendChild(item);
		}
		// Build data form
		dataForm.innerHTML = '';
		for (const name of names) {
			const label = document.createElement('label');
			label.className = 'small';
			label.textContent = name;
			const input = document.createElement('input');
			input.className = 'input';
			input.dataset.key = name;
			dataForm.appendChild(label);
			dataForm.appendChild(input);
		}
	}
	function insertAtCursor(text) {
		const sel = window.getSelection();
		if (!sel || sel.rangeCount === 0) return;
		const range = sel.getRangeAt(0);
		range.deleteContents();
		range.insertNode(document.createTextNode(text));
		range.collapse(false);
		sel.removeAllRanges();
		sel.addRange(range);
		editorPage.focus();
	}
	editorPage.addEventListener('input', rebuildPlaceholdersList);

	// Preview and print
	function renderPreview() {
		const html = editorPage.innerHTML;
		const values = {};
		for (const input of dataForm.querySelectorAll('input')) {
			values[input.dataset.key] = input.value;
		}
		let out = html;
		for (const [k, v] of Object.entries(values)) {
			// Replace all occurrences; escape regex
			const esc = k.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
			const re = new RegExp('\\{\\{\\s*' + esc + '\\s*\\}}', 'g');
			out = out.replace(re, v);
		}
		previewPage.innerHTML = out;
		applyPageSettings(previewPage);
	}
	btnPreview.addEventListener('click', () => {
		if (previewWrap.style.display === 'none' || previewWrap.style.display === '') {
			renderPreview();
			editorWrap.style.display = 'none';
			previewWrap.style.display = 'block';
			btnPreview.textContent = '✍️ Edit';
		} else {
			previewWrap.style.display = 'none';
			editorWrap.style.display = 'block';
			btnPreview.textContent = '👁️ Preview';
		}
	});
	btnApplyData.addEventListener('click', () => {
		renderPreview();
		if (previewWrap.style.display !== 'block') {
			previewWrap.style.display = 'block';
			editorWrap.style.display = 'none';
			btnPreview.textContent = '✍️ Edit';
		}
	});
	btnPrint.addEventListener('click', () => {
		// Ensure preview is up-to-date, then open print dialog on a new window for cleaner margins
		renderPreview();
		const win = window.open('', '_blank');
		const css = document.querySelector('style').outerHTML;
		win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Print</title>' + css + '</head><body>');
		win.document.write('<div class="printable"><div class="page">');
		// Clone preview content with inline styles
		const clone = previewPage.cloneNode(true);
		clone.classList.add('page');
		win.document.body.appendChild(clone);
		win.document.write('</div></div></body></html>');
		win.document.close();
		win.focus();
		setTimeout(() => { win.print(); }, 150);
	});

	// API helpers
	async function apiList() {
		const res = await fetch(apiBase + '?action=list');
		return res.json();
	}
	async function apiLoad(id) {
		const res = await fetch(apiBase + '?action=load&id=' + encodeURIComponent(id));
		return res.json();
	}
	async function apiSave(payload) {
		const res = await fetch(apiBase + '?action=save', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
		return res.json();
	}
	async function apiDelete(id) {
		const res = await fetch(apiBase + '?action=delete', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) });
		return res.json();
	}

	// Populate template dropdown
	async function refreshTemplateList(selectId) {
		const out = await apiList();
		if (!out.ok) return;
		templateList.innerHTML = '';
		for (const t of out.templates) {
			const opt = document.createElement('option');
			opt.value = t.id; opt.textContent = t.name;
			if (selectId && selectId === t.id) opt.selected = true;
			templateList.appendChild(opt);
		}
	}

	// Save template
	btnSave.addEventListener('click', async () => {
		const payload = {
			id: currentTemplateId || undefined,
			name: templateName.value || 'Untitled',
			content: editorPage.innerHTML,
			settings: {
				pageSize: pageSize.value,
				orientation: orientation.value,
				margins: { top: marginTop.value, right: marginRight.value, bottom: marginBottom.value, left: marginLeft.value },
				fontFamily: fontFamily.value,
				fontSize: fontSize.value,
			}
		};
		const out = await apiSave(payload);
		if (!out.ok) { alert('Save failed: ' + out.error); return; }
		currentTemplateId = out.id;
		await refreshTemplateList(currentTemplateId);
		alert('Saved ✔');
	});

	// Load template
	btnLoad.addEventListener('click', async () => {
		const id = templateList.value;
		if (!id) { alert('No template selected'); return; }
		const out = await apiLoad(id);
		if (!out.ok) { alert('Load failed: ' + out.error); return; }
		const t = out.template;
		currentTemplateId = t.id;
		templateName.value = t.name;
		editorPage.innerHTML = t.content || '';
		pageSize.value = t.settings?.pageSize || 'A4';
		orientation.value = t.settings?.orientation || 'portrait';
		marginTop.value = t.settings?.margins?.top || '20mm';
		marginRight.value = t.settings?.margins?.right || '15mm';
		marginBottom.value = t.settings?.margins?.bottom || '20mm';
		marginLeft.value = t.settings?.margins?.left || '15mm';
		fontFamily.value = t.settings?.fontFamily || 'Times New Roman, serif';
		fontSize.value = t.settings?.fontSize || '12pt';
		applyPageSettings(editorPage);
		rebuildPlaceholdersList();
	});

	// Delete template
	btnDelete.addEventListener('click', async () => {
		const id = templateList.value || currentTemplateId;
		if (!id) { alert('No template selected'); return; }
		if (!confirm('Delete this template?')) return;
		const out = await apiDelete(id);
		if (!out.ok) { alert('Delete failed: ' + out.error); return; }
		currentTemplateId = '';
		templateName.value = '';
		editorPage.innerHTML = '';
		rebuildPlaceholdersList();
		await refreshTemplateList();
		alert('Deleted ✔');
	});

	// Initial content & UI state
	const starter = '<div style="text-align:center"><div style="font-size:18pt;font-weight:bold">Document Title</div><div class="badge" style="margin:8px 0">{{date}}</div></div><p>Dear {{recipient_name}},</p><p>Thank you for choosing {{company_name}}. This is a dynamic document. You can add placeholders like {{invoice.number}} and fill them from the right panel.</p><p>Sincerely,<br>{{sender_name}}</p>';
	if (!editorPage.innerHTML) editorPage.innerHTML = starter;
	applyPageSettings(editorPage);
	applyPageSettings(previewPage);
	rebuildPlaceholdersList();
	refreshTemplateList();
})();
</script>
</body>
</html>

