<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\db\Query;

class PharmacysalesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $identity = Yii::$app->user->identity;
                            return isset($identity->role) && $identity->role === 'hospital_admin';
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->response->redirect(['site/login'])->send();
            return false;
        }

        $modules = Yii::$app->db->createCommand('SELECT * FROM modules WHERE is_active = 1 AND id_deleted = 0 
        AND parent_id IS NULL AND link = :link', [':link' => Yii::$app->controller->id])->queryOne();

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
        Yii::$app->view->params['parent_id'] = 26;
        Yii::$app->view->params['controller'] = 'pharmacysales';
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionDashboard()
    {
        return $this->render('dashboard');
    }

    public function actionDesignbills()
    {
        return $this->render('designbills');
    }
}