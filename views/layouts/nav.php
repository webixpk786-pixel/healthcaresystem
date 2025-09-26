<?php
// Get current user's role
$role = Yii::$app->user->identity->role ?? null;

// Get role_id from roles table
$roleId = Yii::$app->db->createCommand('SELECT id FROM roles WHERE link = :role AND id_deleted = 0', [':role' => $role])->queryScalar();

$modules = [];
if ($roleId) {
    $modules = Yii::$app->db->createCommand('
        SELECT m.name, m.link, m.id
        FROM modules m
        INNER JOIN role_module_permissions rmp ON rmp.module_id = m.id
        WHERE m.parent_id IS NULL
        AND rmp.role_id = :roleId
        AND rmp.can_view = 1
        AND m.is_active = 1
        AND m.id_deleted = 0
        ORDER BY m.sort_order ASC, m.id ASC
    ', [':roleId' => $roleId])->queryAll();
}

// Get current system type setting
$currentSystemType = Yii::$app->db->createCommand('SELECT setting_value FROM system_settings WHERE setting_key = "system_type"')->queryScalar() ?: 'both';

// Get current active module setting
$currentActiveModule = Yii::$app->db->createCommand('SELECT setting_value FROM system_settings WHERE setting_key = "active_module"')->queryScalar();

// If no active module is set, use the first available module
if (!$currentActiveModule && !empty($modules)) {
    $currentActiveModule = $modules[0]['link'];
    // Save it to database
    $exists = Yii::$app->db->createCommand('SELECT COUNT(*) FROM system_settings WHERE setting_key = "active_module"')->queryScalar();
    if ($exists) {
        Yii::$app->db->createCommand()->update('system_settings', [
            'setting_value' => $currentActiveModule
        ], ['setting_key' => 'active_module'])->execute();
    } else {
        Yii::$app->db->createCommand()->insert('system_settings', [
            'setting_key' => 'active_module',
            'setting_value' => $currentActiveModule,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->execute();
    }
}

// Get the active module details
$activeModuleDetails = null;
if ($currentActiveModule) {
    $activeModuleDetails = Yii::$app->db->createCommand('
        SELECT name, link FROM modules WHERE link = :link AND id_deleted = 0
    ', [':link' => $currentActiveModule])->queryOne();
}

?>
<div>
    <div class="navbar-fixed">
        <img id="navbar_logo_img"
            src="images/<?= Yii::$app->db->createCommand('SELECT setting_value FROM system_settings WHERE setting_key = "system_logo"')->queryScalar() ?: 'default-logo.png' ?>"
            width="50px" height="50px" onerror="this.src='images/default-logo.png'; this.onerror=null;"
            style="object-fit: contain; border-radius: 4px;">&nbsp;&nbsp;&nbsp;
        <a href="index.php" data-discover="true" class="navbar-logo" id="dashboard-link">
            <?= Yii::$app->db->createCommand('SELECT setting_value FROM system_settings WHERE setting_key = "app_name"')->queryScalar() ?>
        </a>

        <!-- System Type Toggle Buttons -->
        <div class="navbar-center system-type-toggle">
            <button class="toggle-btn <?= $currentSystemType === 'hospital' ? 'active' : '' ?>" data-type="hospital"
                onclick="updateSystemType('hospital')">
                🏥 Hospital
            </button>
            <button class="toggle-btn <?= $currentSystemType === 'pharmacy' ? 'active' : '' ?>" data-type="pharmacy"
                onclick="updateSystemType('pharmacy')">
                💊 Pharmacy

            </button>
            <!-- <button class="toggle-btn <?= $currentSystemType === 'both' ? 'active' : '' ?>" data-type="both"
                onclick="updateSystemType('both')">
                <a style="text-decoration: none; color: <?= $currentSystemType === 'both' ? 'white' : 'black' ?>;"
                    href="index.php?r=both/dashboard" data-discover="true">
                    🏥💊 Both
                </a>
            </button> -->
        </div>

        <!-- <div class="navbar-center1" style="display: none;">
            <div class="navbar-scroll-container">
                <div class="navbar-scroll-inner">
                    <div class="modules-scroll">
                        <?php if ($activeModuleDetails): ?>
                            <a class="module-link active"
                                style="text-decoration:none;color:#1976d2; cursor: pointer; border-bottom: 2.5px solid #1976d2; background: #e3f0ff;"
                                data-module-key="<?= htmlspecialchars($activeModuleDetails['link']) ?>"
                                href="index.php?r=<?= htmlspecialchars($activeModuleDetails['link']) ?>/dashboard"
                                data-discover="true">
                                <?= htmlspecialchars($activeModuleDetails['name']) ?>
                            </a>
                        <?php else: ?>
                            <?php if (true) { ?>
                                <?php foreach ($modules as $mod): ?>
                                    <a onclick="doit()" class="module-link"
                                        style="text-decoration:none;color:black; cursor: pointer;"
                                        data-module-key="<?php echo htmlspecialchars($mod['link']); ?>"
                                        href="index.php?r=<?php echo htmlspecialchars($mod['link']); ?>/dashboard"
                                        data-discover="true">
                                        <?php echo htmlspecialchars($mod['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php } ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="navbar-actions">
            <button title="Notifications" tabindex="0" class="navbar-btn">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em"
                    width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path
                        d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z">
                    </path>
                </svg>
            </button>
            <button title="Logout" tabindex="0" class="navbar-btn navbar-logout-btn">
                <a href="index.php?r=site/logout" class="navbar-logout-link" data-discover="true">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em"
                        width="1em" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path
                            d="m17 7-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z">
                        </path>
                    </svg>
                </a>
            </button>
        </div>

        <?php include('maintenance.php'); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function selectModule(systemType) {
    // Update system type in database
    $.ajax({
        url: 'index.php?r=users/updatesetting',
        method: 'POST',
        data: {
            key: 'system_type',
            value: systemType
        },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                localStorage.setItem('systemType', systemType);
                window.location.href = `index.php?r=${systemType}/dashboard`;
            }
        }
    });

    // Close modal
    document.getElementById('globalModal').classList.remove('active');

    // Update active module in database
    updateActiveModuleInDatabase(moduleLink);

    // Update navbar-center with selected module
    updateNavbarCenter(moduleLink, moduleName);

    // Navigate to the selected module
    setTimeout(function() {
        window.location.href = `index.php?r=${moduleLink}/dashboard`;
    }, 300);
}

// Function to update active module in database
function updateActiveModuleInDatabase(moduleLink) {
    $.ajax({
        url: 'index.php?r=users/updatesetting',
        method: 'POST',
        data: {
            key: 'active_module',
            value: moduleLink
        },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                localStorage.setItem('activeModuleKey', moduleLink);
            }
        }
    });
}

// Function to update navbar-center with selected module
function updateNavbarCenter(moduleLink, moduleName) {
    const navbarCenter = document.querySelector('.navbar-center');
    if (navbarCenter) {
        navbarCenter.innerHTML = `
            <div class="navbar-scroll-container">
                <div class="navbar-scroll-inner">
                    <div class="modules-scroll">
                        <a class="module-link active" 
                           style="text-decoration:none;color:#1976d2; cursor: pointer; border-bottom: 2.5px solid #1976d2; background: #e3f0ff;"
                           data-module-key="${moduleLink}"
                           href="index.php?r=${moduleLink}/dashboard"
                           data-discover="true">
                            ${moduleName}
                        </a>
                    </div>
                </div>
            </div>
        `;
    }
}

// Function to update system type (modified to show modal first)
function updateSystemType(type) {
    selectModule(type);
}

// Function to update navbar logo
function updateNavbarLogo(filename) {
    var navbarLogo = document.getElementById('navbar_logo_img');
    if (navbarLogo) {
        var newSrc = 'images/' + filename + '?t=' + new Date().getTime();
        navbarLogo.src = newSrc;
        console.log('Updating navbar logo to:', newSrc);
    } else {
        console.log('Navbar logo element not found');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Clear localStorage on dashboard (logo) click
    const dashboardLink = document.getElementById('dashboard-link');
    if (dashboardLink) {
        dashboardLink.addEventListener('click', function() {
            // Reset active module to first available module
            const firstModule = document.querySelector('.module-link');
            if (firstModule) {
                const moduleLink = firstModule.getAttribute('data-module-key');
                const moduleName = firstModule.textContent.trim();
                updateActiveModuleInDatabase(moduleLink);
                updateNavbarCenter(moduleLink, moduleName);
            }
        });
    }
});
</script>

<style>
.navbar-fixed {
    position: fixed;
    top: 0px;
    left: 0px;
    right: 0px;
    background-color: transparent;
    height: 8%;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 32px;
}

.navbar-logo {
    font-weight: 700;
    font-size: 24px;
    color: black;
    text-decoration: none;
    margin-right: 18px;
}

/* System Type Toggle Styles */
.system-type-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: 20px;
    margin-right: 20px;
}

.toggle-btn {
    padding: 6px 12px;
    border: 2px solid #e0e6f7;
    background: #fafdff;
    color: #666;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    outline: none;
}

.toggle-btn:hover {
    border-color: #646cff;
    background: #f0f4ff;
    color: #646cff;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(100, 108, 255, 0.15);
}

.toggle-btn.active {
    border-color: #646cff;
    background: #646cff;
    color: white;
    box-shadow: 0 2px 8px rgba(100, 108, 255, 0.3);
}

.toggle-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.navbar-center {
    flex: 1 1 0%;
    display: flex;
    justify-content: center;
    align-items: center;
    min-width: 0px;
}

.navbar-scroll-container {
    max-width: 70%;
    width: auto;
    min-width: 0px;
    margin: 0px auto;
    display: flex;
    align-items: center;
    overflow-x: auto;
    white-space: nowrap;
}

.navbar-scroll-inner {
    position: relative;
    width: 100%;
    min-width: 0px;
    display: flex;
    align-items: center;
    flex: 1 1 0%;
    max-width: 100vw;
    overflow: hidden;
}

.modules-scroll {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    width: 100%;
    min-width: 0px;
    text-align: center;
    justify-content: center;
    border-radius: 8px;
    padding: 2px 4px;
    white-space: normal;
    gap: 2px;
}

.module-link {
    color: black;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    margin-left: 6px;
    margin-right: 0px;
    padding: 3px 10px;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    font-size: clamp(12px, 1.5vw, 15px);
    min-width: 0px;
    max-width: 100%;
    border-radius: 6px;
    background: transparent;
    box-shadow: none;
    transition: background 0.18s, color 0.18s, box-shadow 0.18s, border 0.18s;
    cursor: pointer;
}

.module-link:first-child {
    margin-left: 6px;
}

.module-link:not(:first-child) {
    margin-left: 0px;
}

.module-link.active {
    border-bottom: 2.5px solid #1976d2;
    background: #e3f0ff;
    color: #1976d2;
}

.navbar-actions {
    display: flex;
    align-items: center;
}

.navbar-btn {
    background: none;
    border: none;
    color: black;
    font-size: 28px;
    cursor: pointer;
    padding: 0px;
    outline: none;
    box-shadow: none;
    -webkit-tap-highlight-color: transparent;
}

.navbar-logout-link {
    text-decoration: none;
    color: black;
}

.navbar-logout-btn {
    margin-left: 20px;
}

/* Responsive design for toggle buttons */
@media (max-width: 768px) {
    .system-type-toggle {
        margin-left: 10px;
        margin-right: 10px;
        gap: 4px;
    }

    .toggle-btn {
        padding: 4px 8px;
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .system-type-toggle {
        flex-direction: column;
        gap: 2px;
    }

    .toggle-btn {
        padding: 3px 6px;
        font-size: 9px;
    }
}
</style>

<style>
.navbar-fixed {
    position: fixed;
    top: 0px;
    left: 0px;
    right: 0px;
    background-color: transparent;
    height: 8%;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 32px;
}

.navbar-logo {
    font-weight: 700;
    font-size: 24px;
    color: black;
    text-decoration: none;
    margin-right: 18px;
}

.navbar-center {
    flex: 1 1 0%;
    display: flex;
    justify-content: center;
    align-items: center;
    min-width: 0px;
}

.navbar-scroll-container {
    max-width: 70%;
    width: auto;
    min-width: 0px;
    margin: 0px auto;
    display: flex;
    align-items: center;
    overflow-x: auto;
    white-space: nowrap;
}

.navbar-scroll-inner {
    position: relative;
    width: 100%;
    min-width: 0px;
    display: flex;
    align-items: center;
    flex: 1 1 0%;
    max-width: 100vw;
    overflow: hidden;
}

.modules-scroll {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    width: 100%;
    min-width: 0px;
    text-align: center;
    justify-content: center;
    border-radius: 8px;
    padding: 2px 4px;
    white-space: normal;
    gap: 2px;
}

.module-link {
    color: black;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    margin-left: 6px;
    margin-right: 0px;
    padding: 3px 10px;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    font-size: clamp(12px, 1.5vw, 15px);
    min-width: 0px;
    max-width: 100%;
    border-radius: 6px;
    background: transparent;
    box-shadow: none;
    transition: background 0.18s, color 0.18s, box-shadow 0.18s, border 0.18s;
    cursor: pointer;
}

.module-link:first-child {
    margin-left: 6px;
}

.module-link:not(:first-child) {
    margin-left: 0px;
}

.module-link.active {
    border-bottom: 2.5px solid #1976d2;
    background: #e3f0ff;
    color: #1976d2;
}

.navbar-actions {
    display: flex;
    align-items: center;
}

.navbar-btn {
    background: none;
    border: none;
    color: black;
    font-size: 28px;
    cursor: pointer;
    padding: 0px;
    outline: none;
    box-shadow: none;
    -webkit-tap-highlight-color: transparent;
}

.navbar-logout-link {
    text-decoration: none;
    color: black;
}

.navbar-logout-btn {
    margin-left: 20px;
}
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
#globalModal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    min-width: 100vw;
    min-height: 100vh;
    background: rgba(0, 0, 0, 0.4);
    align-items: flex-start;
    /* Changed from center to flex-start for top positioning */
    justify-content: center;
    transition: background 0.2s;
    padding-top: 50px;
    /* Add top padding */
}

#globalModal.active {
    display: flex;
    animation: fadeInBg 0.2s;
}

@keyframes fadeInBg {
    from {
        background: rgba(0, 0, 0, 0);
    }

    to {
        background: rgba(0, 0, 0, 0.4);
    }
}

.globalModal-content {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    width: 40%;
    /* 40% width */
    height: 30%;
    /* 30% height */
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
    text-align: center;
    margin: 0 auto;
    position: relative;
    animation: fadeInModal 0.25s cubic-bezier(.4, 2, .6, 1);
    transform: translateY(0);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 300px;
    /* Minimum width to prevent too small modal */
    min-height: 150px;
    /* Minimum height to prevent too small modal */
}

@keyframes fadeInModal {
    from {
        transform: scale(0.85) translateY(-40px);
        /* Changed from translateY(40px) to translateY(-40px) */
        opacity: 0;
    }

    to {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

.globalModal-icon {
    font-size: 38px;
    margin-bottom: 12px;
    display: none;
}

.globalModal-icon.info {
    color: #2196f3;
    display: block;
}

.globalModal-icon.success {
    color: #43a047;
    display: block;
}

.globalModal-icon.warning {
    color: #ffa000;
    display: block;
}

.globalModal-icon.error {
    color: #e53935;
    display: block;
}

#globalModalMessage {
    margin-bottom: 24px;
    font-size: 17px;
    color: #222;
}

#globalModalActions button,
#globalModalAlertActions button {
    min-width: 90px;
    padding: 10px 0;
    font-size: 15px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    margin: 0 8px;
    box-shadow: 0 1px 4px rgba(100, 108, 255, 0.07);
    transition: background 0.18s, color 0.18s;
    cursor: pointer;
}

#globalModalConfirm {
    background: #646cff;
    color: #fff;
}

#globalModalConfirm:hover {
    background: #5a61e6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(100, 108, 255, 0.25);
}

#globalModalCancel {
    background: #f5f5f5;
    color: #666;
    border: 1px solid #ddd;
}

#globalModalCancel:hover {
    background: #e8e8e8;
    color: #333;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#globalModalAlertOk {
    background: #646cff;
    color: #fff;
}

@media (max-width: 500px) {
    .globalModal-content {
        min-width: 0;
        padding: 18px 6vw 14px 6vw;
    }
}

/* Modal module links styling */
#globalModal .module-link {
    color: black;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    white-space: nowrap;
    border: 2px solid transparent;
    font-size: 14px;
    border-radius: 6px;
    background: transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    margin: 2px;
}

#globalModal .module-link:hover {
    background: #e3f0ff;
    border-color: #1976d2;
    color: #1976d2;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.15);
}

#globalModal .module-link:active {
    transform: translateY(0);
}

/* Modal system-type-toggle styling (exactly same as navbar but without icons) */
#globalModal .system-type-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
    width: 100%;
    height: 100%;
}

#globalModal .toggle-btn {
    padding: 6px 12px;
    border: 2px solid #e0e6f7;
    background: #fafdff;
    color: #666;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    outline: none;
}

#globalModal .toggle-btn:hover {
    border-color: #646cff;
    background: #f0f4ff;
    color: #646cff;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(100, 108, 255, 0.15);
}

#globalModal .toggle-btn:active {
    transform: translateY(0);
}

/* Remove icons from navbar system-type-toggle as well */
.system-type-toggle .toggle-btn {
    padding: 6px 12px;
    border: 2px solid #e0e6f7;
    background: #fafdff;
    color: #666;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    outline: none;
}

.system-type-toggle .toggle-btn:hover {
    border-color: #646cff;
    background: #f0f4ff;
    color: #646cff;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(100, 108, 255, 0.15);
}

.system-type-toggle .toggle-btn.active {
    border-color: #646cff;
    background: #646cff;
    color: white;
    box-shadow: 0 2px 8px rgba(100, 108, 255, 0.3);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #globalModal {
        padding-top: 20px;
    }

    .globalModal-content {
        width: 60%;
        /* Larger percentage on tablets */
        height: 40%;
        padding: 16px;
        min-width: 280px;
        min-height: 120px;
    }

    #globalModal .system-type-toggle {
        gap: 4px;
    }

    #globalModal .toggle-btn {
        padding: 4px 8px;
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .globalModal-content {
        width: 80%;
        /* Even larger percentage on mobile */
        height: 50%;
        padding: 12px;
        min-width: 250px;
        min-height: 100px;
    }

    #globalModal .system-type-toggle {
        flex-direction: column;
        gap: 2px;
    }

    #globalModal .toggle-btn {
        padding: 3px 6px;
        font-size: 9px;
        width: 100%;
    }
}
</style>
<!-- Global Modal for Confirmations and Alerts -->
<div id="globalModal">
    <div class="globalModal-content">
        <div id="globalModalMessage"></div>
        <div id="globalModalActions">
            <button id="globalModalConfirm">Confirm</button>
            <button id="globalModalCancel">Cancel</button>
        </div>
        <div id="globalModalAlertActions" style="display:none;">
            <button id="globalModalAlertOk">OK</button>
        </div>
    </div>
</div>
<script>
function showGlobalConfirm(message, onConfirm, icon) {
    $('#globalModalMessage').text(message);
    $('#globalModalActions').show();
    $('#globalModalAlertActions').hide();
    setGlobalModalIcon(icon || 'info');
    $('#globalModal').addClass('active');
    $('#globalModalConfirm').off('click').on('click', function() {
        $('#globalModal').removeClass('active');
        if (onConfirm) onConfirm();
    });
    $('#globalModalCancel').off('click').on('click', function() {
        $('#globalModal').removeClass('active');
    });
}

function showGlobalAlert(message, icon) {
    $('#globalModalMessage').text(message);
    $('#globalModalActions').hide();
    $('#globalModalAlertActions').show();
    setGlobalModalIcon(icon || 'info');
    $('#globalModal').addClass('active');
    $('#globalModalAlertOk').off('click').on('click', function() {
        $('#globalModal').removeClass('active');
    });
}

function setGlobalModalIcon(type) {
    var $icon = $('#globalModalIcon');
    $icon.removeClass('info success warning error');
    if (type === 'success') {
        $icon.html('&#10003;').addClass('success').show(); // checkmark
    } else if (type === 'warning') {
        $icon.html('&#9888;').addClass('warning').show(); // warning
    } else if (type === 'error') {
        $icon.html('&#10060;').addClass('error').show(); // cross
    } else {
        $icon.html('&#9432;').addClass('info').show(); // info
    }
}

function showGlobalAlert1(message, type) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type || 'info',
        title: message,
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        customClass: {
            popup: 'swal2-toast'
        }
    });
}
</script>