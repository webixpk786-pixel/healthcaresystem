<?php
$role = Yii::$app->user->identity->role ?? null;
$controller = Yii::$app->view->params['controller'] ?? 'admin';
$roleId = Yii::$app->db->createCommand('SELECT id FROM roles WHERE link = :role AND id_deleted = 0', [':role' => $role])->queryScalar();

$modules = [];
if ($roleId) {

    $parent_module = Yii::$app->db->createCommand('
                    SELECT m.name, m.link, m.id, m.icon
                    FROM modules m
                    INNER JOIN role_module_permissions rmp ON rmp.module_id = m.id
                    WHERE m.id = :id
                    AND rmp.role_id = :roleId
                    AND rmp.can_view = 1
                    AND m.is_active = 1
                    AND m.id_deleted = 0
                    ORDER BY m.sort_order ASC
                ', [':id' => $parent_id, ':roleId' => $roleId])->queryOne();
    if ($parent_module) {
        $modules = Yii::$app->db->createCommand('
                    SELECT m.name, m.link, m.id, m.icon
                    FROM modules m
                    INNER JOIN role_module_permissions rmp ON rmp.module_id = m.id
                    WHERE m.parent_id = :parent_id
                    AND rmp.role_id = :roleId
                    AND rmp.can_view = 1
                    AND m.is_active = 1
                    AND m.id_deleted = 0
                    ORDER BY m.sort_order ASC
                ', [':parent_id' => $parent_module['id'], ':roleId' => $roleId])->queryAll();
    }
    if (count($modules) == 0) {

        $js = "showGlobalAlert1('You do not have permission to view this page.', 'error');";
        Yii::$app->view->registerJs($js, \yii\web\View::POS_END);
        // Yii::$app->response->redirect(['site/login'])->send();
        // exit;
    }
}
?>
<style>
    .users-sidebar {
        width: 100%;
        background: #fff;
        border-radius: 16px;
        /* box-shadow: 0 2px 12px rgba(100, 108, 255, 0.08); */
        padding: 18px 0 18px 0;
        /* min-height: 60vh; */
        margin-bottom: 24px;
        /* border: 1px solid #e3e3e3; */
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .users-sidebar-title {
        font-size: 15px;
        font-weight: 600;
        color: #888;
        margin: 0 0 12px 24px;
        letter-spacing: 0.5px;
    }

    .users-sidebar-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .users-sidebar-link {
        display: flex;
        align-items: center;
        padding: 10px 24px;
        color: #232360;
        font-size: 14px;
        font-weight: 500;
        border-left: 3px solid transparent;
        border-radius: 8px 0 0 8px;
        text-decoration: none;
        transition: background 0.15s, color 0.15s, border 0.15s;
        margin-bottom: 2px;
    }

    .users-sidebar-link:hover,
    .users-sidebar-link.active {
        background: #f0f4ff;
        color: #1976d2;
        border-left: 3px solid #1976d2;
        text-decoration: none;
    }

    .users-sidebar-icon {
        margin-right: 10px;
        font-size: 16px;
        color: #646cff;
        min-width: 18px;
        text-align: center;
    }

    .users-sidebar-empty {
        color: #aaa;
        padding: 10px 24px;
        font-size: 13px;
    }
</style>
<aside class="users-sidebar">
    <ul class="users-sidebar-list">
        <?php if (!empty($modules)): ?>
            <?php foreach ($modules as $mod): ?>
                <li>
                    <a href="index.php?r=<?= htmlspecialchars($controller) ?>/<?= htmlspecialchars($mod['link']) ?>"
                        class="users-sidebar-link" data-module-key="<?= htmlspecialchars($mod['link']) ?>">
                        <?php if (!empty($mod['icon'])): ?>
                            <i class="users-sidebar-icon <?= htmlspecialchars($mod['icon']) ?>"></i>
                        <?php else: ?>
                            <span class="users-sidebar-icon"><i class="fas fa-circle"></i></span>
                        <?php endif; ?>
                        <?= htmlspecialchars($mod['name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
        <?php endif; ?>
    </ul>
</aside>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.users-sidebar-link');
        const activeKey = localStorage.getItem('usersSidebarActiveKey');
        if (activeKey) {
            links.forEach(link => {
                if (link.getAttribute('data-module-key') === activeKey) {
                    link.classList.add('active');
                }
            });
        } else {
            // Fallback: highlight link matching current URL
            // const current = window.location.search.match(/r=([^&]+)/);
            // if (current && current[1]) {
            //     links.forEach(link => {
            //         if (link.getAttribute('data-module-key') === current[1].replace('/dashboard', '')) {
            //             link.classList.add('active');
            //         }
            //     });
            // }
        }
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                localStorage.setItem('usersSidebarActiveKey', this.getAttribute('data-module-key'));
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>