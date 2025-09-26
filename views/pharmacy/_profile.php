<?php

$user = Yii::$app->user->identity;
$attr = $user->attributes;
$profileImage = $attr['profile_image'] ?? null;
$firstName = $attr['first_name'] ?? '';
$lastName = $attr['last_name'] ?? '';
$role = $attr['role'] ?? '';
$email = $attr['email'] ?? '';
$address = $attr['address'] ?? '';
$initials = strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1));

?>

<div class="col-md-3">
    <div
        style="width: 100%; padding: 28px 24px 24px; border-radius: 18px; display: flex; flex-direction: column; gap: 0px;">
        <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 10px;">
            <div class="profile-avatar"
                style="width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: rgb(255, 255, 255); font-weight: 800; font-size: 30px; border: 3px solid rgb(255, 255, 255); flex-shrink: 0; overflow: hidden; cursor: pointer; position: relative; transition: all 0.3s ease;"
                onclick="uploadProfileImage(<?= $user->id ?>)" title="Click to upload profile image">
                <?php if ($profileImage): ?>
                    <img alt="Profile" src="images/profile_images/<?= $profileImage ?>"
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div
                        style="display: none; width: 100%; height: 100%; background: rgb(100, 108, 255); box-shadow: rgba(100, 108, 255, 0.13) 0px 2px 8px; align-items: center; justify-content: center;">
                        <?= $initials ?>
                    </div>
                <?php else: ?>
                    <div
                        style="display: flex; width: 100%; background: rgb(100, 108, 255); height: 100%; box-shadow: rgba(100, 108, 255, 0.13) 0px 2px 8px; align-items: center; justify-content: center;">
                        <?= $initials ?>
                    </div>
                <?php endif; ?>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: center;">
                <div style="font-weight: 900; color: rgb(35, 35, 96); font-size: 22px; line-height: 1.2;">
                    <?= $firstName . ' ' . $lastName ?>
                </div>
                <span
                    style="display: inline-block; background: rgb(230, 234, 255); color: rgb(100, 108, 255); font-weight: 600; font-size: 13px; border-radius: 12px; padding: 2px 12px; margin-top: 6px; vertical-align: middle; text-transform: capitalize;">
                    <?= $role ?>
                </span>
            </div>
        </div>
        <div style="height: 1px; background: rgb(240, 240, 247); margin: 0px 0px 10px; width: 100%;"></div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">

            <span style="color: rgb(85, 85, 85); font-size: 14px; word-break: break-word;"><?= $email ?></span>
        </div>
        <div style="display: flex; gap: 8px;">
            <span style="color: rgb(85, 85, 85); font-size: 14px; text-align: left;">
                <?= $address ?>
            </span>
        </div>
    </div>
</div>

<style>
    .profile-avatar {
        transition: all 0.3s ease;
        position: relative;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(100, 108, 255, 0.2);
    }

    .profile-avatar::after {
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

    .profile-avatar:hover::after {
        opacity: 1;
    }
</style>

<script>
    // Function to upload profile image
    function uploadProfileImage(userId) {
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
                if (typeof showGlobalAlert === 'function') {
                    showGlobalAlert('Please select an image file.', 'error');
                } else {
                    alert('Please select an image file.');
                }
                return;
            }

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                if (typeof showGlobalAlert === 'function') {
                    showGlobalAlert('File size must be less than 2MB.', 'error');
                } else {
                    alert('File size must be less than 2MB.');
                }
                return;
            }

            // Show loading
            if (typeof showGlobalAlert === 'function') {
                showGlobalAlert('Uploading profile image...', 'info');
            }

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
                        // Update the profile image display
                        var avatar = document.querySelector('.profile-avatar');
                        if (avatar) {
                            var img = avatar.querySelector('img');
                            if (img) {
                                img.src = 'images/profile_images/' + resp.filename + '?t=' + new Date()
                                    .getTime();
                                img.style.display = 'block';
                                img.nextElementSibling.style.display = 'none';
                            } else {
                                // Create new image element if it doesn't exist
                                var newImg = document.createElement('img');
                                newImg.alt = 'Profile';
                                newImg.src = 'images/profile_images/' + resp.filename + '?t=' + new Date()
                                    .getTime();
                                newImg.style.cssText =
                                    'width: 100%; height: 100%; object-fit: cover; border-radius: 50%;';
                                newImg.onerror = function() {
                                    this.style.display = 'none';
                                    this.nextElementSibling.style.display = 'flex';
                                };

                                var initialsDiv = avatar.querySelector('div');
                                if (initialsDiv) {
                                    initialsDiv.style.display = 'none';
                                    avatar.insertBefore(newImg, initialsDiv);
                                }
                            }
                        }

                        if (typeof showGlobalAlert === 'function') {
                            showGlobalAlert('Profile image uploaded successfully!', 'success');
                        } else {
                            alert('Profile image uploaded successfully!');
                        }
                    } else {
                        if (typeof showGlobalAlert === 'function') {
                            showGlobalAlert(resp.message || 'Failed to upload image.', 'error');
                        } else {
                            alert(resp.message || 'Failed to upload image.');
                        }
                    }
                },
                error: function() {
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert('Failed to upload image. Please try again.', 'error');
                    } else {
                        alert('Failed to upload image. Please try again.');
                    }
                }
            });
        };

        // Trigger file selection
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    }
</script>