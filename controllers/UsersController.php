<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class UsersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['dashboard'],
                'rules' => [
                    [
                        'actions' => ['dashboard'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    // $modules = array(
    //   array('id' => '1','name' => 'Users','link' => 'users','icon' => 'fa fa-users-cog','react_icon' => 'MdPeople','color' => '#e6eaff','parent_id' => NULL,'sort_order' => '1','is_active' => '1','current_status' => '1','description' => 'Manage users, roles, and permissions across the system.','last_updated_at' => '2025-07-23 20:08:54','created_at' => '2025-06-29 19:35:04','updated_at' => '2025-08-09 18:04:45','id_deleted' => '0'),
    //   array('id' => '8','name' => 'Users','link' => 'users','icon' => 'fa fa-user','react_icon' => NULL,'color' => NULL,'parent_id' => '1','sort_order' => '2','is_active' => '1','current_status' => '1','description' => NULL,'last_updated_at' => NULL,'created_at' => '2025-06-29 19:37:01','updated_at' => '2025-07-21 23:51:32','id_deleted' => '0'),
    //   array('id' => '9','name' => 'Roles & Permissions','link' => 'roles','icon' => 'fa fa-user-shield','react_icon' => NULL,'color' => NULL,'parent_id' => '1','sort_order' => '1','is_active' => '1','current_status' => '1','description' => NULL,'last_updated_at' => NULL,'created_at' => '2025-06-29 19:37:01','updated_at' => '2025-08-14 14:51:02','id_deleted' => '0')
    // );
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->response->redirect(['site/login'])->send();
            return false; // Prevents the action from continuing
        }

        $modules = Yii::$app->db->createCommand('SELECT * FROM modules WHERE is_active = 1 AND id_deleted = 0 AND parent_id IS NULL AND link = :link', [':link' => Yii::$app->controller->id])->queryOne();

        if (!$modules) {
            Yii::$app->response->redirect(['site/login'])->send();
            return false;
        }
        $permissions = Yii::$app->db->createCommand(
            'SELECT * FROM role_module_permissions
            WHERE module_id = :module_id AND role_id = :role_id AND can_view = 1',
            [
                ':module_id' => $modules['id'],
                ':role_id' => Yii::$app->user->identity->role_id,
            ]
        )->queryOne();
        if (!$permissions) {
            Yii::$app->response->redirect(['site/login'])->send();
            return false;
        }
        date_default_timezone_set('Asia/Karachi');
        $this->layout = 'sidebar';
        Yii::$app->view->params['parent_id'] = 1;
        Yii::$app->view->params['controller'] = 'users';
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    public function checkPermission($ids)
    {
        $can = Yii::$app->systemcomponent->checkModulePermission($ids, false);

        if ($can['canView'] == 0) {
            $js = "showGlobalAlert1('You do not have permission to view this page.', 'error');";
            Yii::$app->view->registerJs($js, \yii\web\View::POS_END);
            return false;
        }
        return true;
    }

    public function actionDashboard()
    {
        $can = $this->checkPermission('1,2');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        return $this->render('dashboard');
    }


    public function actionUsers()
    {
        $can = $this->checkPermission('1,2');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }

        $roles = Yii::$app->db->createCommand('SELECT * FROM roles WHERE id_deleted = 0')->queryAll();
        $users = Yii::$app->db->createCommand('SELECT *, (select name from roles where id = role_id) as role_name FROM users WHERE id_deleted = 0')->queryAll();
        // SELECT `id`, `role_id`, `username`, `email`, `password_hash`, `auth_key`, `password_reset_token`, `first_name`, `last_name`, `gender`, `dob`, `cnic`, `phone`, `alternate_phone`, `address`, `city`, `country`, `role`, `status`, `profile_image`, `last_login_at`, `created_at`, `updated_at`, `id_deleted` FROM `users` WHERE 1
        return $this->render('users', ['users' => $users, 'roles' => $roles]);
    }
    public function actionRoles()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        $roles = Yii::$app->db->createCommand('SELECT
                    r.*,
                    COUNT(u.id) AS user_count
                FROM roles r
                LEFT JOIN users u ON u.role_id = r.id
                WHERE r.id_deleted = 0
                GROUP BY r.id
                ORDER BY r.id ASC')->queryAll();
        return $this->render('roles', ['roles' => $roles]);
    }
    public function actionPermissions()
    {

        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        $roles = Yii::$app->db->createCommand('SELECT
                    r.*,
                    COUNT(u.id) AS user_count
                FROM roles r
                LEFT JOIN users u ON u.role_id = r.id
                WHERE r.id_deleted = 0
                GROUP BY r.id
                ORDER BY r.name ASC')->queryAll();

        return $this->render('permissions', ['roles' => $roles]);
    }

    public function actionUpdaterole()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $name = Yii::$app->request->post('name');
        $description = Yii::$app->request->post('description');
        if (!$id || !$name) {
            return ['success' => false, 'message' => 'Missing required fields!'];
        }
        $updated = Yii::$app->db->createCommand()->update('roles', [
            'name' => $name,
            'description' => $description,
        ], ['id' => $id])->execute();
        if ($updated) {
            Yii::$app->systemcomponent->userlogs('Update', 'Users', 'Updated role ' . $name);

            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Failed to update role!'];
        }
    }

    public function actionDeleterole()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['success' => false, 'message' => 'Missing role id!'];
        }
        $deleted = Yii::$app->db->createCommand()->update('roles', [
            'id_deleted' => 1
        ], ['id' => $id])->execute();
        if ($deleted) {
            $role = Yii::$app->db->createCommand(
                'SELECT name FROM roles WHERE id = :id'
            )->bindValue(':id', $id)->queryScalar();
            Yii::$app->systemcomponent->userlogs('Delete', 'Users', 'Deleted role ' . $role);

            Yii::$app->session->setFlash('notification', 'Role deleted!');
            return ['success' => true];
        } else {
            Yii::$app->session->setFlash('notification', 'Failed to delete role.');
            return ['success' => false, 'message' => 'Failed to delete role.'];
        }
    }

    public function actionToggleroleactive()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $active = Yii::$app->request->post('active');
        if ($id === null || $active === null) {
            return ['success' => false, 'message' => 'Missing parameters!'];
        }
        $updated = Yii::$app->db->createCommand()->update('roles', [
            'status' => $active
        ], ['id' => $id])->execute();
        if ($updated) {
            $role = Yii::$app->db->createCommand(
                'SELECT name FROM roles WHERE id = :id'
            )->bindValue(':id', $id)->queryScalar();
            Yii::$app->systemcomponent->userlogs('Update', 'Users', 'Updated role status to ' . ($active ? 'Active' : 'Inactive') . ' for role ' . $role);

            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Failed to update status.'];
        }
    }

    public function actionAddrole()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $name = Yii::$app->request->post('name');
        $description = Yii::$app->request->post('description');
        if (!$name) {
            return ['success' => false, 'message' => 'Role name is required!'];
        }
        Yii::$app->db->createCommand()->insert('roles', [
            'name' => $name,
            'description' => $description,
            'status' => 1,
            'id_deleted' => 0
        ])->execute();


        Yii::$app->systemcomponent->userlogs('Create', 'Users', 'Added New Role ' . $name);

        $id = Yii::$app->db->getLastInsertID();
        $role = Yii::$app->db->createCommand('SELECT *, 0 as user_count FROM roles WHERE id = :id')
            ->bindValue(':id', $id)
            ->queryOne();
        return ['success' => true, 'role' => $role];
    }

    public function actionAdduser()
    {
        $can = $this->checkPermission('1,2');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            // Basic validation (add more as needed)
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['username']) || empty($data['password']) || empty($data['role_id'])) {
                return ['success' => false, 'message' => 'Please fill all required fields.'];
            }
            // Hash password
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $auth_key = Yii::$app->security->generateRandomString();
            $now = date('Y-m-d H:i:s');
            $userData = [
                'role_id' => $data['role_id'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => $password_hash,
                'auth_key' => $auth_key,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'gender' => $data['gender'] ?? null,
                'dob' => $data['dob'] ?? null,
                'cnic' => $data['cnic'] ?? null,
                'phone' => $data['phone'] ?? null,
                'alternate_phone' => $data['alternate_phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'role' => $data['role'] ?? null,
                'status' => $data['status'] ?? 1,
                'profile_image' => $data['profile_image'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
                'id_deleted' => $data['id_deleted'] ?? 0,
            ];
            Yii::$app->db->createCommand()->insert('users', $userData)->execute();
            $userId = Yii::$app->db->getLastInsertID();
            $user = Yii::$app->db->createCommand('SELECT * FROM users WHERE id=:id', [':id' => $userId])->queryOne();
            return ['success' => true, 'user' => $user, 'message' => 'User added successfully.'];
        }
        return ['success' => false, 'message' => 'Invalid request.'];
    }

    public function actionUpdateuser($id)
    {
        $can = $this->checkPermission('1,2');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = $request->post();
            if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['username']) || empty($data['role_id'])) {
                return ['success' => false, 'message' => 'Please fill all required fields.'];
            }
            $current = Yii::$app->db->createCommand('SELECT * FROM users WHERE id=:id', [':id' => $id])->queryOne();
            if (!$current) {
                return ['success' => false, 'message' => 'User not found.'];
            }
            $fields = [
                'role_id',
                'username',
                'email',
                'first_name',
                'last_name',
                'gender',
                'dob',
                'cnic',
                'phone',
                'alternate_phone',
                'address',
                'city',
                'country',
                'role',
                'status',
                'profile_image',
                'id_deleted'
            ];
            $userData = [];
            foreach ($fields as $field) {
                if (array_key_exists($field, $data) && ($data[$field] !== '' && $data[$field] !== null)) {
                    $newVal = $data[$field];
                    $oldVal = $current[$field] ?? null;
                    if ($newVal != $oldVal) {
                        $userData[$field] = $newVal;
                    }
                }
            }
            // Password update
            if (isset($data['password']) && $data['password'] !== '') {
                $userData['password_hash'] = $data['password'];
                // $newHash = password_hash($data['password'], PASSWORD_DEFAULT);
                // if ($newHash != $current['password_hash']) {
                //     $userData['password_hash'] = $newHash;
                // }
            }
            // Always update updated_at
            $userData['updated_at'] = date('Y-m-d H:i:s');
            if (count($userData) === 1 && isset($userData['updated_at'])) {
                return ['success' => false, 'message' => 'No changes detected.'];
            }
            Yii::$app->db->createCommand()->update('users', $userData, ['id' => $id])->execute();
            $user = Yii::$app->db->createCommand('SELECT * FROM users WHERE id=:id', [':id' => $id])->queryOne();
            return ['success' => true, 'user' => $user, 'message' => 'User updated successfully.'];
        }
        return ['success' => false, 'message' => 'Invalid request.'];
    }

    public function actionDeleteuser()
    {
        $can = $this->checkPermission('1,2');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['success' => false, 'message' => 'Missing user id!'];
        }
        $deleted = Yii::$app->db->createCommand()->update('users', [
            'id_deleted' => 1
        ], ['id' => $id])->execute();
        if ($deleted) {
            $user = Yii::$app->db->createCommand('SELECT first_name, last_name FROM users WHERE id = :id')->bindValue(':id', $id)->queryOne();
            Yii::$app->systemcomponent->userlogs('Delete', 'Users', 'Deleted User ' . $user['first_name'] . ' ' . $user['last_name']);
            return ['success' => true, 'message' => 'User deleted!'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete user.'];
        }
    }

    public function actionToggleuserstatus()
    {
        $can = $this->checkPermission('1,2');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');
        if (!$id || ($status !== '0' && $status !== '1' && $status !== 0 && $status !== 1)) {
            return ['success' => false, 'message' => 'Missing or invalid parameters!'];
        }
        $updated = Yii::$app->db->createCommand()->update('users', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id])->execute();
        if ($updated) {
            $user = Yii::$app->db->createCommand('SELECT first_name, last_name FROM users WHERE id = :id')->bindValue(':id', $id)->queryOne();
            Yii::$app->systemcomponent->userlogs('Update', 'Users', 'Updated User Status to ' . ($status ? 'Active' : 'Inactive') . ' for User ' . $user['first_name'] . ' ' . $user['last_name']);
            return ['success' => true, 'message' => 'User status updated!'];
        } else {
            return ['success' => false, 'message' => 'Failed to update status.'];
        }
    }

    public function actionModules()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        if (isset($_POST['role_id'])) {
            $role_id = $_POST['role_id'];
        } else {
            return $this->redirect('index.php?r=users/roles');
        }
        $modules = Yii::$app->db->createCommand('SELECT * FROM modules WHERE id_deleted = 0 ORDER BY sort_order ASC, name ASC')->queryAll();
        $roles = Yii::$app->db->createCommand('SELECT * FROM roles WHERE id_deleted = 0')->queryAll();
        $role = Yii::$app->db->createCommand('SELECT * FROM roles WHERE id = :id', [':id' => $role_id])->queryOne();
        $permissions = Yii::$app->db->createCommand('SELECT * FROM role_module_permissions WHERE role_id = :role_id')->bindValue(':role_id', $role_id)->queryAll();
        $permMap = [];
        foreach ($permissions as $perm) {
            $permMap[$perm['module_id']] = $perm;
        }
        return $this->render('modules', [
            'role' => $role,
            'roles' => $roles,
            'modules' => $modules,
            'permissions' => $permMap,
        ]);
    }

    public function actionUpdatemodulepermissions()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $module_id = Yii::$app->request->post('module_id');
        $perms = Yii::$app->request->post('perms'); // array: [role_id => [can_view, can_create, can_edit, can_delete]]
        if (!$module_id || !is_array($perms)) {
            return ['success' => false, 'message' => 'Invalid data.'];
        }
        foreach ($perms as $role_id => $perm) {
            $exists = Yii::$app->db->createCommand('SELECT COUNT(*) FROM role_module_permissions WHERE role_id = :role_id AND module_id = :module_id')
                ->bindValue(':role_id', $role_id)
                ->bindValue(':module_id', $module_id)
                ->queryScalar();
            if ($exists) {
                Yii::$app->db->createCommand()->update('role_module_permissions', [
                    'can_view' => !empty($perm['can_view']) ? 1 : 0,
                    'can_create' => !empty($perm['can_create']) ? 1 : 0,
                    'can_edit' => !empty($perm['can_edit']) ? 1 : 0,
                    'can_delete' => !empty($perm['can_delete']) ? 1 : 0,
                    'can_export' => !empty($perm['can_export']) ? 1 : 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], ['role_id' => $role_id, 'module_id' => $module_id])->execute();
            } else {
                Yii::$app->db->createCommand()->insert('role_module_permissions', [
                    'role_id' => $role_id,
                    'module_id' => $module_id,
                    'can_view' => !empty($perm['can_view']) ? 1 : 0,
                    'can_create' => !empty($perm['can_create']) ? 1 : 0,
                    'can_edit' => !empty($perm['can_edit']) ? 1 : 0,
                    'can_delete' => !empty($perm['can_delete']) ? 1 : 0,
                    'can_export' => !empty($perm['can_export']) ? 1 : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ])->execute();
            }
        }
        $module = Yii::$app->db->createCommand('SELECT name FROM modules WHERE id = :id')->bindValue(':id', $module_id)->queryScalar();
        Yii::$app->systemcomponent->userlogs('Update', 'Modules', "Updated permissions for module $module");
        return ['success' => true, 'message' => 'Permissions updated.'];
    }

    public function actionUpdatemodulepermission()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $module_id = Yii::$app->request->post('module_id');
        $role_id = Yii::$app->request->post('role_id');
        $perm_type = Yii::$app->request->post('perm_type');
        $value = Yii::$app->request->post('value');
        $validPerms = ['can_view', 'can_create', 'can_edit', 'can_delete', 'can_export'];
        if (!$module_id || !$role_id || !in_array($perm_type, $validPerms)) {
            return ['success' => false, 'message' => 'Invalid data.'];
        }
        $exists = Yii::$app->db->createCommand('SELECT COUNT(*) FROM role_module_permissions WHERE role_id = :role_id AND module_id = :module_id')
            ->bindValue(':role_id', $role_id)
            ->bindValue(':module_id', $module_id)
            ->queryScalar();
        if ($exists) {
            Yii::$app->db->createCommand()->update('role_module_permissions', [
                $perm_type => $value ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['role_id' => $role_id, 'module_id' => $module_id])->execute();
        } else {
            $data = [
                'role_id' => $role_id,
                'module_id' => $module_id,
                'can_view' => 0,
                'can_create' => 0,
                'can_edit' => 0,
                'can_delete' => 0,
                'can_export' => 0,
                $perm_type => $value ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            Yii::$app->db->createCommand()->insert('role_module_permissions', $data)->execute();
        }
        $module = Yii::$app->db->createCommand('SELECT name FROM modules WHERE id = :id')->bindValue(':id', $module_id)->queryScalar();
        Yii::$app->systemcomponent->userlogs('Update', 'Modules', "Updated permission for module $module");
        return ['success' => true, 'message' => 'Permission updated.'];
    }

    public function actionTogglemoduleactive()
    {
        $can = $this->checkPermission('1,3');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $is_active = Yii::$app->request->post('is_active');
        if (!$id || ($is_active !== '0' && $is_active !== '1' && $is_active !== 0 && $is_active !== 1)) {
            return ['success' => false, 'message' => 'Missing or invalid parameters!'];
        }
        $updated = Yii::$app->db->createCommand()->update('modules', [
            'is_active' => $is_active,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id])->execute();
        if ($updated) {
            $module = Yii::$app->db->createCommand('SELECT name FROM modules WHERE id = :id')->bindValue(':id', $id)->queryScalar();
            Yii::$app->systemcomponent->userlogs('Update', 'Modules', 'Updated module status to ' . ($is_active ? 'Active' : 'Inactive') . ' for module ' . $module);
            return ['success' => true, 'message' => 'Module status updated!'];
        } else {
            return ['success' => false, 'message' => 'Failed to update module status.'];
        }
    }

    public function actionSystemmodules()
    {
        $can = $this->checkPermission('1,4');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }

        // Fetch all modules from DB
        $modules = Yii::$app->db->createCommand("
            SELECT * FROM modules 
            WHERE id_deleted = 0 
            ORDER BY sort_order ASC, name ASC
        ")->queryAll();

        // Index modules by ID and initialize children
        $moduleMap = [];
        foreach ($modules as $module) {
            $module['children'] = [];
            $moduleMap[$module['id']] = $module;
        }

        // Build hierarchical tree
        $tree = [];
        foreach ($moduleMap as &$module) {
            if ($module['parent_id'] === null) {
                $tree[] = &$module;
            } elseif (isset($moduleMap[$module['parent_id']])) {
                $moduleMap[$module['parent_id']]['children'][] = &$module;
            }
        }
        unset($module); // break reference

        // Recursive function to sort modules by sort_order
        $sortModules = function (&$modules) use (&$sortModules) {
            usort($modules, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);
            foreach ($modules as &$module) {
                if (!empty($module['children'])) {
                    $sortModules($module['children']);
                }
            }
        };
        $sortModules($tree);

        // Optional: Flatten tree into ordered list
        $flattenModules = function ($modules, &$flatList = []) use (&$flattenModules) {
            foreach ($modules as $module) {
                $children = $module['children'];
                unset($module['children']);
                $flatList[] = $module;
                if (!empty($children)) {
                    $flattenModules($children, $flatList);
                }
            }
            return $flatList;
        };
        $orderedModules = $flattenModules($tree);

        // Render view with ordered modules
        return $this->render('systemmodules', [
            'modules' => $orderedModules
        ]);
    }

    public function actionUpdatemodulefield()
    {
        $can = $this->checkPermission('1,4');
        if (!$can) {
            return $this->asJson(['success' => false, 'message' => 'No permission.']);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');
        $allowedFields = ['name', 'color', 'icon', 'link', 'description', 'sort_order'];
        if (!$id || !in_array($field, $allowedFields)) {
            return ['success' => false, 'message' => 'Invalid request.'];
        }
        $updated = Yii::$app->db->createCommand()->update('modules', [
            $field => $value,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id])->execute();
        if ($updated) {
            $module = Yii::$app->db->createCommand('SELECT name FROM modules WHERE id = :id')->bindValue(':id', $id)->queryScalar();
            Yii::$app->systemcomponent->userlogs('Update', 'Modules', "Updated $field for module $module");
            return ['success' => true, 'message' => 'Module updated!'];
        } else {
            return ['success' => false, 'message' => 'Failed to update module.'];
        }
    }

    public function actionAddmodule()
    {
        $can = Yii::$app->systemcomponent->checkModulePermission('1,4', false);
        if (!$can['canAdd']) {
            return $this->asJson(['success' => false, 'message' => 'No permission to add module.']);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $name = trim(Yii::$app->request->post('name'));
        $link = trim(Yii::$app->request->post('link'));
        $description = trim(Yii::$app->request->post('description'));
        $sort_order = Yii::$app->request->post('sort_order');
        $is_active = Yii::$app->request->post('is_active', 1);
        $parent_id = Yii::$app->request->post('parent_id');
        if (!$name) {
            return ['success' => false, 'message' => 'Name is required.'];
        }
        $data = [
            'name' => $name,
            'link' => $link,
            'description' => $description,
            'sort_order' => $sort_order,
            'is_active' => $is_active,
            'parent_id' => $parent_id ? $parent_id : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'id_deleted' => 0
        ];
        $ok = Yii::$app->db->createCommand()->insert('modules', $data)->execute();
        if ($ok) {
            Yii::$app->systemcomponent->userlogs('Create', 'Modules', 'Added module ' . $name);
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Failed to add module.'];
        }
    }

    public function actionUpdatemodulestatus()
    {
        $can = $this->checkPermission('1,4');
        if (!$can) {
            return $this->asJson(['success' => false, 'message' => 'No permission.']);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $current_status = Yii::$app->request->post('current_status');
        if (!$id || !in_array($current_status, ['1', '2', '3'])) {
            return ['success' => false, 'message' => 'Invalid request.'];
        }
        $updated = Yii::$app->db->createCommand()->update('modules', [
            'current_status' => $current_status,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id])->execute();
        if ($updated) {
            $module = Yii::$app->db->createCommand('SELECT name FROM modules WHERE id = :id')->bindValue(':id', $id)->queryScalar();
            $statusText = ['1' => 'Active', '2' => 'Maintenance', '3' => 'Restricted'][$current_status];
            Yii::$app->systemcomponent->userlogs('Update', 'Modules', "Updated module status to $statusText for module ID $module");
            return ['success' => true, 'message' => 'Module status updated!'];
        } else {
            return ['success' => false, 'message' => 'Failed to update module status.'];
        }
    }


    public function actionSettings()
    {

        $can = $this->checkPermission('1,5');
        if (!$can) {
            return $this->render('../layouts/no_permissions');
        }
        return $this->render('settings');
    }

    public function actionChangelanguage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $language = Yii::$app->request->post('language');

        if (!in_array($language, ['en', 'es', 'fr', 'ar'])) {
            return ['success' => false, 'message' => 'Invalid language.'];
        }

        $success = \app\components\LanguageManager::setLanguage($language);

        if ($success) {
            return ['success' => true, 'message' => 'Language changed successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to change language.'];
        }
    }

    public function actionUpdatesetting()
    {
        $can = $this->checkPermission('1,5');
        if (!$can) {
            return $this->asJson(['success' => false, 'message' => 'No permission.']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $key = Yii::$app->request->post('key');
        $value = Yii::$app->request->post('value');

        if (!$key) {
            return ['success' => false, 'message' => 'Setting key is required.'];
        }

        // Check if setting exists
        $exists = Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM system_settings WHERE setting_key = :key'
        )->bindValue(':key', $key)->queryScalar();

        if ($exists) {
            $updated = Yii::$app->db->createCommand()->update('system_settings', [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['setting_key' => $key])->execute();
        } else {
            $updated = Yii::$app->db->createCommand()->insert('system_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();
        }

        if ($updated) {
            Yii::$app->systemcomponent->userlogs('Update', 'Settings', "Updated setting: $key = $value");
            return ['success' => true, 'message' => 'Setting updated successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to update setting.'];
        }
    }

    public function actionUploadlogo()
    {
        $can = $this->checkPermission('1,5');
        if (!$can) {
            return $this->asJson(['success' => false, 'message' => 'No permission.']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!isset($_FILES['system_logo'])) {
            return ['success' => false, 'message' => 'No file uploaded.'];
        }

        $file = $_FILES['system_logo'];

        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error: ' . $file['error']];
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
        }

        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File size must be less than 2MB.'];
        }

        // Create images directory if it doesn't exist
        $uploadDir = Yii::getAlias('@webroot/images/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate filename with extension
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'systemlogo.' . $extension;
        $filepath = $uploadDir . $filename;

        // Remove old logo if exists
        $oldFiles = glob($uploadDir . 'systemlogo.*');
        foreach ($oldFiles as $oldFile) {
            unlink($oldFile);
        }

        // Upload new file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database setting
            $exists = Yii::$app->db->createCommand(
                'SELECT COUNT(*) FROM system_settings WHERE setting_key = :key'
            )->bindValue(':key', 'system_logo')->queryScalar();

            if ($exists) {
                Yii::$app->db->createCommand()->update('system_settings', [
                    'setting_value' => $filename,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['setting_key' => 'system_logo'])->execute();
            } else {
                Yii::$app->db->createCommand()->insert('system_settings', [
                    'setting_key' => 'system_logo',
                    'setting_value' => $filename,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ])->execute();
            }

            Yii::$app->systemcomponent->userlogs('Upload', 'Settings', "Uploaded system logo: $filename");
            return ['success' => true, 'message' => 'Logo uploaded successfully!', 'filename' => $filename];
        } else {
            return ['success' => false, 'message' => 'Failed to save file.'];
        }
    }

    public function actionUploadprofileimage()
    {
        $can = $this->checkPermission('1,2');
        if (!$can) {
            return $this->asJson(['success' => false, 'message' => 'No permission.']);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!isset($_FILES['profile_image'])) {
            return ['success' => false, 'message' => 'No file uploaded.'];
        }

        $file = $_FILES['profile_image'];
        $userId = Yii::$app->request->post('user_id');

        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error: ' . $file['error']];
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
        }

        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File size must be less than 2MB.'];
        }

        // Create profile_images directory if it doesn't exist
        $uploadDir = Yii::getAlias('@webroot/images/profile_images/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate filename with extension
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Remove old profile image if exists
        $oldImage = Yii::$app->db->createCommand(
            'SELECT profile_image FROM users WHERE id = :id'
        )->bindValue(':id', $userId)->queryScalar();

        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }

        // Upload new file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $updated = Yii::$app->db->createCommand()->update('users', [
                'profile_image' => $filename,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $userId])->execute();

            if ($updated) {
                $user = Yii::$app->db->createCommand(
                    'SELECT first_name, last_name FROM users WHERE id = :id'
                )->bindValue(':id', $userId)->queryOne();

                Yii::$app->systemcomponent->userlogs('Upload', 'Users', "Uploaded profile image for user: " . $user['first_name'] . ' ' . $user['last_name']);
                return ['success' => true, 'message' => 'Profile image uploaded successfully!', 'filename' => $filename];
            } else {
                return ['success' => false, 'message' => 'Failed to update database.'];
            }
        } else {
            return ['success' => false, 'message' => 'Failed to save file.'];
        }
    }

    public function actionActivityLogs()
    {
        sleep(0);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $search = $request->get('search', '');
        $month = $request->get('month', '');
        $location = $request->get('location', '');
        $query = (new \yii\db\Query())
            ->select(['al.id', 'al.user_id', 'u.first_name', 'u.last_name', 'al.action', 'al.location', 'al.description', 'al.created_at'])
            ->from(['al' => 'activity_logs'])
            ->leftJoin(['u' => 'users'], 'al.user_id = u.id')
            ->where(['al.is_deleted' => 0]);
        if ($search) {
            $query->andWhere([
                'or',
                ['like', 'al.action', $search],
                ['like', 'al.location', $search],
                ['like', 'al.description', $search],
                ['like', 'u.first_name', $search],
                ['like', 'u.last_name', $search],
            ]);
        }
        if ($month) {
            $query->andWhere(['like', 'al.created_at', $month]); // month in format YYYY-MM
        }
        if (!empty($location)) {
            if (is_array($location)) {
                $query->andWhere(['in', 'al.location', $location]);
            } else {
                $query->andWhere(['al.location' => $location]);
            }
        }
        $query->orderBy(['al.created_at' => SORT_DESC]);
        $logs = $query->limit(100)->all();
        return ['success' => true, 'logs' => $logs];
    }

    public function actionGetmodules()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $systemType = Yii::$app->request->post('system_type', 'both');
        $role = Yii::$app->user->identity->role ?? null;
        $roleId = Yii::$app->db->createCommand('SELECT id FROM roles WHERE link = :role AND id_deleted = 0', [':role' => $role])->queryScalar();

        if (!$roleId) {
            return ['success' => false, 'message' => 'Invalid user role.'];
        }

        // Base query for modules
        $query = '
            SELECT m.name, m.link, m.id, m.description, m.icon
            FROM modules m
            INNER JOIN role_module_permissions rmp ON rmp.module_id = m.id
            WHERE m.parent_id IS NULL
            AND rmp.role_id = :roleId
            AND rmp.can_view = 1
            AND m.is_active = 1
            AND m.id_deleted = 0
            AND (m.type = :systemType OR m.type = "both")
        ';


        $query .= ' ORDER BY m.sort_order ASC, m.id ASC';

        $modules = Yii::$app->db->createCommand($query, [':roleId' => $roleId, ':systemType' => $systemType])->queryAll();

        return ['success' => true, 'modules' => $modules];
    }

    public function actionGetactivemodule()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $activeModule = Yii::$app->db->createCommand('SELECT setting_value FROM system_settings WHERE setting_key = "active_module"')->queryScalar();

        if ($activeModule) {
            $moduleDetails = Yii::$app->db->createCommand('
                SELECT name, link FROM modules WHERE link = :link AND id_deleted = 0
            ', [':link' => $activeModule])->queryOne();

            return ['success' => true, 'module' => $moduleDetails];
        }

        return ['success' => false, 'message' => 'No active module found.'];
    }
}
