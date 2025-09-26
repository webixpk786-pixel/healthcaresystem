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

// If no pharmacy modules found, add some mock modules for demonstration
if (empty($modules)) {
    $modules = [
        [
            'id' => 1,
            'name' => 'Medicine Catalog',
            'link' => 'pharmacy/medicines',
            'color' => '#e6f7f7',
            'icon' => 'fas fa-pills',
            'current_status' => '1',
            'description' => 'Manage medicine inventory',
            'can_view' => 1,
            'last_updated_at' => null
        ],
        [
            'id' => 2,
            'name' => 'Stock Management',
            'link' => 'pharmacy/stock',
            'color' => '#eafff6',
            'icon' => 'fas fa-boxes-stacked',
            'current_status' => '1',
            'description' => 'Track stock levels',
            'can_view' => 1,
            'last_updated_at' => null
        ],
        [
            'id' => 3,
            'name' => 'Purchase Orders',
            'link' => 'pharmacy/purchase-orders',
            'color' => '#fffbe6',
            'icon' => 'fas fa-shopping-cart',
            'current_status' => '1',
            'description' => 'Manage orders',
            'can_view' => 1,
            'last_updated_at' => null
        ],
        [
            'id' => 4,
            'name' => 'Sales & Billing',
            'link' => 'pharmacy/sales',
            'color' => '#f6eaff',
            'icon' => 'fas fa-cash-register',
            'current_status' => '1',
            'description' => 'Process sales',
            'can_view' => 1,
            'last_updated_at' => null
        ],
        [
            'id' => 5,
            'name' => 'Expiry Alerts',
            'link' => 'pharmacy/expiry-alerts',
            'color' => '#fef2f2',
            'icon' => 'fas fa-exclamation-triangle',
            'current_status' => '1',
            'description' => 'Monitor expiry',
            'can_view' => 1,
            'last_updated_at' => null
        ],
        [
            'id' => 6,
            'name' => 'Suppliers',
            'link' => 'pharmacy/suppliers',
            'color' => '#f0f7ff',
            'icon' => 'fas fa-truck',
            'current_status' => '1',
            'description' => 'Manage suppliers',
            'can_view' => 1,
            'last_updated_at' => null
        ],
        [
            'id' => 7,
            'name' => 'Reports',
            'link' => 'pharmacy/reports',
            'color' => '#fef9e7',
            'icon' => 'fas fa-chart-line',
            'current_status' => '1',
            'description' => 'View analytics',
            'can_view' => 1,
            'last_updated_at' => null
        ],
        [
            'id' => 8,
            'name' => 'Settings',
            'link' => 'pharmacy/settings',
            'color' => '#f0f4ff',
            'icon' => 'fas fa-cog',
            'current_status' => '1',
            'description' => 'System settings',
            'can_view' => 1,
            'last_updated_at' => null
        ]
    ];
}

?>
<style>
    .pharmacy-modules-container {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        margin: 0 24px 24px;
        box-shadow: 0 2px 12px rgba(100, 108, 255, 0.06);
        border: 1px solid #f0f0f7;
    }

    .modules-header {
        margin-bottom: 20px;
    }

    .modules-title {
        font-size: 20px;
        font-weight: 700;
        color: #232360;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modules-title i {
        color: #646cff;
        font-size: 22px;
    }

    .modules-subtitle {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        justify-content: center;
        align-items: stretch;
    }

    .module-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        min-height: 120px;
        border: 2px solid #f8fafc;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
    }

    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #646cff, #8b5cf6, #ec4899);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(100, 108, 255, 0.15);
        border-color: #e0e7ff;
        text-decoration: none;
        color: inherit;
    }

    .module-card:hover::before {
        transform: scaleX(1);
    }

    .module-card:hover .module-icon {
        transform: scale(1.1);
    }

    .module-card:hover .module-title {
        color: #646cff;
    }

    .module-icon {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        border-radius: 12px;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 20px;
        color: #646cff;
        box-shadow: 0 2px 8px rgba(100, 108, 255, 0.2);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .module-title {
        font-weight: 600;
        font-size: 14px;
        color: #232360;
        margin-bottom: 6px;
        transition: color 0.3s ease;
        line-height: 1.3;
    }

    .module-description {
        color: #6b7280;
        font-size: 12px;
        line-height: 1.4;
        margin-bottom: 8px;
        flex: 1;
    }

    .module-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .status-maintenance {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .status-restricted {
        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .no-modules {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }

    .no-modules-icon {
        font-size: 36px;
        color: #d1d5db;
        margin-bottom: 12px;
    }

    .no-modules-text {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .no-modules-subtext {
        font-size: 13px;
        color: #9ca3af;
    }

    @media (max-width: 768px) {
        .pharmacy-modules-container {
            margin: 0 16px 16px;
            padding: 20px;
        }

        .modules-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .modules-title {
            font-size: 18px;
        }

        .module-card {
            padding: 12px;
            min-height: 100px;
        }

        .module-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .module-title {
            font-size: 13px;
        }

        .module-description {
            font-size: 11px;
        }
    }
</style>

<div class="pharmacy-modules-container">
    <div class="modules-header">
        <h3 class="modules-title">
            <i class="fas fa-cubes"></i>
            Pharmacy Modules
        </h3>
        <p class="modules-subtitle">Quick access to all pharmacy management tools</p>
    </div>

    <?php if (!empty($modules)): ?>
        <div class="modules-grid">
            <?php foreach ($modules as $mod): ?>
                <?php
                $status = strtolower($mod['current_status']);
                $statusClass = 'module-status';

                switch ($status) {
                    case '1':
                        $statusClass = 'module-status';
                        break;
                    case '2':
                        $statusClass = 'module-status status-maintenance';
                        break;
                    case '3':
                        $statusClass = 'module-status status-restricted';
                        break;
                    default:
                        $statusClass = 'module-status';
                }

                if ($mod['can_view'] == 0) {
                    $statusClass = 'module-status status-restricted';
                }

                // Build the link
                $moduleLink = 'index.php?r=' . $mod['link'];
                ?>

                <a href="<?= htmlspecialchars($moduleLink) ?>" class="module-card"
                    <?= $mod['can_view'] == 0 ? 'onclick="return false;" style="opacity: 0.6; cursor: not-allowed;"' : '' ?>>

                    <div class="module-icon">
                        <?php if (!empty($mod['icon'])): ?>
                            <i class="<?= htmlspecialchars($mod['icon']) ?>"></i>
                        <?php else: ?>
                            <i class="fas fa-cube"></i>
                        <?php endif; ?>
                    </div>

                    <div class="module-title"><?= htmlspecialchars($mod['name']) ?></div>
                    <div class="module-description">
                        <?= htmlspecialchars($mod['description'] ?? 'Module functionality') ?>
                    </div>
                    <div class="<?= $statusClass ?>">
                        <i class="fas fa-circle" style="font-size: 6px;"></i>
                        Active
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
            <div class="no-modules-subtext">Contact administrator for access</div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click tracking and smooth interactions
        const moduleCards = document.querySelectorAll('.module-card');

        moduleCards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (this.style.opacity === '0.6') {
                    e.preventDefault();
                    return false;
                }

                // Add loading effect
                this.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        });
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />