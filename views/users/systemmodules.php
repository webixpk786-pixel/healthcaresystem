<?php

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('1,4');
$canEdit = $can['canEdit'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];

// Calculate statistics
$totalModules = count($modules);
$activeModules = count(array_filter($modules, fn($m) => $m['is_active']));
$parentModules = count(array_filter($modules, fn($m) => !$m['parent_id']));
$subModules = count(array_filter($modules, fn($m) => $m['parent_id']));
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="system-modules-management" style="min-height: 95vh; margin: -25px;">
    <!-- Header Section -->
    <div class="page-header" style="margin-bottom: 16px;">
        <div class="row">
            <div class="col-md-7">
                <h1 style="color: #1e293b; font-size: 1.5rem; font-weight: 700; margin-bottom: 4px;">
                    <i class="fas fa-cogs" style="color: #6366f1; margin-right: 8px;"></i>
                    <?= \app\components\LanguageManager::translate('System Modules Management') ?>
                </h1>
                <p style="color: #64748b; font-size: 0.85rem; margin: 0;">
                    <?= \app\components\LanguageManager::translate('Manage system modules, configure settings, and control module behavior') ?>
                </p>
            </div>
            <div class="col-md-5 text-right">
                <div class="header-actions" style="display: flex; gap: 6px; justify-content: flex-end;">
                    <?= \app\widgets\LanguageSwitcher::widget(['position' => 'bottom-left']) ?>
                    <?php if ($canAdd): ?>
                        <button id="addModuleBtn"
                            style="display: flex;align-items: center;gap: 6px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; border: none; border-radius: 6px; padding: 6px 12px; font-size: 0.75rem; font-weight: 600; cursor: pointer; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);">
                            <i class="fas fa-plus"></i> <?= \app\components\LanguageManager::translate('Add Module') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules Summary Cards -->
    <div class="modules-summary" style="margin-bottom: 12px;">
        <div class="row g-1">
            <!-- Card 1 - Total Modules -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 8px; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.1rem; font-weight: 700; color: white;">
                        <i class="fas fa-cogs" style="font-size: 1rem; opacity: 0.9; margin-right: 6px;"></i>
                        <span id="totalModules"><?= $totalModules ?></span>
                        <small style="font-size: 0.6rem; font-weight: 500; margin: 0 6px;">Modules</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 1px 4px; border-radius: 6px; backdrop-filter: blur(10px); font-size: 0.5rem; font-weight: 600;">
                            TOTAL
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 - Active Modules -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px; padding: 8px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.1rem; font-weight: 700; color: white;">
                        <i class="fas fa-check-circle" style="font-size: 1rem; opacity: 0.9; margin-right: 6px;"></i>
                        <span id="activeModules"><?= $activeModules ?></span>
                        <small style="font-size: 0.6rem; font-weight: 500; margin: 0 6px;">Active</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 1px 4px; border-radius: 6px; backdrop-filter: blur(10px); font-size: 0.5rem; font-weight: 600;">
                            ACTIVE
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 - Parent Modules -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 8px; padding: 8px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.1rem; font-weight: 700; color: white;">
                        <i class="fas fa-layer-group" style="font-size: 1rem; opacity: 0.9; margin-right: 6px;"></i>
                        <span id="parentModules"><?= $parentModules ?></span>
                        <small style="font-size: 0.6rem; font-weight: 500; margin: 0 6px;">Parents</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 1px 4px; border-radius: 6px; backdrop-filter: blur(10px); font-size: 0.5rem; font-weight: 600;">
                            PARENTS
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 - Sub Modules -->
            <div class="col-lg-3 col-md-6">
                <div class="summary-card h-100"
                    style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 8px; padding: 8px; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.25); border: none; position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; font-size: 1.1rem; font-weight: 700; color: white;">
                        <i class="fas fa-sitemap" style="font-size: 1rem; opacity: 0.9; margin-right: 6px;"></i>
                        <span id="subModules"><?= $subModules ?></span>
                        <small style="font-size: 0.6rem; font-weight: 500; margin: 0 6px;">Submodules</small>
                        <div
                            style="margin-left: auto; background: rgba(255,255,255,0.2); padding: 1px 4px; border-radius: 6px; backdrop-filter: blur(10px); font-size: 0.5rem; font-weight: 600;">
                            SUBMODULES
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Section -->
    <div class="instructions-section" style="margin-bottom: 12px; display:none">
        <div class="row">
            <div class="col-md-12">
                <div class="content-card"
                    style="background: white; border-radius: 8px; padding: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                    <h6
                        style="color: #1e293b; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="color: #6366f1; margin-right: 6px; font-size: 0.8rem;"></i>
                        <?= \app\components\LanguageManager::translate('How to Use This Page') ?>
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul style="margin: 0; padding-left: 16px; color: #64748b; font-size: 0.75rem;">
                                <li style="margin-bottom: 3px;">
                                    <?= \app\components\LanguageManager::translate('This page lists all <strong>system modules</strong> and their key details. Modules are the building blocks of your application') ?>
                                </li>
                                <li style="margin-bottom: 3px;">
                                    <?= \app\components\LanguageManager::translate('You can <strong>edit</strong> module details by double-clicking on any field. Changes are saved instantly and securely') ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul style="margin: 0; padding-left: 16px; color: #64748b; font-size: 0.75rem;">
                                <li style="margin-bottom: 3px;">
                                    <?= \app\components\LanguageManager::translate('The <strong>Active/Inactive</strong> toggle lets you enable or disable a module. Toggle the switch to update the module status') ?>
                                </li>
                                <li style="margin-bottom: 3px;">
                                    <?= \app\components\LanguageManager::translate('Use the <strong>search bar</strong> to quickly filter modules by name or description') ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules Table Container -->
    <div class="modules-table-container"
        style="background: white; border-radius: 8px; padding: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; display: flex; flex-direction: column; height: 70vh;">

        <!-- Search Section -->
        <div class="search-section"
            style="padding: 8px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-radius: 6px 6px 0 0;">
            <div class="row g-1">
                <div class="col-md-10">
                    <div class="form-group mb-0">
                        <label for="moduleSearch"
                            style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;"><?= \app\components\LanguageManager::translate('Search Modules') ?></label>
                        <input type="text" class="form-control" id="moduleSearch"
                            placeholder="<?= \app\components\LanguageManager::translate('Search modules by name, description...') ?>"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; height: 28px;">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label
                            style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">&nbsp;</label>
                        <div style="display: flex; gap: 3px;">
                            <button class="btn btn-sm btn-outline-primary" onclick="applyFilters()"
                                style="border: 1px solid #3b82f6; padding: 3px 6px; border-radius: 3px; font-size: 0.65rem; background: white; color: #3b82f6; height: 28px;">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()"
                                style="border: 1px solid #6b7280; padding: 3px 6px; border-radius: 3px; font-size: 0.65rem; background: white; color: #6b7280; height: 28px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable Table Container -->
        <div class="table-scroll-wrapper" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <div class="table-responsive" style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                <table class="table" id="modulesTable" style="width: 100%; border-collapse: collapse; margin: 0;">
                    <thead style="position: sticky; top: 0; z-index: 10; background: #f8fafc;">
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th
                                style="padding: 6px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Module') ?></th>
                            <th
                                style="padding: 6px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Link') ?></th>
                            <th
                                style="padding: 6px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Description') ?></th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Sort') ?></th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Icon') ?></th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Color') ?></th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Active') ?></th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                <?= \app\components\LanguageManager::translate('Status') ?></th>
                        </tr>
                    </thead>
                    <tbody id="modulesTableBody">
                        <?php foreach ($modules as $module):
                            $child = false;
                            if ($module['parent_id'] != null) {
                                $child = true;
                            }
                        ?>
                            <tr style="pointer-events: <?= $canEdit ? 'auto' : 'none' ?>;" data-id="<?= $module['id'] ?>"
                                data-name="<?= htmlspecialchars(strtolower($module['name'])) ?>"
                                data-desc="<?= htmlspecialchars(strtolower($module['description'] ?? '')) ?>"
                                data-link="<?= htmlspecialchars(strtolower($module['link'] ?? '')) ?>"
                                class="<?= $child ? 'submodule-row' : 'parent-row' ?>">
                                <td class="editable-cell"
                                    style="font-weight:<?= $child ? 'normal' : 'bold' ?>; padding: 6px; font-size: 0.75rem;"
                                    data-field="name">
                                    <div style="display: flex; align-items: center;">
                                        <?php if ($child): ?>
                                            <span style="color: #8b5cf6; margin-right: 6px; font-size: 0.6rem;">&#9679;</span>
                                        <?php else: ?>
                                            <div
                                                style="width: 6px; height: 6px; background: #6366f1; border-radius: 50%; margin-right: 6px;">
                                            </div>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($module['name']) ?>
                                    </div>
                                </td>
                                <td class="editable-cell" data-field="link" style="padding: 6px; font-size: 0.7rem;">
                                    <?= htmlspecialchars($module['link']) ?>
                                </td>
                                <td class="editable-cell" data-field="description"
                                    style="padding: 6px; font-size: 0.7rem; color: #64748b;">
                                    <?= htmlspecialchars($module['description']) ?>
                                </td>
                                <td class="editable-cell" data-field="sort_order"
                                    style="padding: 6px; text-align: center; font-size: 0.7rem;">
                                    <?= htmlspecialchars($module['sort_order']) ?>
                                </td>
                                <td class="editable-cell" data-field="icon" style="padding: 6px; text-align: center;">
                                    <div class="icon-display"
                                        style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <i class="<?= htmlspecialchars($module['icon'] ?? 'fas fa-cube') ?>"
                                            style="color: <?= htmlspecialchars($module['color'] ?? '#646cff') ?>; font-size: 14px;"></i>
                                        <span class="icon-text"
                                            style="font-size: 0.65rem; font-family: monospace;"><?= htmlspecialchars($module['icon'] ?? 'fas fa-cube') ?></span>
                                    </div>
                                </td>
                                <td class="editable-cell" data-field="color" style="padding: 6px; text-align: center;">
                                    <div class="color-display"
                                        style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <div class="color-preview"
                                            style="width: 16px; height: 16px; border-radius: 3px; border: 1px solid #ddd; background-color: <?= htmlspecialchars($module['color'] ?? '#646cff') ?>;">
                                        </div>
                                        <span class="color-value"
                                            style="font-size: 0.65rem; font-family: monospace;"><?= htmlspecialchars($module['color'] ?? '#646cff') ?></span>
                                    </div>
                                </td>
                                <td style="padding: 6px; text-align: center;">
                                    <input type="checkbox" class="module-active-checkbox" data-id="<?= $module['id'] ?>"
                                        <?= $module['is_active'] ? 'checked' : '' ?> />
                                </td>
                                <td style="padding: 6px; text-align: center;">
                                    <select class="module-status-select" data-id="<?= $module['id'] ?>"
                                        style="padding: 2px 4px; border-radius: 3px; border: 1px solid #e0e0e0; font-size: 0.65rem; height: 24px;">
                                        <option value="1" <?= $module['current_status'] == 1 ? 'selected' : '' ?>>
                                            <?= \app\components\LanguageManager::translate('Active') ?></option>
                                        <option value="2" <?= $module['current_status'] == 2 ? 'selected' : '' ?>>
                                            <?= \app\components\LanguageManager::translate('Maintenance Mode') ?></option>
                                        <option value="3" <?= $module['current_status'] == 3 ? 'selected' : '' ?>>
                                            <?= \app\components\LanguageManager::translate('Restricted') ?></option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Module Modal -->
<div id="addModuleModal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.18); align-items:center; justify-content:center;">
    <div
        style="background:#fff; border-radius:10px; max-width:600px; width:95vw; margin:auto; padding:28px 24px; box-shadow:0 4px 24px rgba(100,108,255,0.13); position:relative;">
        <button id="closeAddModuleModal"
            style="position:absolute; right:12px; top:10px; background:none; border:none; font-size:20px; color:#888; cursor:pointer;">&times;</button>
        <h3 style="margin-top:0; color:#232360; font-size:20px; font-weight:700;">Add Module</h3>
        <form id="addModuleForm">
            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Type:</label>
                    <select id="addModuleType" name="type"
                        style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;">
                        <option value="parent">Parent Module</option>
                        <option value="child">Child Module</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Status:</label>
                    <select name="current_status"
                        style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;">
                        <option value="1">Active</option>
                        <option value="2">Maintenance</option>
                        <option value="3">Restricted</option>
                    </select>
                </div>
            </div>
            <div id="parentSelectWrapper" style="display:none; margin-bottom: 12px;">
                <label style="font-size:13px; font-weight:600;">Parent Module:</label>
                <select id="addModuleParentId" name="parent_id"
                    style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;">
                    <option value="">Select Parent</option>
                    <?php foreach ($modules as $m): if (!$m['parent_id']): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                    <?php endif;
                    endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Name:</label>
                    <input type="text" name="name" required
                        style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;" />
                </div>
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Link:</label>
                    <input type="text" name="link"
                        style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;" />
                </div>
            </div>
            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Description:</label>
                    <input type="text" name="description"
                        style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;" />
                </div>
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Sort Order:</label>
                    <input type="number" name="sort_order"
                        style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;" />
                </div>
            </div>
            <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Icon:</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i id="iconPreview" class="fas fa-cube"
                            style="color: #646cff; font-size: 18px; margin-right: 8px;"></i>
                        <input type="text" name="icon" value="fas fa-cube"
                            style="flex: 1; padding: 7px 8px; border-radius: 6px; border: 1px solid #e0e0e0; font-family: monospace;"
                            placeholder="fas fa-cube" />
                    </div>
                </div>
                <div style="flex: 1;">
                    <label style="font-size:13px; font-weight:600;">Color:</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="color" name="color" value="#646cff"
                            style="width: 50px; height: 40px; border: none; border-radius: 6px; cursor: pointer;" />
                        <input type="text" name="color_text" value="#646cff"
                            style="flex: 1; padding: 7px 8px; border-radius: 6px; border: 1px solid #e0e0e0; font-family: monospace;"
                            placeholder="#646cff" />
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 18px;">
                <label style="font-size:13px; font-weight:600;">Active:</label>
                <select name="is_active"
                    style="width:100%; padding:7px 8px; border-radius:6px; border:1px solid #e0e0e0;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button type="submit"
                style="width:100%; background:#646cff; color:#fff; border:none; border-radius:6px; padding:10px 0; font-size:15px; font-weight:600; cursor:pointer;">Add
                Module</button>
        </form>
    </div>
</div>

<script>
    $(function() {
        // Search filter
        $('#moduleSearch').on('input', function() {
            applyFilters();
        });

        // Inline editing on double-click
        $(document).on('dblclick', '.editable-cell', function() {
            if ($(this).hasClass('editing')) return;
            var $cell = $(this);
            var oldVal = $cell.text().trim();
            var field = $cell.data('field');

            // Special handling for icon field
            if (field === 'icon') {
                var $iconDisplay = $cell.find('.icon-display');
                var currentIcon = $iconDisplay.find('.icon-text').text();
                var currentColor = $cell.closest('tr').find('.color-display .color-value').text() ||
                    '#646cff';

                var $iconInput = $('<div style="display: flex; align-items: center; gap: 6px;">' +
                    '<i class="' + currentIcon + '" style="color: ' + currentColor +
                    '; font-size: 14px; margin-right: 6px;"></i>' +
                    '<input type="text" class="edit-icon-text" value="' + currentIcon +
                    '" style="flex: 1; padding: 3px 6px; border-radius: 3px; border: 1px solid #a3a3ff; font-family: monospace; font-size: 0.65rem;" placeholder="fas fa-cube" />' +
                    '</div>');

                $cell.empty().append($iconInput).addClass('editing');

                // Update icon preview as user types
                $iconInput.find('.edit-icon-text').on('input', function() {
                    var iconVal = $(this).val();
                    $iconInput.find('i').attr('class', iconVal);
                });

                $iconInput.find('.edit-icon-text').focus();

                // Handle save on blur or enter
                $iconInput.on('blur keydown', function(e) {
                    if (e.type === 'blur' || (e.type === 'keydown' && e.key === 'Enter')) {
                        var newIcon = $iconInput.find('.edit-icon-text').val().trim();
                        if (newIcon === '') {
                            newIcon = currentIcon;
                        }

                        if (newIcon !== currentIcon) {
                            var $row = $cell.closest('tr');
                            var id = $row.data('id');

                            // Save via AJAX
                            $.ajax({
                                url: 'index.php?r=users/updatemodulefield',
                                method: 'POST',
                                data: {
                                    id: id,
                                    field: field,
                                    value: newIcon
                                },
                                dataType: 'json',
                                success: function(resp) {
                                    if (typeof showGlobalAlert === 'function') {
                                        showGlobalAlert(resp.message ||
                                            'Module icon updated!',
                                            resp.success ? 'success' : 'error');
                                    } else {
                                        Swal.fire(resp.success ? 'Success' : 'Error',
                                            resp
                                            .message || 'Module icon updated!', resp
                                            .success ? 'success' : 'error');
                                    }

                                    if (resp.success) {
                                        // Update the display
                                        $cell.html(
                                            '<div class="icon-display" style="display: flex; align-items: center; justify-content: center; gap: 6px;">' +
                                            '<i class="' + newIcon +
                                            '" style="color: ' + currentColor +
                                            '; font-size: 14px;"></i>' +
                                            '<span class="icon-text" style="font-size: 0.65rem; font-family: monospace;">' +
                                            newIcon +
                                            '</span>' +
                                            '</div>');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error', 'Failed to update module icon.',
                                        'error');
                                    // Restore original display
                                    $cell.html(
                                        '<div class="icon-display" style="display: flex; align-items: center; justify-content: center; gap: 6px;">' +
                                        '<i class="' + currentIcon +
                                        '" style="color: ' + currentColor +
                                        '; font-size: 14px;"></i>' +
                                        '<span class="icon-text" style="font-size: 0.65rem; font-family: monospace;">' +
                                        currentIcon +
                                        '</span>' +
                                        '</div>');
                                }
                            });
                        } else {
                            // Restore original display
                            $cell.html(
                                '<div class="icon-display" style="display: flex; align-items: center; justify-content: center; gap: 6px;">' +
                                '<i class="' + currentIcon + '" style="color: ' + currentColor +
                                '; font-size: 14px;"></i>' +
                                '<span class="icon-text" style="font-size: 0.65rem; font-family: monospace;">' +
                                currentIcon + '</span>' +
                                '</div>');
                        }
                        $cell.removeClass('editing');
                    }
                });
                return;
            }

            // Special handling for color field
            if (field === 'color') {
                var $colorDisplay = $cell.find('.color-display');
                var currentColor = $colorDisplay.find('.color-value').text();

                var $colorInput = $('<div style="display: flex; align-items: center; gap: 6px;">' +
                    '<input type="color" class="edit-color-picker" value="' + currentColor +
                    '" style="width: 24px; height: 24px; border: none; border-radius: 3px; cursor: pointer;" />' +
                    '<input type="text" class="edit-color-text" value="' + currentColor +
                    '" style="flex: 1; padding: 3px 6px; border-radius: 3px; border: 1px solid #a3a3ff; font-family: monospace; font-size: 0.65rem;" />' +
                    '</div>');

                $cell.empty().append($colorInput).addClass('editing');

                // Sync color picker with text input
                $colorInput.find('.edit-color-picker').on('input', function() {
                    $colorInput.find('.edit-color-text').val($(this).val());
                });

                $colorInput.find('.edit-color-text').on('input', function() {
                    var colorVal = $(this).val();
                    if (/^#[0-9A-F]{6}$/i.test(colorVal)) {
                        $colorInput.find('.edit-color-picker').val(colorVal);
                    }
                });

                $colorInput.find('.edit-color-text').focus();

                // Handle save on blur or enter
                $colorInput.on('blur keydown', function(e) {
                    if (e.type === 'blur' || (e.type === 'keydown' && e.key === 'Enter')) {
                        var newColor = $colorInput.find('.edit-color-text').val().trim();
                        if (newColor === '' || !/^#[0-9A-F]{6}$/i.test(newColor)) {
                            newColor = currentColor;
                        }

                        if (newColor !== currentColor) {
                            var $row = $cell.closest('tr');
                            var id = $row.data('id');

                            // Save via AJAX
                            $.ajax({
                                url: 'index.php?r=users/updatemodulefield',
                                method: 'POST',
                                data: {
                                    id: id,
                                    field: field,
                                    value: newColor
                                },
                                dataType: 'json',
                                success: function(resp) {
                                    if (typeof showGlobalAlert === 'function') {
                                        showGlobalAlert(resp.message ||
                                            'Module color updated!',
                                            resp.success ? 'success' : 'error');
                                    } else {
                                        Swal.fire(resp.success ? 'Success' : 'Error',
                                            resp
                                            .message || 'Module color updated!',
                                            resp
                                            .success ? 'success' : 'error');
                                    }

                                    if (resp.success) {
                                        // Update the display
                                        $cell.html(
                                            '<div class="color-display" style="display: flex; align-items: center; justify-content: center; gap: 6px;">' +
                                            '<div class="color-preview" style="width: 16px; height: 16px; border-radius: 3px; border: 1px solid #ddd; background-color: ' +
                                            newColor + ';"></div>' +
                                            '<span class="color-value" style="font-size: 0.65rem; font-family: monospace;">' +
                                            newColor + '</span>' +
                                            '</div>');
                                    }
                                },
                                error: function() {
                                    Swal.fire('Error', 'Failed to update module color.',
                                        'error');
                                    // Restore original display
                                    $cell.html(
                                        '<div class="color-display" style="display: flex; align-items: center; justify-content: center; gap: 6px;">' +
                                        '<div class="color-preview" style="width: 16px; height: 16px; border-radius: 3px; border: 1px solid #ddd; background-color: ' +
                                        currentColor + ';"></div>' +
                                        '<span class="color-value" style="font-size: 0.65rem; font-family: monospace;">' +
                                        currentColor + '</span>' +
                                        '</div>');
                                }
                            });
                        } else {
                            // Restore original display
                            $cell.html(
                                '<div class="color-display" style="display: flex; align-items: center; justify-content: center; gap: 6px;">' +
                                '<div class="color-preview" style="width: 16px; height: 16px; border-radius: 3px; border: 1px solid #ddd; background-color: ' +
                                currentColor + ';"></div>' +
                                '<span class="color-value" style="font-size: 0.65rem; font-family: monospace;">' +
                                currentColor + '</span>' +
                                '</div>');
                        }
                        $cell.removeClass('editing');
                    }
                });
                return;
            }

            var $input = $(
                    '<input style="height: 24px;width:auto; font-size: 0.7rem;" type="text" class="edit-input-field" />'
                )
                .val(oldVal);
            $cell.empty().append($input).addClass('editing');
            $input.focus();
            $input.on('blur keydown', function(e) {
                if (e.type === 'blur' || (e.type === 'keydown' && e.key === 'Enter')) {
                    var newVal = $input.val().trim();
                    if (newVal === '') {
                        $cell.text(oldVal).removeClass('editing');
                        return;
                    }
                    if (newVal !== oldVal) {
                        var $row = $cell.closest('tr');
                        var id = $row.data('id');
                        $cell.text(newVal).removeClass('editing');
                        // Save via AJAX
                        $.ajax({
                            url: 'index.php?r=users/updatemodulefield',
                            method: 'POST',
                            data: {
                                id: id,
                                field: field,
                                value: newVal
                            },
                            dataType: 'json',
                            success: function(resp) {
                                if (typeof showGlobalAlert === 'function') {
                                    showGlobalAlert(resp.message || 'Module updated!',
                                        resp.success ? 'success' : 'error');
                                } else {
                                    Swal.fire(resp.success ? 'Success' : 'Error', resp
                                        .message || 'Module updated!', resp
                                        .success ? 'success' : 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to update module.', 'error');
                            }
                        });
                    } else {
                        $cell.text(oldVal).removeClass('editing');
                    }
                }
            });
        });

        // Active toggle
        $(document).on('change', '.module-active-checkbox', function() {
            var $checkbox = $(this);
            var id = $checkbox.data('id');
            var active = $checkbox.is(':checked') ? 1 : 0;
            $checkbox.prop('disabled', true);
            $.ajax({
                url: 'index.php?r=users/togglemoduleactive',
                method: 'POST',
                data: {
                    id: id,
                    is_active: active
                },
                dataType: 'json',
                success: function(resp) {
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert(resp.message || 'Module status updated!', resp.success ?
                            'success' : 'error');
                    } else {
                        Swal.fire(resp.success ? 'Success' : 'Error', resp.message ||
                            'Module status updated!', resp.success ? 'success' : 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update module status.', 'error');
                },
                complete: function() {
                    $checkbox.prop('disabled', false);
                }
            });
        });

        // Status change handler
        $(document).on('change', '.module-status-select', function() {
            var $select = $(this);
            var id = $select.data('id');
            var status = $select.val();
            $select.prop('disabled', true);
            $.ajax({
                url: 'index.php?r=users/updatemodulestatus',
                method: 'POST',
                data: {
                    id: id,
                    current_status: status
                },
                dataType: 'json',
                success: function(resp) {
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert(resp.message || 'Module status updated!', resp.success ?
                            'success' : 'error');
                    } else {
                        Swal.fire(resp.success ? 'Success' : 'Error', resp.message ||
                            'Module status updated!', resp.success ? 'success' : 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update module status.', 'error');
                },
                complete: function() {
                    $select.prop('disabled', false);
                }
            });
        });

        // Add Module Modal logic
        $('#addModuleBtn').on('click', function() {
            $('#addModuleModal').fadeIn(150);
        });
        $('#closeAddModuleModal').on('click', function() {
            $('#addModuleModal').fadeOut(100);
        });
        $('#addModuleType').on('change', function() {
            if ($(this).val() === 'child') {
                $('#parentSelectWrapper').show();
            } else {
                $('#parentSelectWrapper').hide();
            }
        });

        // Color picker synchronization in Add Module Modal
        $('input[name="color"]').on('input', function() {
            $('input[name="color_text"]').val($(this).val());
            updateIconPreview();
        });

        $('input[name="color_text"]').on('input', function() {
            var colorVal = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(colorVal)) {
                $('input[name="color"]').val(colorVal);
                updateIconPreview();
            }
        });

        // Icon preview update in Add Module Modal
        $('input[name="icon"]').on('input', function() {
            updateIconPreview();
        });

        function updateIconPreview() {
            var iconClass = $('input[name="icon"]').val() || 'fas fa-cube';
            var iconColor = $('input[name="color_text"]').val() || '#646cff';
            $('#iconPreview').attr('class', iconClass).css('color', iconColor);
        }

        // Add Module Form submit
        $('#addModuleForm').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serializeArray();
            var postData = {};
            data.forEach(function(item) {
                if (item.name === 'color_text') {
                    postData['color'] = item.value;
                } else if (item.name !== 'color') {
                    postData[item.name] = item.value;
                }
            });
            if (postData.type === 'parent') postData.parent_id = '';
            $.ajax({
                url: 'index.php?r=users/addmodule',
                method: 'POST',
                data: postData,
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        showGlobalAlert(resp.message || 'Module added!', resp.success ?
                            'success' : 'error');
                        $('#addModuleModal').fadeOut(100);
                        location.reload();
                    } else {
                        showGlobalAlert(resp.message || 'Failed to add module.', 'error');
                    }
                },
                error: function() {
                    showGlobalAlert('Failed to add module.', 'error');
                }
            });
        });
    });

    function applyFilters() {
        var searchVal = $('#moduleSearch').val().toLowerCase();

        $('#modulesTable tbody tr').each(function() {
            var $row = $(this);
            var name = $row.data('name') || '';
            var desc = $row.data('desc') || '';
            var link = $row.data('link') || '';

            var matchesSearch = !searchVal ||
                name.includes(searchVal) ||
                desc.includes(searchVal) ||
                link.includes(searchVal);

            if (matchesSearch) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }

    function clearFilters() {
        $('#moduleSearch').val('');
        applyFilters();
    }
</script>

<style>
    .system-modules-management .summary-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .system-modules-management .summary-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .system-modules-management .table tbody tr:hover {
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .system-modules-management .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    /* Fixed table header styles */
    .system-modules-management .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc;
    }

    .system-modules-management .table thead th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 10;
    }

    /* Scrollable table container */
    .table-scroll-wrapper {
        overflow: hidden;
    }

    .table-scroll-wrapper .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar {
        width: 6px;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 3px;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }

    .table-scroll-wrapper .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Module row styling */
    .parent-row {
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .submodule-row {
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .submodule-row:last-child {
        border-bottom: none;
    }

    /* Editable cell styles */
    .editable-cell {
        cursor: pointer;
        background: #f7faff;
        transition: background 0.2s;
        border-radius: 3px;
        position: relative;
    }

    .editable-cell:hover {
        background: #e0e7ff;
    }

    .edit-input-field {
        width: 100%;
        padding: 4px 6px;
        border: 1.5px solid #a3a3ff;
        border-radius: 4px;
        font-size: 0.7rem;
        box-shadow: 0 1px 4px rgba(100, 108, 255, 0.08);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        color: #232360;
    }

    .edit-input-field:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px #e0e6f7;
        background: #fff;
    }

    /* Color display styles */
    .color-display {
        cursor: pointer;
    }

    .color-preview {
        transition: transform 0.2s;
    }

    .color-display:hover .color-preview {
        transform: scale(1.1);
    }

    .edit-color-picker {
        cursor: pointer;
    }

    .edit-color-text {
        font-family: monospace;
        font-size: 0.65rem;
    }

    /* Icon display styles */
    .icon-display {
        cursor: pointer;
    }

    .icon-display i {
        transition: transform 0.2s;
    }

    .icon-display:hover i {
        transform: scale(1.2);
    }

    .edit-icon-text {
        font-family: monospace;
        font-size: 0.65rem;
    }

    /* Checkbox styles */
    .module-active-checkbox {
        transform: scale(0.8);
        cursor: pointer;
    }

    /* Select styles */
    .module-status-select {
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .module-status-select:focus {
        border-color: #6366f1;
        outline: none;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .system-modules-management .modules-summary .col-md-3 {
            margin-bottom: 8px;
        }

        .system-modules-management .search-section .row>div {
            margin-bottom: 8px;
        }

        .system-modules-management .table-responsive {
            font-size: 0.7rem;
        }

        .modules-table-container {
            height: 65vh !important;
        }
    }

    @media (max-width: 576px) {
        .modules-table-container {
            height: 60vh !important;
        }

        .system-modules-management .table thead th,
        .system-modules-management .table tbody td {
            padding: 4px !important;
            font-size: 0.7rem !important;
        }

        .system-modules-management .summary-card {
            padding: 6px !important;
        }

        .system-modules-management .summary-card div {
            font-size: 1rem !important;
        }
    }
</style>