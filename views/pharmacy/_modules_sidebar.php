<?php

$modules = [];

$user = Yii::$app->user;
if ($user && $user->identity && $user->identity->role) {
    $role = $user->identity->role;

    $roleId = Yii::$app->db->createCommand(
        'SELECT id FROM roles WHERE link LIKE :role AND id_deleted = 0',
        [':role' => $role]
    )->queryScalar();

    if ($roleId) {
        // Get permitted modules for the role
        $modules = Yii::$app->db->createCommand(
            '
            SELECT
                m.id, 
                m.name, 
                m.link,
                m.color,
                m.icon,
                m.react_icon, 
                m.sort_order,
                m.current_status,
                m.description, 
                rmp.can_view, 
                rmp.can_create, 
                rmp.can_edit, 
                rmp.can_delete,
                DATE_FORMAT(al.created_at, "%Y-%m-%d %h:%i %p") AS last_updated_at
            FROM modules m
            LEFT JOIN role_module_permissions rmp 
                ON m.id = rmp.module_id AND rmp.role_id = :roleId
            LEFT JOIN modules parent 
                ON m.parent_id = parent.id
            LEFT JOIN role_module_permissions parent_rmp 
                ON parent.id = parent_rmp.module_id AND parent_rmp.role_id = :roleId
            LEFT JOIN (
                SELECT al1.*
                FROM activity_logs al1
                INNER JOIN (
                    SELECT location, MAX(id) AS max_id
                    FROM activity_logs
                    GROUP BY location
                ) latest ON al1.id = latest.max_id
            ) al ON al.location LIKE CONCAT("%", m.name, "%")
            WHERE
                rmp.role_id = :roleId
                AND m.is_active = TRUE
                AND (m.type = "pharmacy" OR m.type="both")
                AND (
                    m.parent_id IS NULL 
                    OR parent_rmp.can_view = TRUE
                )
                AND m.parent_id IS NULL
                GROUP BY m.id
            ORDER BY al.id DESC
            
            ',
            [':roleId' => $roleId]
        )->queryAll();
    }
}

// // If no pharmacy modules found, add some mock modules for demonstration
// if (empty($modules)) {
//     $modules = [
//         [
//             'id' => 1,
//             'name' => 'Medicine Catalog',
//             'link' => 'pharmacy/medicines',
//             'color' => '#e6f7f7',
//             'icon' => 'fas fa-pills',
//             'current_status' => '1',
//             'description' => 'Manage medicine inventory',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ],
//         [
//             'id' => 2,
//             'name' => 'Stock Management',
//             'link' => 'pharmacy/stock',
//             'color' => '#eafff6',
//             'icon' => 'fas fa-boxes-stacked',
//             'current_status' => '1',
//             'description' => 'Track stock levels',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ],
//         [
//             'id' => 3,
//             'name' => 'Purchase Orders',
//             'link' => 'pharmacy/purchase-orders',
//             'color' => '#fffbe6',
//             'icon' => 'fas fa-shopping-cart',
//             'current_status' => '1',
//             'description' => 'Manage orders',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ],
//         [
//             'id' => 4,
//             'name' => 'Sales & Billing',
//             'link' => 'pharmacy/sales',
//             'color' => '#f6eaff',
//             'icon' => 'fas fa-cash-register',
//             'current_status' => '1',
//             'description' => 'Process sales',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ],
//         [
//             'id' => 5,
//             'name' => 'Expiry Alerts',
//             'link' => 'pharmacy/expiry-alerts',
//             'color' => '#fef2f2',
//             'icon' => 'fas fa-exclamation-triangle',
//             'current_status' => '1',
//             'description' => 'Monitor expiry',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ],
//         [
//             'id' => 6,
//             'name' => 'Suppliers',
//             'link' => 'pharmacy/suppliers',
//             'color' => '#f0f7ff',
//             'icon' => 'fas fa-truck',
//             'current_status' => '1',
//             'description' => 'Manage suppliers',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ],
//         [
//             'id' => 7,
//             'name' => 'Reports',
//             'link' => 'pharmacy/reports',
//             'color' => '#fef9e7',
//             'icon' => 'fas fa-chart-line',
//             'current_status' => '1',
//             'description' => 'View analytics',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ],
//         [
//             'id' => 8,
//             'name' => 'Settings',
//             'link' => 'pharmacy/settings',
//             'color' => '#f0f4ff',
//             'icon' => 'fas fa-cog',
//             'current_status' => '1',
//             'description' => 'System settings',
//             'can_view' => 1,
//             'last_updated_at' => null
//         ]
//     ];
// }

?>
<style>
.pharmacy-sidebar {
    background: #fff;
    border-radius: 16px;
    padding: 0;
    margin-bottom: 24px;
    /* box-shadow: 0 2px 12px rgba(100, 108, 255, 0.08); */
    /* border: 1px solid #f0f0f7; */
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
}

.sidebar-header {
    position: sticky;
    top: 0;
    background: linear-gradient(135deg, #646cff 0%, #8b5cf6 100%);
    color: white;
    padding: 20px;
    border-radius: 16px 16px 0 0;
    text-align: center;
    z-index: 1000;
}

.sidebar-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.sidebar-subtitle {
    font-size: 12px;
    opacity: 0.9;
    margin-top: 4px;
    font-weight: 500;
}

.modules-list {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.module-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-radius: 12px;
    text-decoration: none;
    color: #374151;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.module-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #646cff, #8b5cf6);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.module-item:hover {
    background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
    border-color: #e0e7ff;
    text-decoration: none;
    color: #374151;
    transform: translateX(4px);
}

.module-item:hover::before {
    transform: scaleY(1);
}

.module-item:hover .module-icon {
    transform: scale(1.1);
    background: linear-gradient(135deg, #646cff 0%, #8b5cf6 100%);
    color: white;
}

.module-item:hover .module-name {
    color: #646cff;
    font-weight: 600;
}

.module-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f9fafb;
}

.module-item.disabled:hover {
    transform: none;
    background: #f9fafb;
    border-color: transparent;
}

.module-icon {
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    border-radius: 10px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    color: #646cff;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.module-info {
    flex: 1;
    min-width: 0;
}

.module-name {
    font-weight: 600;
    font-size: 14px;
    color: #232360;
    margin-bottom: 2px;
    transition: all 0.3s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.module-desc {
    font-size: 11px;
    color: #6b7280;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.module-status {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    margin-left: 8px;
    flex-shrink: 0;
}

.module-status.maintenance {
    background: #f59e0b;
}

.module-status.restricted {
    background: #ef4444;
}

.module-arrow {
    color: #d1d5db;
    font-size: 12px;
    margin-left: 8px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.module-item:hover .module-arrow {
    color: #646cff;
    transform: translateX(2px);
}

.no-modules {
    text-align: center;
    padding: 40px 20px;
    color: #6b7280;
}

.no-modules-icon {
    font-size: 32px;
    color: #d1d5db;
    margin-bottom: 12px;
}

.no-modules-text {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
}

.no-modules-subtext {
    font-size: 12px;
    color: #9ca3af;
}

/* Scrollbar styling */
.pharmacy-sidebar::-webkit-scrollbar {
    width: 4px;
}

.pharmacy-sidebar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.pharmacy-sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.pharmacy-sidebar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

@media (max-width: 768px) {
    .pharmacy-sidebar {
        position: relative;
        top: auto;
        margin-bottom: 16px;
    }

    .sidebar-header {
        padding: 16px;
    }

    .sidebar-title {
        font-size: 16px;
    }

    .modules-list {
        padding: 12px;
    }

    .module-item {
        padding: 10px 12px;
    }

    .module-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .module-name {
        font-size: 13px;
    }

    .module-desc {
        font-size: 10px;
    }
}
</style>

<div class="pharmacy-sidebar">
    <!-- <div class="sidebar-header">
        <h3 class="sidebar-title">
            <i class="fas fa-cubes"></i>
            Pharmacy Modules
        </h3>
        <p class="sidebar-subtitle">Quick navigation</p>
    </div> -->

    <?php if (!empty($modules)): ?>
    <div class="modules-list">
        <?php foreach ($modules as $mod): ?>
        <?php
                $status = strtolower($mod['current_status']);
                $statusClass = 'module-status';

                switch ($status) {
                    case '1':
                        $statusClass = 'module-status';
                        break;
                    case '2':
                        $statusClass = 'module-status maintenance';
                        break;
                    case '3':
                        $statusClass = 'module-status restricted';
                        break;
                    default:
                        $statusClass = 'module-status';
                }

                if ($mod['can_view'] == 0) {
                    $statusClass = 'module-status restricted';
                }

                // Build the link
                $moduleLink = 'index.php?r=' . $mod['link'];
                $isDisabled = $mod['can_view'] == 0;
                ?>

        <a href="<?= htmlspecialchars($moduleLink) ?>/dashboard"
            class="module-item <?= $isDisabled ? 'disabled' : '' ?>"
            <?= $isDisabled ? 'onclick="return false;"' : '' ?>>

            <div class="module-icon">
                <?php if (!empty($mod['icon'])): ?>
                <i class="<?= htmlspecialchars($mod['icon']) ?>"></i>
                <?php else: ?>
                <i class="fas fa-cube"></i>
                <?php endif; ?>
            </div>

            <div class="module-info">
                <div class="module-name"><?= htmlspecialchars($mod['name']) ?></div>
                <div class="module-desc"><?= htmlspecialchars($mod['description'] ?? 'Module functionality') ?></div>
            </div>

            <div class="<?= $statusClass ?>"></div>
            <div class="module-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>

        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="no-modules">
        <div class="no-modules-icon">
            <i class="fas fa-cube"></i>
        </div>
        <div class="no-modules-text">No modules available</div>
        <div class="no-modules-subtext">Contact administrator</div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click tracking and smooth interactions
    const moduleItems = document.querySelectorAll('.module-item:not(.disabled)');

    moduleItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Add loading effect
            this.style.transform = 'translateX(8px) scale(0.98)';

            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Add active state management
    const currentPath = window.location.pathname + window.location.search;
    moduleItems.forEach(item => {
        if (item.getAttribute('href') && currentPath.includes(item.getAttribute('href').split('?r=')[
                1])) {
            item.classList.add('active');
            item.style.background = 'linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%)';
            item.style.borderColor = '#c7d2fe';
        }
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />