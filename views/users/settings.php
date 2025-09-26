<?php

use app\components\SystemComponents;

$systemComponents = new SystemComponents();
$can = $systemComponents->checkModulePermission('1,5');
$canEdit = $can['canEdit'];
$canDelete = $can['canDelete'];
$canAdd = $can['canAdd'];

// Get current settings from database or use defaults
$settings = Yii::$app->db->createCommand('SELECT * FROM system_settings')->queryAll();
$settingsArray = [];
foreach ($settings as $setting) {
    $settingsArray[$setting['setting_key']] = $setting['setting_value'];
}

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div
    style="position: absolute; inset: 50px 0px 0px 230px; overflow: auto; direction: <?= \app\components\LanguageManager::getDirection() ?>;">
    <!-- <div style="display: flex; padding: 0 3% 0 3%; gap: 16px;">
        <div style="flex:1;">
            <div style="font-size: medium;font-weight: 600;color: #232360;margin-bottom: 4px;">
                <?= \app\components\LanguageManager::translate('System Settings Management') ?></div>
            <ul style="margin: 0 0 0 18px; padding: 0; color: #444; font-size: 1rem;">
                <li style="font-size: smaller;">Configure all <b>system settings</b> including application preferences,
                    security, and integrations</li>
                <li style="font-size: smaller;">Settings are organized into <b>tabs</b> for easy navigation and
                    management</li>
                <li style="font-size: smaller;">Changes are <b>saved automatically</b> and applied immediately to the
                    system</li>
                <li style="font-size: smaller;">Some settings may require <b>system restart</b> to take full effect</li>
                <li style="font-size: smaller;">All setting changes are <b>logged</b> for audit and security purposes.
                </li>
            </ul>
        </div>
    </div> -->

    <div class="settings-wrapper" style="padding: 0 3% 0 3%;margin-top: 2%">

        <div class="settings-container">

            <!-- Settings Tabs -->
            <div class="settings-tabs" style="margin-bottom: 24px;">
                <div class="tab-buttons"
                    style="display: flex; gap: 8px; border-bottom: 2px solid #e0e6f7; margin-bottom: 24px;">
                    <button class="tab-btn active" data-tab="general"
                        style="padding: 12px 24px; border: none; background: #646cff; color: white; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        🏢 General
                    </button>
                    <button class="tab-btn" data-tab="security"
                        style="padding: 12px 24px; border: none; background: #f0f0f0; color: #666; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        🔒 Security
                    </button>
                    <button class="tab-btn" data-tab="email"
                        style="padding: 12px 24px; border: none; background: #f0f0f0; color: #666; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        📧 Email
                    </button>
                    <button class="tab-btn" data-tab="backup"
                        style="padding: 12px 24px; border: none; background: #f0f0f0; color: #666; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        💾 Backup
                    </button>
                    <button class="tab-btn" data-tab="ui"
                        style="padding: 12px 24px; border: none; background: #f0f0f0; color: #666; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        🎨 UI
                    </button>
                    <button class="tab-btn" data-tab="integration"
                        style="padding: 12px 24px; border: none; background: #f0f0f0; color: #666; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        🔗 Integration
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content"
                style="height: 70vh; overflow-y: auto; padding: 20px; background: #fff; border-radius: 12px;">

                <!-- General Settings Tab -->
                <div class="tab-pane active" id="general-tab">
                    <div class="settings-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">

                        <div class="setting-item">
                            <label
                                style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Application
                                Name</label>
                            <input type="text" class="setting-input" data-key="app_name"
                                value="<?= $settingsArray['app_name'] ?? 'Super System' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">System
                                Version</label>
                            <input type="text" class="setting-input" data-key="app_version"
                                value="<?= $settingsArray['app_version'] ?? '1.0.0' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">System
                                Logo</label>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="flex: 1;">
                                    <input type="file" id="system_logo" accept="image/*" class="logo-file-input"
                                        style="display: none;">
                                    <button type="button" onclick="document.getElementById('system_logo').click()"
                                        style="width: 100%; padding: 10px; border: 2px dashed #dbe3f7; border-radius: 8px; background: #fafdff; color: #666; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                        <span style="font-size: 16px;">📁</span>
                                        <span>Choose Logo File</span>
                                    </button>
                                </div>
                                <div id="logo-preview"
                                    style="display: none; width: 60px; height: 60px; border-radius: 8px; overflow: hidden; border: 2px solid #e0e6f7;">
                                    <img id="logo-preview-img" src="" alt="Logo Preview"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                            <div id="logo-status" style="margin-top: 8px; font-size: 12px; color: #666;"></div>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Default
                                Language</label>
                            <select class="setting-input" data-key="default_language"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="en"
                                    <?= ($settingsArray['default_language'] ?? 'en') == 'en' ? 'selected' : '' ?>>
                                    English</option>
                                <option value="es"
                                    <?= ($settingsArray['default_language'] ?? 'en') == 'es' ? 'selected' : '' ?>>
                                    Spanish</option>
                                <option value="fr"
                                    <?= ($settingsArray['default_language'] ?? 'en') == 'fr' ? 'selected' : '' ?>>French
                                </option>
                                <option value="ar"
                                    <?= ($settingsArray['default_language'] ?? 'en') == 'ar' ? 'selected' : '' ?>>
                                    العربية
                                </option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Time
                                Zone</label>
                            <select class="setting-input" data-key="timezone"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="UTC"
                                    <?= ($settingsArray['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' ?>>UTC</option>
                                <option value="America/New_York"
                                    <?= ($settingsArray['timezone'] ?? 'UTC') == 'America/New_York' ? 'selected' : '' ?>>
                                    Eastern Time</option>
                                <option value="America/Chicago"
                                    <?= ($settingsArray['timezone'] ?? 'UTC') == 'America/Chicago' ? 'selected' : '' ?>>
                                    Central Time</option>
                                <option value="America/Denver"
                                    <?= ($settingsArray['timezone'] ?? 'UTC') == 'America/Denver' ? 'selected' : '' ?>>
                                    Mountain Time</option>
                                <option value="America/Los_Angeles"
                                    <?= ($settingsArray['timezone'] ?? 'UTC') == 'America/Los_Angeles' ? 'selected' : '' ?>>
                                    Pacific Time</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Date
                                Format</label>
                            <select class="setting-input" data-key="date_format"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="Y-m-d"
                                    <?= ($settingsArray['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' ?>>
                                    YYYY-MM-DD</option>
                                <option value="m/d/Y"
                                    <?= ($settingsArray['date_format'] ?? 'Y-m-d') == 'm/d/Y' ? 'selected' : '' ?>>
                                    MM/DD/YYYY</option>
                                <option value="d/m/Y"
                                    <?= ($settingsArray['date_format'] ?? 'Y-m-d') == 'd/m/Y' ? 'selected' : '' ?>>
                                    DD/MM/YYYY</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">System
                                Status</label>
                            <select class="setting-input" data-key="system_status"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="active"
                                    <?= ($settingsArray['system_status'] ?? 'active') == 'active' ? 'selected' : '' ?>>
                                    Active</option>
                                <option value="maintenance"
                                    <?= ($settingsArray['system_status'] ?? 'active') == 'maintenance' ? 'selected' : '' ?>>
                                    Maintenance Mode</option>
                                <option value="offline"
                                    <?= ($settingsArray['system_status'] ?? 'active') == 'offline' ? 'selected' : '' ?>>
                                    Offline</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Security Settings Tab -->
                <div class="tab-pane" id="security-tab" style="display: none;">
                    <div class="settings-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Session
                                Timeout (minutes)</label>
                            <input type="number" class="setting-input" data-key="session_timeout"
                                value="<?= $settingsArray['session_timeout'] ?? '30' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Max
                                Login Attempts</label>
                            <input type="number" class="setting-input" data-key="max_login_attempts"
                                value="<?= $settingsArray['max_login_attempts'] ?? '5' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label
                                style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Password
                                Policy</label>
                            <select class="setting-input" data-key="password_policy"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="low"
                                    <?= ($settingsArray['password_policy'] ?? 'medium') == 'low' ? 'selected' : '' ?>>
                                    Low (6+ characters)</option>
                                <option value="medium"
                                    <?= ($settingsArray['password_policy'] ?? 'medium') == 'medium' ? 'selected' : '' ?>>
                                    Medium (8+ chars, mixed)</option>
                                <option value="high"
                                    <?= ($settingsArray['password_policy'] ?? 'medium') == 'high' ? 'selected' : '' ?>>
                                    High (10+ chars, special chars)</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label
                                style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Two-Factor
                                Authentication</label>
                            <select class="setting-input" data-key="two_factor_auth"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="disabled"
                                    <?= ($settingsArray['two_factor_auth'] ?? 'disabled') == 'disabled' ? 'selected' : '' ?>>
                                    Disabled</option>
                                <option value="optional"
                                    <?= ($settingsArray['two_factor_auth'] ?? 'disabled') == 'optional' ? 'selected' : '' ?>>
                                    Optional</option>
                                <option value="required"
                                    <?= ($settingsArray['two_factor_auth'] ?? 'disabled') == 'required' ? 'selected' : '' ?>>
                                    Required</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">IP
                                Whitelist</label>
                            <textarea class="setting-input" data-key="ip_whitelist"
                                placeholder="Enter IP addresses (one per line)"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px; min-height: 80px;"><?= $settingsArray['ip_whitelist'] ?? '' ?></textarea>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Enable
                                Audit Log</label>
                            <select class="setting-input" data-key="audit_log_enabled"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="1"
                                    <?= ($settingsArray['audit_log_enabled'] ?? '1') == '1' ? 'selected' : '' ?>>Enabled
                                </option>
                                <option value="0"
                                    <?= ($settingsArray['audit_log_enabled'] ?? '1') == '0' ? 'selected' : '' ?>>
                                    Disabled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Email Settings Tab -->
                <div class="tab-pane" id="email-tab" style="display: none;">
                    <div class="settings-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">SMTP
                                Host</label>
                            <input type="text" class="setting-input" data-key="smtp_host"
                                value="<?= $settingsArray['smtp_host'] ?? 'smtp.gmail.com' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">SMTP
                                Port</label>
                            <input type="number" class="setting-input" data-key="smtp_port"
                                value="<?= $settingsArray['smtp_port'] ?? '587' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">SMTP
                                Username</label>
                            <input type="email" class="setting-input" data-key="smtp_username"
                                value="<?= $settingsArray['smtp_username'] ?? '' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">SMTP
                                Password</label>
                            <input type="password" class="setting-input" data-key="smtp_password"
                                value="<?= $settingsArray['smtp_password'] ?? '' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">From
                                Email</label>
                            <input type="email" class="setting-input" data-key="from_email"
                                value="<?= $settingsArray['from_email'] ?? 'noreply@example.com' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">From
                                Name</label>
                            <input type="text" class="setting-input" data-key="from_name"
                                value="<?= $settingsArray['from_name'] ?? 'System Admin' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- Backup Settings Tab -->
                <div class="tab-pane" id="backup-tab" style="display: none;">
                    <div class="settings-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Auto
                                Backup</label>
                            <select class="setting-input" data-key="auto_backup"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="disabled"
                                    <?= ($settingsArray['auto_backup'] ?? 'disabled') == 'disabled' ? 'selected' : '' ?>>
                                    Disabled</option>
                                <option value="daily"
                                    <?= ($settingsArray['auto_backup'] ?? 'disabled') == 'daily' ? 'selected' : '' ?>>
                                    Daily</option>
                                <option value="weekly"
                                    <?= ($settingsArray['auto_backup'] ?? 'disabled') == 'weekly' ? 'selected' : '' ?>>
                                    Weekly</option>
                                <option value="monthly"
                                    <?= ($settingsArray['auto_backup'] ?? 'disabled') == 'monthly' ? 'selected' : '' ?>>
                                    Monthly</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Backup
                                Retention (days)</label>
                            <input type="number" class="setting-input" data-key="backup_retention"
                                value="<?= $settingsArray['backup_retention'] ?? '30' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Backup
                                Location</label>
                            <input type="text" class="setting-input" data-key="backup_location"
                                value="<?= $settingsArray['backup_location'] ?? '/backups' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- UI Settings Tab -->
                <div class="tab-pane" id="ui-tab" style="display: none;">
                    <div class="settings-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">

                        <div class="setting-item">
                            <label
                                style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Theme</label>
                            <select class="setting-input" data-key="theme"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="light"
                                    <?= ($settingsArray['theme'] ?? 'light') == 'light' ? 'selected' : '' ?>>Light
                                </option>
                                <option value="dark"
                                    <?= ($settingsArray['theme'] ?? 'light') == 'dark' ? 'selected' : '' ?>>Dark
                                </option>
                                <option value="auto"
                                    <?= ($settingsArray['theme'] ?? 'light') == 'auto' ? 'selected' : '' ?>>Auto
                                    (System)</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Primary
                                Color</label>
                            <input type="color" class="setting-input" data-key="primary_color"
                                value="<?= $settingsArray['primary_color'] ?? '#646cff' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px; height: 45px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Items
                                Per Page</label>
                            <select class="setting-input" data-key="items_per_page"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="10"
                                    <?= ($settingsArray['items_per_page'] ?? '20') == '10' ? 'selected' : '' ?>>10
                                </option>
                                <option value="20"
                                    <?= ($settingsArray['items_per_page'] ?? '20') == '20' ? 'selected' : '' ?>>20
                                </option>
                                <option value="50"
                                    <?= ($settingsArray['items_per_page'] ?? '20') == '50' ? 'selected' : '' ?>>50
                                </option>
                                <option value="100"
                                    <?= ($settingsArray['items_per_page'] ?? '20') == '100' ? 'selected' : '' ?>>100
                                </option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Show
                                Notifications</label>
                            <select class="setting-input" data-key="show_notifications"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="1"
                                    <?= ($settingsArray['show_notifications'] ?? '1') == '1' ? 'selected' : '' ?>>
                                    Enabled</option>
                                <option value="0"
                                    <?= ($settingsArray['show_notifications'] ?? '1') == '0' ? 'selected' : '' ?>>
                                    Disabled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Integration Settings Tab -->
                <div class="tab-pane" id="integration-tab" style="display: none;">
                    <div class="settings-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">API
                                Rate Limit</label>
                            <input type="number" class="setting-input" data-key="api_rate_limit"
                                value="<?= $settingsArray['api_rate_limit'] ?? '1000' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Enable
                                API</label>
                            <select class="setting-input" data-key="api_enabled"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                                <option value="1"
                                    <?= ($settingsArray['api_enabled'] ?? '1') == '1' ? 'selected' : '' ?>>Enabled
                                </option>
                                <option value="0"
                                    <?= ($settingsArray['api_enabled'] ?? '1') == '0' ? 'selected' : '' ?>>Disabled
                                </option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <label style="font-weight: 600; color: #232360; margin-bottom: 8px; display: block;">Webhook
                                URL</label>
                            <input type="url" class="setting-input" data-key="webhook_url"
                                value="<?= $settingsArray['webhook_url'] ?? '' ?>"
                                style="width: 100%; padding: 10px; border: 1px solid #dbe3f7; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
    <?php if (\app\components\LanguageManager::isRTL()): ?>

    /* RTL Support for Arabic */
    .settings-wrapper {
        direction: rtl;
        text-align: right;
    }

    .settings-grid {
        direction: rtl;
    }

    .setting-item {
        text-align: right;
    }

    .tab-buttons {
        direction: rtl;
    }

    .tab-btn {
        direction: rtl;
    }

    .language-switcher {
        direction: rtl;
    }

    /* Fix input alignment for RTL */
    .setting-input {
        text-align: right;
        direction: rtl;
    }

    /* Fix dropdown alignment */
    select.setting-input option {
        text-align: right;
        direction: rtl;
    }

    <?php endif;

    ?>.setting-input {
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .setting-input:focus {
        border-color: #646cff !important;
        outline: none;
        box-shadow: 0 0 0 2px #646cff22;
    }

    /* Logo upload button styling */
    .logo-file-input+button {
        transition: all 0.3s ease;
    }

    .logo-file-input+button:hover {
        border-color: #646cff !important;
        background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(100, 108, 255, 0.15);
    }

    .logo-file-input+button:active {
        transform: translateY(0);
    }

    #logo-preview {
        transition: all 0.3s ease;
    }

    #logo-preview:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .setting-item {
        background: #fafdff;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e0e6f7;
        transition: all 0.2s ease;
    }

    .setting-item:hover {
        border-color: #646cff;
        box-shadow: 0 2px 8px rgba(100, 108, 255, 0.1);
    }

    .tab-btn {
        transition: all 0.3s ease;
    }

    .tab-btn:hover {
        background: #646cff !important;
        color: white !important;
        transform: translateY(-2px);
    }

    .tab-btn.active {
        background: #646cff !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(100, 108, 255, 0.3);
    }

    .tab-pane {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr !important;
        }

        .settings-container {
            height: 60vh !important;
        }

        .tab-buttons {
            flex-wrap: wrap;
        }

        .tab-btn {
            flex: 1;
            min-width: 120px;
            font-size: 12px;
            padding: 8px 12px !important;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Tab switching functionality
        $('.tab-btn').on('click', function() {
            var tabId = $(this).data('tab');

            // Remove active class from all tabs and content
            $('.tab-btn').removeClass('active').css({
                'background': '#f0f0f0',
                'color': '#666'
            });
            $('.tab-pane').hide();

            // Add active class to clicked tab and show content
            $(this).addClass('active').css({
                'background': '#646cff',
                'color': 'white'
            });
            $('#' + tabId + '-tab').show();
        });

        // Auto-save settings when changed
        $('.setting-input').on('change', function() {
            var key = $(this).data('key');
            var value = $(this).val();
            var $input = $(this);

            // Show saving indicator
            $input.css('border-color', '#ffa726');

            $.ajax({
                url: 'index.php?r=users/updatesetting',
                method: 'POST',
                data: {
                    key: key,
                    value: value
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $input.css('border-color', '#4caf50');
                        setTimeout(function() {
                            $input.css('border-color', '#dbe3f7');
                        }, 2000);

                        if (typeof showGlobalAlert === 'function') {
                            showGlobalAlert('Setting updated successfully!', 'success');
                            window.location.reload();
                        }
                    } else {
                        $input.css('border-color', '#f44336');
                        if (typeof showGlobalAlert === 'function') {
                            showGlobalAlert(resp.message || 'Failed to update setting.',
                                'error');
                            window.location.reload();
                        }
                    }
                },
                error: function() {
                    $input.css('border-color', '#f44336');
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert('Failed to update setting.', 'error');
                    }
                }
            });
        });

        // Test email settings
        $('input[data-key="smtp_host"], input[data-key="smtp_username"], input[data-key="smtp_password"]').on(
            'change',
            function() {
                // Add test email functionality here if needed
            });

        // Logo upload functionality
        $('#system_logo').on('change', function() {
            var file = this.files[0];
            var $status = $('#logo-status');
            var $preview = $('#logo-preview');
            var $previewImg = $('#logo-preview-img');
            var $button = $(this).siblings('button');

            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    $status.html('<span style="color: #f44336;">❌ Please select an image file</span>');
                    return;
                }

                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    $status.html('<span style="color: #f44336;">❌ File size must be less than 2MB</span>');
                    return;
                }

                // Show preview
                var reader = new FileReader();
                reader.onload = function(e) {
                    $previewImg.attr('src', e.target.result);
                    $preview.show();
                };
                reader.readAsDataURL(file);

                // Update button text
                $button.html('<span style="font-size: 16px;">📁</span><span>' + file.name + '</span>');

                // Upload file
                var formData = new FormData();
                formData.append('system_logo', file);

                $status.html('<span style="color: #ffa726;">⏳ Uploading...</span>');

                $.ajax({
                    url: 'index.php?r=users/uploadlogo',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.success) {
                            $status.html(
                                '<span style="color: #4caf50;">✅ Logo uploaded successfully!</span>'
                            );
                            setTimeout(function() {
                                $status.html('<span style="color: #666;">Logo: ' + resp
                                    .filename + '</span>');
                            }, 3000);

                            // Update navbar logo immediately
                            if (typeof updateNavbarLogo === 'function') {
                                updateNavbarLogo(resp.filename);
                            } else {
                                // Fallback if function doesn't exist
                                var navbarLogo = document.getElementById('navbar_logo_img');
                                if (navbarLogo) {
                                    var newSrc = 'images/' + resp.filename + '?t=' + new Date()
                                        .getTime();
                                    navbarLogo.src = newSrc;
                                    console.log('Navbar logo updated via fallback');
                                }
                            }
                        } else {
                            $status.html('<span style="color: #f44336;">❌ ' + (resp.message ||
                                'Upload failed') + '</span>');
                        }
                    },
                    error: function() {
                        $status.html(
                            '<span style="color: #f44336;">❌ Upload failed. Please try again.</span>'
                        );
                    }
                });
            }
        });

        // Show current logo if exists
        var currentLogo = '<?= $settingsArray['system_logo'] ?? '' ?>';
        if (currentLogo) {
            $('#logo-preview-img').attr('src', 'images/' + currentLogo);
            $('#logo-preview').show();
            $('#logo-status').html('<span style="color: #666;">Current logo: ' + currentLogo + '</span>');
        }
    });
</script>