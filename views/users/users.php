<?php

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('1,2');
$canEdit = $can['canEdit'] ?? false;
$canDelete = $can['canDelete'] ?? false;
$canAdd = $can['canAdd'] ?? false;
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div
    style="position: absolute; inset: 50px 0px 0px 230px; overflow: hidden; direction: <?= \app\components\LanguageManager::getDirection() ?>;">
    <!-- <div style="display: flex; padding: 0 3% 0 3%; gap: 16px;">
        <div style="flex:1;">
            <div style="font-size: medium;font-weight: 600;color: #232360;margin-bottom: 4px;">
                <?= \app\components\LanguageManager::translate('System Users Management') ?></div>
            <ul style="margin: 0 0 0 18px; padding: 0; color: #444; font-size: 1rem;">
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('This page lists all <b>system users</b> and their key details. Users are the main building blocks of your application') ?>
                </li>
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('You can <b>edit</b> user details by clicking the Edit button. Changes are saved instantly and securely.') ?>
                </li>
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('The <b>Active/Inactive</b> toggle lets you enable or disable a user account. Toggle the switch to update the user status') ?>
                </li>
                <li style="font-size: smaller;">
                    <?= \app\components\LanguageManager::translate('Use the <b>search bar</b> to quickly filter users by name, email, or username') ?>
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
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <input id="userSearch"
                    placeholder="<?= \app\components\LanguageManager::translate('Search user by name') ?>..."
                    type="text" style="width: 300px; margin-right: 10px;" />

                <select id="roleFilter"
                    style="padding: 8px 10px; border-radius: 7px; border: 1px solid #e0e0e0; font-size: 13px;">
                    <option value=""><?= \app\components\LanguageManager::translate('All Roles') ?></option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($canAdd): ?>
                <button id="addUserBtn"
                    style="background:#646cff; color:#fff; border:none; border-radius:6px; padding:7px 18px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.2s;">+
                    <?= \app\components\LanguageManager::translate('Add New') ?></button>
            <?php endif; ?>
        </div>
        <div class="users-grid-container" style="height: 75vh; overflow-y: auto;">
            <div id="usersGrid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px;"></div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
    .swal2-wide-modal {
        width: 900px !important;
        max-width: 98vw;
        padding: 0 !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
        font-family: 'Segoe UI', Arial, sans-serif;
        padding: 20px;
    }

    .form-row {
        text-align: left !important;
    }

    .swal2-html-container {
        width: 100%;
        padding: 0 !important;
    }

    #swalUserForm {
        font-size: 14px;
        margin-top: 0;
    }

    #swalUserForm .form-row {
        /* display: flex;
        gap: 5px; */
        margin-bottom: 20px;
    }

    #swalUserForm .form-group {
        /* flex: 1 1 0; */
        min-width: 0;
        margin-bottom: 10px;
    }

    #swalUserForm label {
        font-weight: 600;
        color: #232360;
        margin-bottom: 2px;
        font-size: 13px;
    }

    #swalUserForm .form-control,
    #swalUserForm .form-select {
        border-radius: 7px;
        border: 1.2px solid #dbe3f7;
        font-size: 13px;
        padding: 7px 10px;
        box-shadow: none;
        background: #fafdff;
        transition: border-color 0.2s, box-shadow 0.2s;
        height: 32px;
    }

    #swalUserForm .form-control:focus,
    #swalUserForm .form-select:focus {
        border-color: #646cff;
        outline: none;
        box-shadow: 0 0 0 2px #646cff22;
        background: #f3f6ff;
    }

    .swal2-actions .btn {
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 10px 32px;
        margin: 0 8px;
        box-shadow: 0 2px 8px rgba(100, 108, 255, 0.08);
        border: none;
        transition: background 0.2s, box-shadow 0.2s;
    }

    .swal2-actions .btn-primary {
        background: linear-gradient(90deg, #646cff 0%, #232360 100%);
        color: #fff;
    }

    .swal2-actions .btn-primary:hover {
        background: linear-gradient(90deg, #232360 0%, #646cff 100%);
    }

    .swal2-actions .btn-secondary {
        background: #e0e6f7;
        color: #232360;
    }

    .swal2-actions .btn-secondary:hover {
        background: #d1d8f7;
    }

    .swal2-popup {
        padding-bottom: 0 !important;
    }

    .swal2-actions {
        display: flex !important;
        flex-direction: row !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 18px !important;
        width: 100%;
        margin-top: 18px;
        margin-bottom: 0;
        padding: 0 10px 10px 10px;
    }

    .swal2-actions .btn {
        min-width: 120px;
        font-size: 1.08rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 10px 32px;
        box-shadow: 0 2px 8px rgba(100, 108, 255, 0.08);
        border: none;
        transition: background 0.2s, box-shadow 0.2s;
    }

    .swal2-actions .btn-primary {
        background: linear-gradient(90deg, #646cff 0%, #232360 100%);
        color: #fff;
    }

    .swal2-actions .btn-primary:hover {
        background: linear-gradient(90deg, #232360 0%, #646cff 100%);
    }

    .swal2-actions .btn-secondary {
        background: #e0e6f7;
        color: #232360;
    }

    .swal2-actions .btn-secondary:hover {
        background: #d1d8f7;
    }

    @media (max-width: 1000px) {
        .swal2-wide-modal {
            width: 98vw !important;
        }

        #swalUserForm .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>
<script>
    var users = <?php echo json_encode($users); ?>;
    var canEdit = <?= $canEdit ?? false; ?>;
    var canDelete = <?= $canDelete ?? false; ?>;

    // Function to upload profile image
    function uploadProfileImage(userId) {
        if (!canEdit) {
            showGlobalAlert('You do not have permission to upload profile images.', 'error');
            return;
        }

        // Create file input
        var fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';

        fileInput.onchange = function() {
            var file = this.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                showGlobalAlert('Please select an image file.', 'error');
                return;
            }

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showGlobalAlert('File size must be less than 2MB.', 'error');
                return;
            }

            // Show loading
            showGlobalAlert('Uploading profile image...', 'info');

            // Upload file
            var formData = new FormData();
            formData.append('profile_image', file);
            formData.append('user_id', userId);

            $.ajax({
                url: 'index.php?r=users/uploadprofileimage',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        // Update user data
                        var user = users.find(function(u) {
                            return u.id == userId;
                        });
                        if (user) {
                            user.profile_image = resp.filename;
                        }

                        // Re-render users to show new image
                        filterUsers();

                        showGlobalAlert('Profile image uploaded successfully!', 'success');
                    } else {
                        showGlobalAlert(resp.message || 'Failed to upload image.', 'error');
                    }
                },
                error: function() {
                    showGlobalAlert('Failed to upload image. Please try again.', 'error');
                }
            });
        };

        // Trigger file selection
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    }

    function renderUsers(usersList) {
        var html = usersList.map(function(user) {
            var status = user.status == 1 ? 'checked' : '';
            return `<div class="role-card user-card" data-user-id="${user.id}"
            style="background: white; border-radius: 12px; padding: 24px; box-shadow: rgba(100, 108, 255, 0.1) 0px 2px 12px; border: 1px solid rgb(240, 240, 247); transition: 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 16px; background: transparent;">
                    <div class="user-avatar" style="width: 60px; height: 60px; border-radius: 50%; background: rgba(100, 108, 255, 0.133); border: none; display: flex; align-items: center; justify-content: center; color: rgb(100, 108, 255); font-weight: 700; font-size: 24px; position: relative; overflow: hidden; cursor: pointer;" onclick="uploadProfileImage(${user.id})" title="Click to upload profile image">
                        ${user.profile_image ? 
                            `<img src="images/profile_images/${user.profile_image}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                             <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; background: rgba(100, 108, 255, 0.133); color: rgb(100, 108, 255); font-weight: 700; font-size: 24px;">
                                ${(user.first_name ? user.first_name[0] : '') + (user.last_name ? user.last_name[0] : '')}
                             </div>` 
                            : 
                            `${(user.first_name ? user.first_name[0] : '') + (user.last_name ? user.last_name[0] : '')}`
                        }
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <h3 class="user-name" style="margin: 0px; color: rgb(35, 35, 96); font-size: 18px; font-weight: 600; text-align: left;">
                                ${user.first_name || ''} ${user.last_name || ''}
                            </h3>
                            <span style="display: flex; align-items: center; margin-left: 8px; cursor: pointer; user-select: none;">
                                <label class="switch mb-0" style="margin-bottom:0;">
                                ${canEdit ? `<input type="checkbox" class="user-status-toggle" data-id="${user.id}" ${status }>
                                  <span class="slider round"></span>` : ''}
                                </label>
                                <span class="ml-2">${user.status == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>'}</span>
                            </span>
                            <span style="display: flex; align-items: center; margin-left: 8px; cursor: pointer; user-select: none;">
                                <div style="color: rgb(35, 35, 96); font-size: small; line-height: 1.2;">
                                ${user.role_name}
                            </div>
                            </span>
                        </div>
                        <div style="color: #666; font-size: 13px; margin-top: 2px;">
                            <b>Username:</b> ${user.username || ''} &nbsp; <b>Email:</b> ${user.email || ''}
                        </div>
                        <div style="color: #666; font-size: 13px; margin-top: 2px;">
                            <b>Phone:</b> ${user.phone || ''} &nbsp; <b>Gender:</b> ${user.gender || ''}
                        </div>
                        <div style="color: #666; font-size: 13px; margin-top: 2px;">
                            <b>City:</b> ${user.city || ''} &nbsp; <b>Country:</b> ${user.country || ''}
                        </div>
                        <div style="color: #888; font-size: 12px; margin-top: 2px;">
                            <b>Created:</b> ${user.created_at ? user.created_at.substring(0, 16).replace('T', ' ') : ''} &nbsp; <b>Last Login:</b> ${user.last_login_at ? user.last_login_at.substring(0, 16).replace('T', ' ') : ''}
                        </div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    ${canEdit ? `<button class="btn btn-sm btn-info editUserBtn" data-id="${user.id}" style="margin-bottom: 6px;">Edit</button>` : ''}
                    ${canDelete ? `<button class="btn btn-sm btn-danger deleteUserBtn" data-id="${user.id}">Delete</button>` : ''}
                </div>
            </div>
        </div>`;
        }).join('');
        $('#usersGrid').html(html);
    }

    function filterUsers() {
        var search = $('#userSearch').val().toLowerCase();
        var role = $('#roleFilter').val();
        var filtered = users.filter(function(user) {
            var matchesSearch = !search || (user.first_name && user.first_name.toLowerCase().includes(search)) || (
                user.last_name && user.last_name.toLowerCase().includes(search)) || (user.email && user.email
                .toLowerCase().includes(search)) || (user.username && user.username.toLowerCase().includes(
                search));
            var matchesRole = !role || String(user.role_id) === role;
            return matchesSearch && matchesRole;
        });
        renderUsers(filtered);
    }

    $('<style>\
.switch { position: relative; display: inline-block; width: 40px; height: 22px; }\
.switch input { opacity: 0; width: 0; height: 0; }\
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 22px; }\
.slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }\
input:checked + .slider { background-color: #43a047; }\
input:checked + .slider:before { transform: translateX(18px); }\
</style>').appendTo('head');

    $(function() {
        renderUsers(users);
        $('#userSearch, #roleFilter').on('input change', filterUsers);

        function userFormSwal(isEdit, user) {
            let rolesOptions = `<option value="">Select Role</option>`;
            <?php foreach ($roles as $role): ?>
                rolesOptions +=
                    `<option value="<?= htmlspecialchars($role['id']) ?>" ${user && user.role_id == <?= json_encode($role['id']) ?> ? 'selected' : ''}><?= htmlspecialchars($role['name']) ?></option>`;
            <?php endforeach; ?>
            const html = `
            <form id="swalUserForm" autocomplete="off" style="margin-top:0;margin: 25px;">
                <input type="hidden"  name="id" value="${user ? user.id || '' : ''}" readonly>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" value="${user ? user.first_name || '' : ''}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" value="${user ? user.last_name || '' : ''}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="${user ? user.email || '' : ''}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>CNIC</label>
                        <input type="text" class="form-control" name="cnic" value="${user ? user.cnic || '' : ''}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Username</label>
                        <input type="text" class="form-control" autocomplete="off" name="username" value="${user ? user.username || '' : ''}" required>
                    </div>
                    <div class="form-group col-md-4 password-group">
                        <label>Password</label>
                        <input type="password" class="form-control" autocomplete="off" name="password" ${isEdit ? '' : 'required'}>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Role</label>
                        <select class="form-control" name="role_id" required>${rolesOptions}</select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Gender</label>
                        <select class="form-control" name="gender">
                            <option value="">Select Gender</option>
                            <option value="male" ${user && user.gender == 'Male' ? 'selected' : ''}>Male</option>
                            <option value="female" ${user && user.gender == 'Female' ? 'selected' : ''}>Female</option>
                            <option value="other" ${user && user.gender == 'Other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Date of Birth</label>
                        <input type="date" class="form-control" name="dob" value="${user ? user.dob || '' : ''}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="${user ? user.phone || '' : ''}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Alternate Phone</label>
                        <input type="text" class="form-control" name="alternate_phone" value="${user ? user.alternate_phone || '' : ''}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" value="${user ? user.address || '' : ''}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" value="${user ? user.city || '' : ''}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Country</label>
                        <input type="text" class="form-control" name="country" value="${user ? user.country || '' : ''}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="1" ${user && user.status == 1 ? 'selected' : ''}>Active</option>
                            <option value="0" ${user && user.status == 0 ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Profile Image</label>
                        <input type="file" class="form-control" name="profile_image">
                    </div>
                </div>
            </form>
        `;
            Swal.fire({
                title: isEdit ? 'Edit User' : 'Add User',
                html: html,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'swal2-wide-modal',
                    // confirmButton: 'btn btn-primary',
                    // cancelButton: 'btn btn-secondary'
                },
                didOpen: () => {},
                preConfirm: () => {
                    const form = $('#swalUserForm');
                    if (!form[0].checkValidity()) {
                        form[0].reportValidity();
                        return false;
                    }
                    return form.serialize();
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const data = result.value;
                    const url = isEdit ? 'index.php?r=users/updateuser&id=' + (user ? user.id : '') :
                        'index.php?r=users/adduser';
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(resp) {
                            if (resp.success) {
                                if (isEdit) {
                                    var idx = users.findIndex(function(u) {
                                        return u.id == resp.user.id;
                                    });
                                    if (idx !== -1) users[idx] = resp.user;
                                } else {
                                    users.unshift(resp.user);
                                }
                                filterUsers();
                                Swal.close();
                                showGlobalAlert(resp.message || 'User saved!', 'success');
                            } else {
                                showGlobalAlert(resp.message || 'Failed to save user.',
                                    'error');
                            }
                        },
                        error: function() {
                            showGlobalAlert('Failed to save user.', 'error');
                        }
                    });
                }
            });
        }
        $('#addUserBtn').off('click').on('click', function() {
            userFormSwal(false, null);
        });
        $(document).on('click', '.editUserBtn', function() {
            var id = $(this).data('id');
            var user = users.find(function(u) {
                return u.id == id;
            });
            if (user) {
                userFormSwal(true, user);
            }
        });

        // Delete User
        $(document).on('click', '.deleteUserBtn', function() {
            var id = $(this).data('id');
            showGlobalConfirm('Are you sure you want to delete this user?', function() {
                $.ajax({
                    url: 'index.php?r=users/deleteuser',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.success) {
                            users = users.filter(function(u) {
                                return u.id != id;
                            });
                            filterUsers();
                            showGlobalAlert(resp.message || 'User deleted!', 'success');
                        } else {
                            showGlobalAlert(resp.message || 'Failed to delete user.',
                                'error');
                        }
                    },
                    error: function() {
                        showGlobalAlert('Failed to delete user.', 'error');
                    }
                });
            }, 'warning');
        });

        // Toggle user status
        $(document).on('change', '.user-status-toggle', function() {
            var id = $(this).data('id');
            var checked = $(this).is(':checked');
            var newStatus = checked ? 1 : 0;
            var $card = $(this).closest('.user-card');
            $.ajax({
                url: 'index.php?r=users/toggleuserstatus',
                method: 'POST',
                data: {
                    id: id,
                    status: newStatus
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        var user = users.find(function(u) {
                            return u.id == id;
                        });
                        if (user) user.status = newStatus;
                        $card.find('span.badge').removeClass('badge-success badge-secondary')
                            .addClass(newStatus ? 'badge-success' : 'badge-secondary').text(
                                newStatus ? 'Active' : 'Inactive');
                        showGlobalAlert(resp.message || 'Status updated!', 'success');
                    } else {
                        showGlobalAlert(resp.message || 'Failed to update status.', 'error');
                    }
                },
                error: function() {
                    showGlobalAlert('Failed to update status.', 'error');
                }
            });
        });
    });
</script>

<style>
    #userSearch {
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

    /* Profile image styling */
    .user-avatar {
        transition: all 0.3s ease;
        position: relative;
    }

    .user-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(100, 108, 255, 0.2);
    }

    .user-avatar::after {
        content: '📷';
        position: absolute;
        top: -5px;
        right: -5px;
        background: #646cff;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .user-avatar:hover::after {
        opacity: 1;
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