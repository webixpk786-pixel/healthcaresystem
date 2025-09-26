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

<div class="modules-management" style="min-height: 95vh; margin: -25px;">
    <!-- Header Section -->
    <div class="page-header" style="margin-bottom: 16px;">
        <div class="row">
            <div class="col-md-7">
                <h1 style="color: #1e293b; font-size: 1.5rem; font-weight: 700; margin-bottom: 4px;">
                    <i class="fas fa-cogs" style="color: #6366f1; margin-right: 8px;"></i>
                    Modules & Permissions
                </h1>
                <p style="color: #64748b; font-size: 0.85rem; margin: 0;">
                    Manage system modules, configure permissions, and control access for different roles
                </p>
            </div>
            <div class="col-md-5 text-right">
                <div class="header-actions" style="display: flex; gap: 6px; justify-content: flex-end;">
                    <button class="btn btn-sm btn-outline-secondary" onclick="refreshModules()"
                        style="display: flex;align-items: center;gap: 6px; border: 1px solid #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #64748b; margin-right: 6px;">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="exportModules()"
                        style="border: 1px solid #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; background: white; color: #64748b;">
                        <i class="fas fa-download"></i> Export
                    </button>
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
                        How to Use This Page
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul style="margin: 0; padding-left: 16px; color: #64748b; font-size: 0.75rem;">
                                <li style="margin-bottom: 3px;">Each row represents a <strong>module</strong> or
                                    <strong>submodule</strong> in the system.
                                </li>
                                <li style="margin-bottom: 3px;">Use the <strong>View, Create, Edit, Delete,
                                        Export</strong> checkboxes to grant or revoke permissions.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul style="margin: 0; padding-left: 16px; color: #64748b; font-size: 0.75rem;">
                                <li style="margin-bottom: 3px;">The <strong>Active</strong> toggle enables or disables
                                    the module for all users.</li>
                                <li style="margin-bottom: 3px;">Click the module's <strong>link</strong> to open its
                                    page (if available).</li>
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
                <div class="col-md-8">
                    <div class="form-group mb-0">
                        <label for="moduleSearch"
                            style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Search
                            Modules</label>
                        <input type="text" class="form-control" id="moduleSearch"
                            placeholder="Search modules by name, description, or link..."
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; height: 28px;">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label for="filterStatus"
                            style="font-weight: 500; color: #374151; margin-bottom: 2px; font-size: 0.7rem;">Status</label>
                        <select class="form-control" id="filterStatus"
                            style="border: 1px solid #d1d5db; border-radius: 4px; padding: 4px 6px; font-size: 0.7rem; height: 28px;">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
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
                                Module</th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                View</th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Create</th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Edit</th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Delete</th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Export</th>
                            <th
                                style="padding: 6px; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Active</th>
                            <th
                                style="padding: 6px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Link</th>
                            <th
                                style="padding: 6px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Description</th>
                            <th
                                style="padding: 6px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Created</th>
                            <th
                                style="padding: 6px; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.75rem;">
                                Updated</th>
                        </tr>
                    </thead>
                    <tbody id="modulesTableBody">
                        <?php
                        $permTypes = [
                            'can_view' => 'View',
                            'can_create' => 'Create',
                            'can_edit' => 'Edit',
                            'can_delete' => 'Delete',
                            'can_export' => 'Export',
                        ];
                        $parents = array_filter($modules, fn($m) => !$m['parent_id']);
                        $children = array_filter($modules, fn($m) => $m['parent_id']);
                        foreach ($parents as $parent):
                            $perm = $permissions[$parent['id']] ?? [];
                            $parentRestricted = !$canEdit;
                        ?>
                            <tr class="parent-row" data-name="<?= htmlspecialchars(strtolower($parent['name'])) ?>"
                                data-desc="<?= htmlspecialchars(strtolower($parent['description'] ?? '')) ?>"
                                data-link="<?= htmlspecialchars(strtolower($parent['link'] ?? '')) ?>"
                                data-status="<?= $parent['is_active'] ? 'active' : 'inactive' ?>">
                                <td style="padding: 6px; font-weight: 500; color: #1e293b; font-size: 0.75rem;">
                                    <div style="display: flex; align-items: center;">
                                        <div
                                            style="width: 6px; height: 6px; background: #6366f1; border-radius: 50%; margin-right: 6px;">
                                        </div>
                                        <?= htmlspecialchars($parent['name']) ?>
                                    </div>
                                </td>
                                <?php foreach ($permTypes as $permKey => $permLabel): ?>
                                    <td style="padding: 6px; text-align: center;">
                                        <label class="permission-switch" title="<?= $permLabel ?>">
                                            <input type="checkbox" class="module-perm-checkbox"
                                                data-module="<?= $parent['id'] ?>" data-perm="<?= $permKey ?>"
                                                <?= !empty($perm[$permKey]) ? 'checked' : '' ?>
                                                <?= $canEdit ? '' : 'disabled' ?> />
                                            <span class="permission-slider"></span>
                                            <span class="permission-tooltip"><?= $permLabel ?></span>
                                        </label>
                                    </td>
                                <?php endforeach; ?>
                                <td style="padding: 6px; text-align: center;">
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="module-active-checkbox" data-id="<?= $parent['id'] ?>"
                                            <?= $parent['is_active'] ? 'checked' : '' ?>
                                            <?= $canEdit ? '' : 'disabled' ?> />
                                        <span class="toggle-slider"></span>
                                    </label>
                                </td>
                                <td style="padding: 6px; font-size: 0.7rem;">
                                    <?= !empty($parent['link']) ? '<a href="index.php?r=' . htmlspecialchars($parent['link']) . '/dashboard" target="_blank" style="color: #6366f1; text-decoration: none; font-weight: 500;">/' . htmlspecialchars($parent['link']) . '</a>' : '<span style="color: #9ca3af;">-</span>' ?>
                                </td>
                                <td style="padding: 6px; color: #64748b; font-size: 0.7rem;">
                                    <?= htmlspecialchars($parent['description']) ?>
                                </td>
                                <td style="padding: 6px; color: #64748b; font-size: 0.7rem;">
                                    <?= htmlspecialchars(date('Y-m-d', strtotime($parent['created_at']))) ?>
                                </td>
                                <td style="padding: 6px; color: #64748b; font-size: 0.7rem;">
                                    <?= htmlspecialchars(date('Y-m-d', strtotime($parent['updated_at']))) ?>
                                </td>
                            </tr>
                            <?php
                            $childList = array_filter($children, fn($c) => $c['parent_id'] == $parent['id']);
                            foreach ($childList as $child):
                                $perm = $permissions[$child['id']] ?? [];
                                $childRestricted = $parentRestricted ? true : !$canEdit;
                            ?>
                                <tr class="submodule-row" data-name="<?= htmlspecialchars(strtolower($child['name'])) ?>"
                                    data-desc="<?= htmlspecialchars(strtolower($child['description'] ?? '')) ?>"
                                    data-link="<?= htmlspecialchars(strtolower($child['link'] ?? '')) ?>"
                                    data-status="<?= $child['is_active'] ? 'active' : 'inactive' ?>">
                                    <td style="padding: 6px; font-weight: 500; color: #1e293b; font-size: 0.75rem;">
                                        <div style="display: flex; align-items: center; padding-left: 16px;">
                                            <span style="color: #8b5cf6; margin-right: 6px; font-size: 0.6rem;">&#9679;</span>
                                            <?= htmlspecialchars($child['name']) ?>
                                        </div>
                                    </td>
                                    <?php foreach ($permTypes as $permKey => $permLabel): ?>
                                        <td style="padding: 6px; text-align: center;">
                                            <label class="permission-switch" title="<?= $permLabel ?>">
                                                <input type="checkbox" class="module-perm-checkbox"
                                                    data-module="<?= $child['id'] ?>" data-perm="<?= $permKey ?>"
                                                    <?= !empty($perm[$permKey]) ? 'checked' : '' ?>
                                                    <?= $childRestricted ? 'disabled' : '' ?> />
                                                <span class="permission-slider"></span>
                                                <span class="permission-tooltip"><?= $permLabel ?></span>
                                            </label>
                                        </td>
                                    <?php endforeach; ?>
                                    <td style="padding: 6px; text-align: center;">
                                        <label class="toggle-switch">
                                            <input type="checkbox" class="module-active-checkbox" data-id="<?= $child['id'] ?>"
                                                <?= $child['is_active'] ? 'checked' : '' ?>
                                                <?= $childRestricted ? 'disabled' : '' ?> />
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                    <td style="padding: 6px; font-size: 0.7rem;">
                                        <?= !empty($child['link']) ?
                                            '<a href="index.php?r=' . htmlspecialchars($parent['link']) . '/' . htmlspecialchars($child['link']) . '" target="_blank" style="color: #6366f1; text-decoration: none; font-weight: 500;">
                                            ' . htmlspecialchars($parent['link']) . '/' . htmlspecialchars($child['link'])  .
                                            '</a>' : '<span style="color: #9ca3af;">-</span>' ?>
                                    </td>
                                    <td style="padding: 6px; color: #64748b; font-size: 0.7rem;">
                                        <?= htmlspecialchars($child['description']) ?>
                                    </td>
                                    <td style="padding: 6px; color: #64748b; font-size: 0.7rem;">
                                        <?= htmlspecialchars(date('Y-m-d', strtotime($child['created_at']))) ?>
                                    </td>
                                    <td style="padding: 6px; color: #64748b; font-size: 0.7rem;">
                                        <?= htmlspecialchars(date('Y-m-d', strtotime($child['updated_at']))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .modules-management .summary-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .modules-management .summary-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .modules-management .table tbody tr:hover {
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .modules-management .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    /* Fixed table header styles */
    .modules-management .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc;
    }

    .modules-management .table thead th {
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

    /* Permission switch styles - Compact */
    .permission-switch {
        position: relative;
        display: inline-block;
        width: 36px;
        height: 20px;
    }

    .permission-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .permission-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        transition: .4s;
        border-radius: 20px;
    }

    .permission-slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .permission-switch input:checked+.permission-slider {
        background-color: #10b981;
    }

    .permission-switch input:focus+.permission-slider {
        box-shadow: 0 0 1px #10b981;
    }

    .permission-switch input:checked+.permission-slider:before {
        transform: translateX(16px);
    }

    .permission-switch input:disabled+.permission-slider {
        background-color: #e2e8f0;
        cursor: not-allowed;
    }

    .permission-switch input:disabled+.permission-slider:before {
        background-color: #f1f5f9;
    }

    /* Toggle switch styles - Compact */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 36px;
        height: 20px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        transition: .4s;
        border-radius: 20px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch input:checked+.toggle-slider {
        background-color: #6366f1;
    }

    .toggle-switch input:focus+.toggle-slider {
        box-shadow: 0 0 1px #6366f1;
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(16px);
    }

    .toggle-switch input:disabled+.toggle-slider {
        background-color: #e2e8f0;
        cursor: not-allowed;
    }

    .toggle-switch input:disabled+.toggle-slider:before {
        background-color: #f1f5f9;
    }

    /* Tooltip styles - Compact */
    .permission-tooltip {
        visibility: hidden;
        width: 50px;
        background-color: #1e293b;
        color: #fff;
        text-align: center;
        border-radius: 4px;
        padding: 3px 6px;
        position: absolute;
        z-index: 20;
        bottom: 125%;
        left: 50%;
        margin-left: -25px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 10px;
        pointer-events: none;
    }

    .permission-switch:hover .permission-tooltip {
        visibility: visible;
        opacity: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modules-management .modules-summary .col-md-3 {
            margin-bottom: 8px;
        }

        .modules-management .search-section .row>div {
            margin-bottom: 8px;
        }

        .modules-management .table-responsive {
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

        .modules-management .table thead th,
        .modules-management .table tbody td {
            padding: 4px !important;
            font-size: 0.7rem !important;
        }
    }
</style>

<script>
    $(function() {
        // Search filter
        $('#moduleSearch').on('input', function() {
            applyFilters();
        });

        // Status filter
        $('#filterStatus').on('change', function() {
            applyFilters();
        });

        // Active toggle (modern switch)
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
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert('Failed to update module status.', 'error');
                    } else {
                        Swal.fire('Error', 'Failed to update module status.', 'error');
                    }
                },
                complete: function() {
                    $checkbox.prop('disabled', false);
                }
            });
        });

        // Permission toggle for module
        $(document).on('change', '.module-perm-checkbox', function() {
            var $cb = $(this);
            var moduleId = $cb.data('module');
            var permType = $cb.data('perm');
            var checked = $cb.is(':checked') ? 1 : 0;
            var roleId = <?= (int)$role['id'] ?>;
            $cb.prop('disabled', true);
            var data = {
                module_id: moduleId,
                role_id: roleId,
                perm_type: permType,
                value: checked
            };
            $.ajax({
                url: 'index.php?r=users/updatemodulepermission',
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(resp) {
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert(resp.message || 'Permission updated!', resp.success ?
                            'success' : 'error');
                    } else {
                        Swal.fire(resp.success ? 'Success' : 'Error', resp.message ||
                            'Permission updated!', resp.success ? 'success' : 'error');
                    }
                },
                error: function() {
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert('Failed to update permission.', 'error');
                    } else {
                        Swal.fire('Error', 'Failed to update permission.', 'error');
                    }
                },
                complete: function() {
                    $cb.prop('disabled', false);
                }
            });
        });
    });

    function applyFilters() {
        var searchVal = $('#moduleSearch').val().toLowerCase();
        var statusVal = $('#filterStatus').val();

        $('#modulesTable tbody tr').each(function() {
            var $row = $(this);
            var name = $row.data('name') || '';
            var desc = $row.data('desc') || '';
            var link = $row.data('link') || '';
            var status = $row.data('status') || '';

            var matchesSearch = !searchVal ||
                name.includes(searchVal) ||
                desc.includes(searchVal) ||
                link.includes(searchVal);

            var matchesStatus = !statusVal || status === statusVal;

            if (matchesSearch && matchesStatus) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }

    function clearFilters() {
        $('#moduleSearch').val('');
        $('#filterStatus').val('');
        applyFilters();
    }

    function refreshModules() {
        location.reload();
    }

    function exportModules() {
        // Create CSV content
        let csvContent = "Module,View,Create,Edit,Delete,Export,Active,Link,Description,Created,Updated\n";

        $('#modulesTable tbody tr:visible').each(function() {
            var $row = $(this);
            var moduleName = $row.find('td:first').text().trim();
            var link = $row.find('td:nth-child(8)').text().trim();
            var description = $row.find('td:nth-child(9)').text().trim();
            var created = $row.find('td:nth-child(10)').text().trim();
            var updated = $row.find('td:nth-child(11)').text().trim();

            var view = $row.find('input[data-perm="can_view"]').is(':checked') ? 'Yes' : 'No';
            var create = $row.find('input[data-perm="can_create"]').is(':checked') ? 'Yes' : 'No';
            var edit = $row.find('input[data-perm="can_edit"]').is(':checked') ? 'Yes' : 'No';
            var delete_perm = $row.find('input[data-perm="can_delete"]').is(':checked') ? 'Yes' : 'No';
            var export_perm = $row.find('input[data-perm="can_export"]').is(':checked') ? 'Yes' : 'No';
            var active = $row.find('input.module-active-checkbox').is(':checked') ? 'Yes' : 'No';

            csvContent +=
                `"${moduleName}","${view}","${create}","${edit}","${delete_perm}","${export_perm}","${active}","${link}","${description}","${created}","${updated}"\n`;
        });

        // Download CSV
        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'modules_permissions.csv';
        a.click();
        window.URL.revokeObjectURL(url);

        if (typeof showGlobalAlert === 'function') {
            showGlobalAlert('Modules exported successfully', 'success');
        } else {
            Swal.fire('Success', 'Modules exported successfully', 'success');
        }
    }
</script>