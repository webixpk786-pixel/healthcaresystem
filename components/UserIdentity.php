<?php


namespace app\components;

use yii\web\IdentityInterface;

class UserIdentity implements IdentityInterface
{
    public $id;
    public $username;
    public $role;
    public $attributes = [];

    public function __construct($userData)
    {
        $this->id = $userData['id'];
        $this->username = $userData['username'];
        $this->role = $userData['role'];
        $this->attributes = $userData;
    }

    public static function findIdentity($id)
    {
        $userData = \Yii::$app->db->createCommand('SELECT * FROM users WHERE id = :id', [
            ':id' => $id
        ])->queryOne();

        return $userData ? new self($userData) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return true;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function userlogs($action, $location, $description)
    {
        \Yii::$app->db->createCommand(
            'INSERT INTO activity_logs (user_id, action, location, description, ip_address,user_agent, created_at) VALUES 
                (:user_id, :action, :location, :description, :ip_address, :user_agent, :created_at)',
            [
                ':user_id' => \Yii::$app->user->identity->id,
                ':action' => $action,
                ':location' => $location,
                ':description' => $description,
                ':ip_address' =>  \Yii::$app->request->userIP,
                ':user_agent' =>  \Yii::$app->request->userAgent,
                ':created_at' => date('Y-m-d H:i:s')
            ]
        )->execute();
    }
}