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
                AND (m.type = "hospital" OR m.type="both")
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

?>
<style>
    .modules-cool-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        grid-template-columns: repeat(5, minmax(180px, 1fr));
        gap: 24px;
        margin: 0px 24px 18px;
        justify-content: center;
        align-items: stretch;
        overflow: visible;
    }

    .cool-module-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(100, 108, 255, 0.10);
        padding: 18px 16px 14px 16px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-height: 140px;
        border: 1.5px solid #f0f0f7;
        position: relative;
        transition: box-shadow 0.18s, transform 0.18s, background 0.18s;
        cursor: pointer;
        height: 100%;
        font-size: 13px;
        overflow: hidden;
    }

    .cool-module-card:hover {
        box-shadow: 0 6px 24px rgba(100, 108, 255, 0.18);
        background: #f5f8ff;
        transform: translateY(-3px) scale(1.025);
        border-color: #dbeafe;
    }

    .cool-module-card .cool-module-header {
        display: flex;
        align-items: center;
        width: 100%;
        margin-bottom: 6px;
    }

    .cool-module-card .cool-module-icon {
        background: #e6eaff;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(100, 108, 255, 0.07);
    }

    .cool-module-card .cool-module-title {
        font-weight: 700;
        font-size: 15px;
        color: #232360;
        letter-spacing: -0.5px;
        margin-right: 6px;
        flex: 1 1 0%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cool-module-card .cool-module-status {
        color: #4caf50;
        border-radius: 8px;
        padding: 2px 10px;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        background: #eafbe7;
        margin-left: 4px;
    }

    .cool-module-card .cool-module-desc {
        margin-top: 2px;
        color: #888;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
        min-height: 32px;
        margin-bottom: 8px;
    }

    .cool-module-card .cool-module-updated {
        margin-top: auto;
        color: #aaa;
        font-size: 10px;
        display: flex;
        align-items: center;
        gap: 3px;
        background: #f8f8fa;
        padding: 2px 8px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(100, 108, 255, 0.04);
        width: 100%;
    }
</style>
<div class="modules-cool-grid">
    <?php foreach ($modules as $mod): ?>
        <div class="cool-module-card" style="cursor: default;background: <?= $mod['color'] ? $mod['color'] : '#fff' ?>;">

            <div class="cool-module-header">
                <span class="cool-module-icon">
                    <?php if (!empty($mod['icon'])): ?>
                        <i class="<?= htmlspecialchars($mod['icon']) ?>" style="color: #646cff; font-size: 20px;"></i>
                    <?php else: ?>
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" color="#646cff"
                            height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="#e0e0e0" />
                        </svg>
                    <?php endif; ?>
                </span>
                <span class="cool-module-title">
                    <?= htmlspecialchars($mod['name']) ?>
                </span>

                <?php
                $status = strtolower($mod['current_status']); // Normalize input like "Active", "active", etc.

                switch ($status) {
                    case '1':
                        $color = '#4caf50'; // Green
                        $label = 'Active';
                        $icon = '<path d="M0 0h24v24H0z" fill="none"></path>
                 <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 
                          10-4.48 10-10S17.52 2 12 2zm-2 15-5-5 
                          1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>';
                        break;

                    case '2':
                        $color = '#ff9800'; // Orange
                        $label = 'Maintenance';
                        $icon = '<path d="M0 0h24v24H0z" fill="none"></path>
                 <path d="M12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm1 
                          13h-2v-2h2v2zm0-4h-2V7h2v6z"></path>';
                        break;

                    case '3':
                        $color = '#f44336'; // Red
                        $label = 'Restricted';
                        $icon = '<path d="M0 0h24v24H0z" fill="none"></path>
                 <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 
                          10 10-4.48 10-10S17.52 2 12 2zm5 
                          13H7v-2h10v2z"></path>'; // minus sign
                        break;

                    default:
                        $color = '#9e9e9e'; // Gray (unknown)
                        $label = 'Unknown';
                        $icon = '<circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>';
                }

                if ($mod['can_view'] == 0) {
                    $color = '#f44336'; // Red
                    $label = 'Restricted';
                    $icon = '<path d="M0 0h24v24H0z" fill="none"></path>
                 <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 
                          10 10-4.48 10-10S17.52 2 12 2zm5 
                          13H7v-2h10v2z"></path>'; // minus sign
                }
                ?>

                <span class="cool-module-status" style="display: inline-flex; align-items: center; color: <?= $color ?>;">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em"
                        width="1em" xmlns="http://www.w3.org/2000/svg" style="margin-right: 4px;">
                        <?= $icon ?>
                    </svg>
                    <?= htmlspecialchars($label) ?>
                </span>

            </div>
            <div class="cool-module-desc">
                <?= htmlspecialchars($mod['description'] ?? '') ?>
            </div>
            <div class="cool-module-updated">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em"
                    xmlns="http://www.w3.org/2000/svg" style="margin-right: 3px;">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path
                        d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79s7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58s9.14-3.47 12.65 0L21 3v7.12zM12.5 8v4.25l3.5 2.08-.72 1.21L11 13V8h1.5z">
                    </path>
                </svg>
                Last updated
                <?= !empty($mod['last_updated_at']) ? Yii::$app->formatter->asRelativeTime($mod['last_updated_at']) : 'Never' ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>