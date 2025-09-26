<?php

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('1,3');
$canEdit = $can['canEdit'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];
?>
<div
    style="position: absolute; inset: 50px 0px 0px 230px; overflow: hidden; direction: <?= \app\components\LanguageManager::getDirection() ?>;">

    <!-- <div style="display: flex; padding: 0 3% 0 3%; gap: 16px;">

        <div style="flex:1;">
            <div style="font-size: medium;font-weight: 600;color: #232360;margin-bottom: 4px;">
                <?= \app\components\LanguageManager::translate('System Roles Management') ?></div>
            <ul style="margin: 0 0 0 18px; padding: 0; color: #444; font-size: 1rem;">
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('This page lists all <b>system roles</b> and their key details. Roles define user permissions and access levels') ?>
                </li>
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('You can <b>edit</b> role details by clicking the Edit button. Changes are saved instantly and securely') ?>
                </li>
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('The <b>Active/Inactive</b> toggle lets you enable or disable a role. Toggle the switch to update the role status') ?>
                </li>
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('Use the <b>search bar</b> to quickly filter roles by name or description') ?>
                </li>
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('All changes are logged for audit and security purposes') ?>
                </li>
            </ul>
        </div>
    </div> -->
    <div class="systemmodules-wrapper">
        <div class="systemmodules-header" style="display: flex;justify-content: space-between;align-items: baseline;">
            <?= \app\widgets\LanguageSwitcher::widget(['position' => 'bottom-left']) ?>
            <input id="moduleSearch"
                placeholder="<?= \app\components\LanguageManager::translate('Search roles by name, description...') ?>"
                type="text" style="width: 300px; margin-right: 10px;" />
            <?php if ($canAdd): ?>
                <button id="addModuleBtn"
                    style="float:right; margin-right:10px; background:#646cff; color:#fff; border:none; border-radius:6px; padding:7px 18px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.2s;">+
                    <?= \app\components\LanguageManager::translate('Add New') ?></button>
            <?php endif; ?>
        </div>

        <div class="roles-grid-container" style="height: 75vh; overflow-y: auto;">

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px;">
                <?php foreach ($roles as $role): ?>
                    <div class="role-card" data-role-id="<?= $role['id'] ?>"
                        style="background: white; border-radius: 12px; padding: 24px; box-shadow: rgba(100, 108, 255, 0.1) 0px 2px 12px; border: 1px solid rgb(240, 240, 247); transition: 0.2s; transform: translateY(0px);">
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 16px; background: transparent;">
                                <div
                                    style="width: 60px; height: 60px; border-radius: 50%; background: rgba(100, 108, 255, 0.133); border: none; display: flex; align-items: center; justify-content: center; color: rgb(100, 108, 255); font-weight: 700; font-size: 24px; position: relative; overflow: hidden;">
                                    <?= strtoupper(substr($role['name'], 0, 2)) ?>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <h3 class="role-name" data-editable="false"
                                            style="margin: 0px; color: rgb(35, 35, 96); font-size: 18px; font-weight: 600; text-align: left;">
                                            <?= htmlspecialchars($role['name']) ?>
                                        </h3>

                                        <input class="role-name-input" type="text"
                                            value="<?= htmlspecialchars($role['name']) ?>"
                                            style="display:none; font-size:18px; font-weight:600; color:rgb(35,35,96); border:1px solid #ccc; border-radius:4px; padding:2px 8px;" />

                                        <span
                                            style="display: flex; align-items: center; margin-left: 8px; cursor: pointer; user-select: none;">
                                            <?php if ($canEdit): ?>
                                                <span class="role-toggle-active"
                                                    style="width: 36px; height: 20px; border-radius: 12px; background: <?= $role['status'] ? 'rgb(100, 108, 255)' : '#ccc' ?>; display: inline-block; position: relative; transition: background 0.2s; margin-right: 6px; cursor:pointer;">
                                                    <span
                                                        style="position: absolute; left: <?= $role['status'] ? '18px' : '2px' ?>; top: 2px; width: 16px; height: 16px; border-radius: 50%; background: rgb(255, 255, 255); box-shadow: rgba(0, 0, 0, 0.08) 0px 1px 4px; transition: left 0.2s;"></span>
                                                </span>
                                            <?php endif; ?>
                                            <span
                                                style="margin-left: 2px; font-size: 12px; color: <?= $role['status'] ? 'rgb(46, 125, 50)' : '#888' ?>; font-weight: 600; min-width: 54px; text-align: left;">
                                                <?= $role['status'] ? 'Active' : 'Inactive' ?>
                                            </span>

                                        </span>
                                    </div>
                                    <p class="role-desc" data-editable="false"
                                        style="margin: 4px 0px 0px; color: rgb(102, 102, 102); font-size: 14px; text-align: left;">
                                        <?= htmlspecialchars($role['description']) ?>
                                    </p>
                                    <input class="role-desc-input" type="text"
                                        value="<?= htmlspecialchars($role['description']) ?>"
                                        style="display:none; font-size:14px; color:rgb(102,102,102); border:1px solid #ccc; border-radius:4px; padding:2px 8px; width:100%; margin-top:4px;" />
                                </div>
                            </div>
                            <div
                                style="min-width: 75px; background: rgb(232, 245, 232); color: rgb(46, 125, 50); padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px; text-transform: capitalize; text-align: center;">
                                <?= $role['user_count'] ?? 0 ?> Users
                            </div>
                        </div>
                        <div style="display: flex;gap: 8px;margin-top: 30px;height: 35px; width: 100%;">
                            <form class="role-post-form" action="index.php?r=users/modules" method="post"
                                style="display:inline;">
                                <input type="hidden" name="role_id" value="<?= $role['id'] ?>" />
                                <button type="submit"
                                    style="flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(224, 224, 224); border-radius: 6px; background: white; color: rgb(35, 35, 96); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;">Module</button>
                            </form>
                            <form class="role-post-form" action="index.php?r=users/reports" method="post"
                                style="display:inline;">
                                <input type="hidden" name="role_id" value="<?= $role['id'] ?>" />
                                <button type="submit"
                                    style="flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(224, 224, 224); border-radius: 6px; background: white; color: rgb(35, 35, 96); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;">Report</button>
                            </form>
                            <?php if ($canEdit): ?>
                                <button class="role-edit-btn"
                                    style="width: 25%;flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(224, 224, 224); border-radius: 6px; background: white; color: rgb(35, 35, 96); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;">
                                    <span class="edit-label">Edit</span>
                                    <span class="save-label" style="display:none;">Save</span>
                                </button>
                            <?php endif; ?>
                            <?php if ($canDelete): ?>

                                <button class="role-delete-btn"
                                    style="width: 25%;flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(255, 235, 238); border-radius: 6px; background: white; color: rgb(211, 47, 47); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;">Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        // Search functionality for roles
        $('#moduleSearch').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.role-card').each(function() {
                var $card = $(this);
                var roleName = $card.find('.role-name').text().toLowerCase();
                var roleDesc = $card.find('.role-desc').text().toLowerCase();

                if (roleName.includes(searchTerm) || roleDesc.includes(searchTerm)) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        });

        // POST redirect for Module/Report
        $('.role-post-form').on('submit', function(e) {
            // Allow normal POST submit (not AJAX)
        });

        // Edit/Save toggle
        $('.role-edit-btn').on('click', function() {
            var card = $(this).closest('.role-card');
            var isEditing = card.data('editing') === true;
            if (!isEditing) {
                card.data('editing', true);
                card.find('.role-name').hide();
                card.find('.role-desc').hide();
                card.find('.role-name-input').show().focus();
                card.find('.role-desc-input').show();
                $(this).find('.edit-label').hide();
                $(this).find('.save-label').show();
            } else {
                // Save via AJAX
                var roleId = card.data('role-id');
                var newName = card.find('.role-name-input').val();
                var newDesc = card.find('.role-desc-input').val();
                var btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: 'index.php?r=users/updaterole',
                    method: 'POST',
                    data: {
                        id: roleId,
                        name: newName,
                        description: newDesc
                    },
                    dataType: 'json',
                    success: function(resp) {
                        card.find('.role-name').text(newName).show();
                        card.find('.role-desc').text(newDesc).show();
                        card.find('.role-name-input').hide();
                        card.find('.role-desc-input').hide();
                        card.data('editing', false);
                        btn.find('.edit-label').show();
                        btn.find('.save-label').hide();
                        if (resp.success) {
                            showGlobalAlert('Role updated successfully!');
                        } else {
                            showGlobalAlert(resp.message || 'Failed to update role.');
                        }
                    },
                    error: function() {
                        showGlobalAlert('Error updating role.');
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            }
        });

        // Delete with confirmation
        $('.role-delete-btn').on('click', function() {
            var card = $(this).closest('.role-card');
            var roleId = card.data('role-id');
            showGlobalConfirm('Are you sure you want to delete this role?', function() {
                $.ajax({
                    url: 'index.php?r=users/deleterole',
                    method: 'POST',
                    data: {
                        id: roleId
                    },
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.success) {
                            card.fadeOut(300, function() {
                                $(this).remove();
                            });
                            showGlobalAlert('Role deleted successfully!');
                        } else {
                            showGlobalAlert(resp.message ||
                                'Failed to delete role.');
                        }
                    },
                    error: function() {
                        showGlobalAlert('Error deleting role.');
                    }
                });
            });
        });

        // Toggle Active/Inactive
        $('.role-toggle-active').on('click', function() {
            var card = $(this).closest('.role-card');
            var roleId = card.data('role-id');
            var isActive = $(this).css('background-color') === 'rgb(100, 108, 255)';
            var newStatus = isActive ? 0 : 1;
            var toggle = $(this);
            $.ajax({
                url: 'index.php?r=users/toggleroleactive',
                method: 'POST',
                data: {
                    id: roleId,
                    active: newStatus
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        // Update UI
                        if (newStatus) {
                            toggle.css('background', 'rgb(100, 108, 255)');
                            toggle.next('span').css('color', 'rgb(46, 125, 50)').text(
                                'Active');
                            toggle.find('span').css('left', '18px');
                        } else {
                            toggle.css('background', '#ccc');
                            toggle.next('span').css('color', '#888').text('Inactive');
                            toggle.find('span').css('left', '2px');
                        }
                        showGlobalAlert('Role status updated!');
                    } else {
                        showGlobalAlert(resp.message || 'Failed to update status.');
                    }
                },
                error: function() {
                    showGlobalAlert('Error updating status.');
                }
            });
        });

        // Add Role (Add New button)
        var addRoleFormHtml = `
    <div class="role-card add-role-card" style="background: #f7f8ff; border: 2px dashed #646cff; margin-bottom: 18px;">
      <div style="display: flex; align-items: flex-start; gap: 16px;">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(100, 108, 255, 0.133); display: flex; align-items: center; justify-content: center; color: #646cff; font-weight: 700; font-size: 24px;">+</div>
        <div style="flex:1;">
          <input type="text" class="add-role-name" placeholder="Role Name" style="width:100%; font-size:18px; font-weight:600; color:rgb(35,35,96); border:1px solid #ccc; border-radius:4px; padding:6px 10px; margin-bottom:8px;" />
          <input type="text" class="add-role-desc" placeholder="Description" style="width:100%; font-size:14px; color:rgb(102,102,102); border:1px solid #ccc; border-radius:4px; padding:6px 10px;" />
        </div>
        <div style="display:flex; flex-direction:column; gap:8px;">
          <button class="add-role-save" style="background:#646cff; color:white; border:none; border-radius:6px; padding:8px 18px; font-weight:600;">Save</button>
          <button class="add-role-cancel" style="background:#eee; color:#333; border:none; border-radius:6px; padding:8px 18px; font-weight:600;">Cancel</button>
        </div>
      </div>
    </div>
  `;
        var $rolesGrid = $('div[style*="grid-template-columns"]');
        var addRoleActive = false;
        $(document).on('click', 'button:contains("Add New")', function() {
            if (addRoleActive) return;
            $rolesGrid.prepend(addRoleFormHtml);
            addRoleActive = true;
        });
        $(document).on('click', '.add-role-cancel', function() {
            $(this).closest('.add-role-card').remove();
            addRoleActive = false;
        });
        $(document).on('click', '.add-role-save', function() {
            var $card = $(this).closest('.add-role-card');
            var name = $card.find('.add-role-name').val().trim();
            var desc = $card.find('.add-role-desc').val().trim();
            if (!name) {
                showGlobalAlert('Role name is required.', 'warning');
                return;
            }
            $(this).prop('disabled', true);
            $.ajax({
                url: 'index.php?r=users/addrole',
                method: 'POST',
                data: {
                    name: name,
                    description: desc
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success && resp.role) {
                        // Build new card HTML (reuse existing card structure)
                        var role = resp.role;
                        var cardHtml = `<div class="role-card" data-role-id="${role.id}" style="background: white; border-radius: 12px; padding: 24px; box-shadow: rgba(100, 108, 255, 0.1) 0px 2px 12px; border: 1px solid rgb(240, 240, 247); transition: 0.2s; transform: translateY(0px);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 16px; background: transparent;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(100, 108, 255, 0.133); border: none; display: flex; align-items: center; justify-content: center; color: rgb(100, 108, 255); font-weight: 700; font-size: 24px; position: relative; overflow: hidden;">
                ${role.name.substring(0,2).toUpperCase()}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <h3 class="role-name" data-editable="false" style="margin: 0px; color: rgb(35, 35, 96); font-size: 18px; font-weight: 600; text-align: left;">
                        ${$('<div>').text(role.name).html()}
                    </h3>
                    <input class="role-name-input" type="text" value="${$('<div>').text(role.name).html()}" style="display:none; font-size:18px; font-weight:600; color:rgb(35,35,96); border:1px solid #ccc; border-radius:4px; padding:2px 8px;" />
                    <span style="display: flex; align-items: center; margin-left: 8px; cursor: pointer; user-select: none;">
                        <span class="role-toggle-active" style="width: 36px; height: 20px; border-radius: 12px; background: rgb(100, 108, 255); display: inline-block; position: relative; transition: background 0.2s; margin-right: 6px; cursor:pointer;">
                            <span style="position: absolute; left: 18px; top: 2px; width: 16px; height: 16px; border-radius: 50%; background: rgb(255, 255, 255); box-shadow: rgba(0, 0, 0, 0.08) 0px 1px 4px; transition: left 0.2s;"></span>
                        </span>
                        <span style="margin-left: 2px; font-size: 12px; color: rgb(46, 125, 50); font-weight: 600; min-width: 54px; text-align: left;">
                            Active
                        </span>
                    </span>
                </div>
                <p class="role-desc" data-editable="false" style="margin: 4px 0px 0px; color: rgb(102, 102, 102); font-size: 14px; text-align: left;">
                    ${$('<div>').text(role.description || '').html()}
                </p>
                <input class="role-desc-input" type="text" value="${$('<div>').text(role.description || '').html()}" style="display:none; font-size:14px; color:rgb(102,102,102); border:1px solid #ccc; border-radius:4px; padding:2px 8px; width:100%; margin-top:4px;" />
            </div>
        </div>
        <div style="min-width: 75px; background: rgb(232, 245, 232); color: rgb(46, 125, 50); padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px; text-transform: capitalize; text-align: center;">
            0 Users
        </div>
    </div>
    <div style="display: flex; gap: 8px; margin-top: 12px;">
        <form class="role-post-form" action="index.php?r=users/modules" method="post" style="display:inline;">
            <input type="hidden" name="role_id" value="${role.id}" />
            <button type="submit" style="flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(224, 224, 224); border-radius: 6px; background: white; color: rgb(35, 35, 96); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;">Module</button>
        </form>
        <form class="role-post-form" action="index.php?r=users/reports" method="post" style="display:inline;">
            <input type="hidden" name="role_id" value="${role.id}" />
            <button type="submit" style="flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(224, 224, 224); border-radius: 6px; background: white; color: rgb(35, 35, 96); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;">Report</button>
        </form>
        <button class="role-edit-btn" style="flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(224, 224, 224); border-radius: 6px; background: white; color: rgb(35, 35, 96); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;"><span class="edit-label">Edit</span><span class="save-label" style="display:none;">Save</span></button>
        <button class="role-delete-btn" style="flex: 1 1 0%; padding: 8px 16px; border: 1px solid rgb(255, 235, 238); border-radius: 6px; background: white; color: rgb(211, 47, 47); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; transition: 0.2s;">Delete</button>
    </div>
</div>`;
                        $card.remove();
                        $rolesGrid.prepend(cardHtml);
                        addRoleActive = false;
                        showGlobalAlert('Role added successfully!', 'success');
                    } else {
                        showGlobalAlert(resp.message || 'Failed to add role.', 'error');
                    }
                },
                error: function() {
                    showGlobalAlert('Error adding role.', 'error');
                },
                complete: function() {
                    $('.add-role-save').prop('disabled', false);
                }
            });
        });
    });
</script>

<style>
    #moduleSearch {
        min-width: 200px;
        width: 100%;
        padding: 8px 10px 8px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 7px;
        font-size: 13px;
        outline: none;
        margin-bottom: 16px;
        margin-top: 8px;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }

    .editable-cell {
        cursor: pointer;
        transition: background 0.2s;
        border-radius: 5px;
        position: relative;
    }


    .modules-table-container {
        width: 100%;
        max-width: 100vw;
        height: 60vh;
        overflow-y: auto;
        overflow-x: auto;
        background: #fff;
        border-radius: 10px;
        padding: 0;
        margin: 0;
    }

    .modules-table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
    }

    .modules-table th,
    .modules-table td {
        font-size: 13px;
        padding: 3px 8px;
        border-bottom: 1px solid #f0f0f0;
        text-align: left;
    }

    .modules-table th {
        background: #fafdff;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .modules-table tr:last-child td {
        border-bottom: none;
    }

    .systemmodules-wrapper {
        width: auto;
        min-height: 100vh;
        padding: 0 3% 0 3%;
        margin: 0;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .systemmodules-header {
        color: #232360;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 12px 0;
        padding: 24px 0 0 0;
        background: none;
    }

    .edit-input-field {
        width: 100%;
        padding: 7px 10px;
        border: 1.5px solid #a3a3ff;
        border-radius: 6px;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(100, 108, 255, 0.08);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        color: #232360;
    }

    .edit-input-field:focus {
        border-color: #646cff;
        box-shadow: 0 0 0 2px #e0e6f7;
        background: #fff;
    }

    @media (max-width: 900px) {

        .modules-table th,
        .modules-table td {
            font-size: 11px;
            padding: 6px 2px;
        }

        .modules-table-container {
            height: 40vh;
        }

        .systemmodules-wrapper {
            padding: 0 1% 0 1%;
        }
    }
</style>