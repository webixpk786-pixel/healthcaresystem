<?php

/** @var $modules array */
?>
<style>
    .module-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(370px, 1fr));
        gap: 24px;
        margin-top: 32px;
    }

    .module-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(100, 108, 255, 0.08);
        border: 1px solid #f0f0f7;
        padding: 28px 24px 18px 24px;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s;
        margin-bottom: 0;
        position: relative;
    }

    .module-card:hover {
        box-shadow: 0 4px 24px rgba(100, 108, 255, 0.16);
    }

    .module-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .module-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--mod-color, #e6eaff);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #646cff;
        font-size: 2rem;
        font-weight: 700;
        margin-right: 18px;
        box-shadow: 0 2px 8px #646cff11;
    }

    .module-title {
        font-size: 1.18rem;
        font-weight: 700;
        color: #232360;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .module-link {
        color: #646cff;
        font-size: 13px;
        text-decoration: underline;
        margin-bottom: 4px;
        display: inline-block;
    }

    .module-desc {
        color: #888;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .module-meta {
        color: #aaa;
        font-size: 12px;
        margin-bottom: 2px;
    }

    .module-active-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }

    .module-active-toggle input[type=checkbox] {
        width: 22px;
        height: 22px;
        accent-color: #646cff;
    }

    .child-modules-list {
        margin-top: 18px;
        padding-left: 12px;
        border-left: 3px solid #f0f0f7;
    }

    .child-module-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #232360;
        margin-bottom: 2px;
    }

    .child-module-meta {
        color: #aaa;
        font-size: 12px;
        margin-bottom: 2px;
    }

    .child-module-desc {
        color: #888;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .permissions-section {
        margin-top: 18px;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .permissions-table {
        width: 100%;
        border-collapse: collapse;
    }

    .permissions-table th,
    .permissions-table td {
        padding: 8px 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .permissions-table th {
        background-color: #f0f0f0;
        font-weight: 600;
        color: #333;
    }

    .permissions-table input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: #646cff;
    }

    .save-perms-btn {
        background: #646cff;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 15px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: rgba(100, 108, 255, 0.25) 0px 2px 8px;
        transition: 0.2s;
    }

    .save-perms-btn:hover {
        background: #535abd;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        vertical-align: middle;
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
        background: #ccc;
        border-radius: 24px;
        transition: background 0.2s;
    }

    .toggle-switch input:checked+.toggle-slider {
        background: #646cff;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 1px 4px #646cff22;
        transition: transform 0.2s;
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(20px);
    }

    .toggle-label {
        font-size: 13px;
        color: #444;
        margin-left: 10px;
        user-select: none;
    }

    .permission-switch {
        display: inline-block;
        position: relative;
        width: 38px;
        height: 22px;
        margin: 0 6px;
        vertical-align: middle;
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
        background: #e0e0e0;
        border-radius: 22px;
        transition: background 0.2s;
    }

    .permission-switch input:checked+.permission-slider {
        background: #646cff;
    }

    .permission-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 1px 4px #646cff22;
        transition: transform 0.2s;
    }

    .permission-switch input:checked+.permission-slider:before {
        transform: translateX(16px);
    }

    .permission-tooltip {
        visibility: hidden;
        background: #232360;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 4px 10px;
        position: absolute;
        z-index: 10;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.2s;
        font-size: 12px;
        pointer-events: none;
        white-space: nowrap;
    }

    .permission-switch:hover .permission-tooltip {
        visibility: visible;
        opacity: 1;
    }

    .permission-labels {
        display: flex;
        gap: 8px;
        margin-top: 4px;
    }

    @media (max-width: 700px) {
        .module-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div style="position: fixed; inset: 50px 0px 0px 230px; overflow: auto;">
    <div style="padding: 30px; width: 100%; min-height: 100%;">
        <div style="margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <h1 style="color: rgb(35, 35, 96); margin: 0px; font-size: 28px; font-weight: 700;">Modules</h1>
                </div>
                <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap; margin-bottom: 16px;">
                    <input id="moduleSearch" placeholder="Search modules by name, description, or link..." type="text"
                        value=""
                        style="flex: 1 1 0%; min-width: 300px; width: 100%; padding: 12px 12px 12px 16px; border: 1px solid rgb(224, 224, 224); border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;">
                </div>
            </div>
        </div>
        <div class="modules-scroll-area" style="max-height: 70vh; overflow-y: auto; padding-right: 8px;">
            <div class="module-cards-grid">
                <?php
                // Group modules by parent_id
                $parents = array_filter($modules, fn($m) => !$m['parent_id']);
                $children = array_filter($modules, fn($m) => $m['parent_id']);
                foreach ($parents as $parent):
                ?>
                    <div class="module-card" data-module-id="<?= $parent['id'] ?>"
                        data-name="<?= htmlspecialchars(strtolower($parent['name'])) ?>"
                        data-desc="<?= htmlspecialchars(strtolower($parent['description'] ?? '')) ?>"
                        data-link="<?= htmlspecialchars(strtolower($parent['link'] ?? '')) ?>">
                        <div class="module-header-row">
                            <div style="display:flex; align-items:center;">
                                <div class="module-icon"
                                    style="background: <?= htmlspecialchars($parent['color'] ?? '#e6eaff') ?>;">
                                    <?php if (!empty($parent['icon'])): ?>
                                        <i class="<?= htmlspecialchars($parent['icon']) ?>"></i>
                                    <?php else: ?>
                                        <?= strtoupper(substr($parent['name'], 0, 2)) ?>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 18px;">
                                    <div>
                                        <div class="module-title" style="display: flex; align-items: center; gap: 18px;">
                                            <?= htmlspecialchars($parent['name']) ?>
                                            <div class="permission-labels" style="margin: 0;">
                                                <?php
                                                $permTypes = [
                                                    'can_view' => 'View',
                                                    'can_create' => 'Create',
                                                    'can_edit' => 'Edit',
                                                    'can_delete' => 'Delete',
                                                ];
                                                $perm = $permissions[$parent['id']] ?? [];
                                                // print_r($perm);
                                                foreach ($permTypes as $permKey => $permLabel):
                                                ?>
                                                    <label class="permission-switch" title="<?= $permLabel ?>">
                                                        <input type="checkbox" class="module-perm-checkbox"
                                                            data-module="<?= $perm['module_id'] ?>" data-perm="<?= $permKey ?>"
                                                            <?= !empty($perm[$permKey]) ? 'checked' : '' ?> />
                                                        <span class="permission-slider"></span>
                                                        <span class="permission-tooltip"><?= $permLabel ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($parent['link'])): ?>
                                            <a class="module-link" href="<?= htmlspecialchars($parent['link']) ?>"
                                                target="_blank">/<?= htmlspecialchars($parent['link']) ?></a>
                                        <?php endif; ?>
                                        <?php if (!empty($parent['description'])): ?>
                                            <div class="module-desc"> <?= htmlspecialchars($parent['description']) ?> </div>
                                        <?php endif; ?>
                                        <div class="module-meta">
                                            Created: <?= htmlspecialchars($parent['created_at']) ?> | Updated:
                                            <?= htmlspecialchars($parent['updated_at']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="module-active-toggle">
                                <label class="toggle-label">Active</label>
                                <label class="toggle-switch">
                                    <input type="checkbox" class="module-active-checkbox" data-id="<?= $parent['id'] ?>"
                                        <?= $parent['is_active'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <?php
                        $childList = array_filter($children, fn($c) => $c['parent_id'] == $parent['id']);
                        if ($childList): ?>
                            <div class="child-modules-list">
                                <?php foreach ($childList as $child): ?>
                                    <div style="margin-bottom: 14px;">
                                        <div class="child-module-title"> <?= htmlspecialchars($child['name']) ?> </div>
                                        <?php if (!empty($child['link'])): ?>
                                            <a class="module-link" href="<?= htmlspecialchars($child['link']) ?>"
                                                target="_blank">/<?= htmlspecialchars($child['link']) ?></a>
                                        <?php endif; ?>
                                        <?php if (!empty($child['description'])): ?>
                                            <div class="child-module-desc"> <?= htmlspecialchars($child['description']) ?> </div>
                                        <?php endif; ?>
                                        <div class="child-module-meta">
                                            Created: <?= htmlspecialchars($child['created_at']) ?> | Updated:
                                            <?= htmlspecialchars($child['updated_at']) ?>
                                        </div>
                                        <div class="module-active-toggle" style="margin-top: 4px;">
                                            <label class="toggle-label">Active</label>
                                            <label class="toggle-switch">
                                                <input type="checkbox" class="module-active-checkbox" data-id="<?= $child['id'] ?>"
                                                    <?= $child['is_active'] ? 'checked' : '' ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        // Search filter
        $('#moduleSearch').on('input', function() {
            var val = $(this).val().toLowerCase();
            $('.module-card').each(function() {
                var name = $(this).data('name');
                var desc = $(this).data('desc');
                var link = $(this).data('link');
                if (name.includes(val) || desc.includes(val) || link.includes(val)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        // Save permissions
        $('.save-perms-btn').on('click', function() {
            var moduleId = $(this).data('module');
            var perms = {};
            $(this).closest('.permissions-section').find('tr').each(function() {
                var roleId = $(this).find('td:first').text();
                if (!roleId) return;
                perms[roleId] = {
                    can_view: $(this).find('input[data-perm="can_view"]').is(':checked') ? 1 : 0,
                    can_create: $(this).find('input[data-perm="can_create"]').is(':checked') ?
                        1 : 0,
                    can_edit: $(this).find('input[data-perm="can_edit"]').is(':checked') ? 1 : 0,
                    can_delete: $(this).find('input[data-perm="can_delete"]').is(':checked') ?
                        1 : 0
                };
            });
            $.ajax({
                url: 'index.php?r=users/updatemodulepermissions',
                method: 'POST',
                data: {
                    module_id: moduleId,
                    perms: perms
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        Swal.fire('Success', resp.message || 'Permissions updated!', 'success');
                    } else {
                        Swal.fire('Error', resp.message || 'Failed to update permissions.',
                            'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update permissions.', 'error');
                }
            });
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
                    if (resp.success) {
                        if (typeof showGlobalAlert === 'function') {
                            showGlobalAlert(resp.message || 'Module status updated!',
                                'success');
                        } else {
                            Swal.fire('Success', resp.message || 'Module status updated!',
                                'success');
                        }
                    } else {
                        if (typeof showGlobalAlert === 'function') {
                            showGlobalAlert(resp.message || 'Failed to update module status.',
                                'error');
                        } else {
                            Swal.fire('Error', resp.message ||
                                'Failed to update module status.', 'error');
                        }
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
            var roleId = <?= $role['id'] ?>; // Set this variable in PHP for the current role context
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
</script>