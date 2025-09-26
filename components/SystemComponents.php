<?php

namespace app\components;

use Yii;

class SystemComponents
{
    public function userlogs($action, $location, $description)
    {
        Yii::$app->db->createCommand(
            'INSERT INTO activity_logs (user_id, action, location, description, ip_address,user_agent, created_at) VALUES 
                (:user_id, :action, :location, :description, :ip_address, :user_agent, :created_at)',
            [
                ':user_id' => Yii::$app->user->identity->id,
                ':action' => $action,
                ':location' => $location,
                ':description' => $description,
                ':ip_address' =>  Yii::$app->request->userIP,
                ':user_agent' =>  Yii::$app->request->userAgent,
                ':created_at' => date('Y-m-d H:i:s')
            ]
        )->execute();
    }
    public function checkModulePermission($moduleName, $showMessage = true)
    {
        $role_id = Yii::$app->user->identity->role_id;

        $permissions = Yii::$app->db->createCommand("
        SELECT (SELECT is_active from modules WHERE id = module_id) as active,
        module_id, can_edit, can_delete, can_create,can_view, can_export
        FROM `role_module_permissions`
        WHERE role_id = :role_id AND module_id IN ($moduleName)
    ")
            ->bindValue(':role_id', $role_id)
            ->queryAll();


        $canView = true;
        $canEdit = false;
        $canDelete = false;
        $canAdd = false;
        $parent_module = isset($permissions[0]) ? $permissions[0] : null;
        $child_modules = isset($permissions[1]) ? $permissions[1] : null;

        if ($child_modules) {
            $canView = $child_modules['can_view'] == 1 && $child_modules['active'] == 1;
            $canEdit = $child_modules['can_edit'] == 1;
            $canDelete = $child_modules['can_delete'] == 1;
            $canAdd = $child_modules['can_create'] == 1;
            $canExport = $child_modules['can_export'] == 1;
        }
        if ($canView) {
            $canView = $parent_module['can_view'] == 1 && $parent_module['active'] == 1;
        }
        if ($canEdit) {
            $canEdit = $parent_module['can_edit'] == 1;
        }

        if ($canDelete) {
            $canDelete = $parent_module['can_delete'] == 1;
        }

        if ($canAdd) {
            $canAdd = $parent_module['can_create'] == 1;
        }

        if ($canExport) {
            $canExport = $parent_module['can_export'] == 1;
        }

        if ($showMessage) {
            $messages = [];
            if (!$canAdd) {
                $messages[] = 'Add';
            }
            if (!$canEdit) {
                $messages[] = 'Edit';
            }
            if (!$canDelete) {
                $messages[] = 'Delete';
            }
            // if (!$canExport) {
            //     $messages[] = 'Export';
            // }
            if (!empty($messages)) {
                $count = count($messages);
                if ($count === 1) {
                    $message = $messages[0];
                } elseif ($count === 2) {
                    $message = $messages[0] . ' and ' . $messages[1];
                } else {
                    $last = array_pop($messages);
                    $message = implode(', ', $messages) . ', and ' . $last;
                }
                $message .= ' permissions are restricted for your role.';
                Yii::$app->view->params['message'] = $message;
                include Yii::$app->basePath . '/views/layouts/restricted.php';
            }
        }

        return [
            'canView' => $canView ? 1 : 0,
            'canAdd' => $canAdd ? 1 : 0,
            'canEdit' => $canEdit ? 1 : 0,
            'canDelete' => $canDelete ? 1 : 0,
            'canExport' => $canExport ? 1 : 0
        ];
    }
}
