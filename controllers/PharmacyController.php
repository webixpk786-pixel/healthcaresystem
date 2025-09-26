<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class PharmacyController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['dashboard'], // add actions that require access control
                'rules' => [
                    [
                        'actions' => ['dashboard'],
                        'allow' => true,
                        'roles' => ['@'], // authenticated users only
                        'matchCallback' => function ($rule, $action) {
                            $identity = Yii::$app->user->identity;
                            return true;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'dashboard' => ['get'],
                ],
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
        $role = Yii::$app->user->identity->role ?? null;
        // $allowedRoles = ['pharmacy_admin', 'pharmacist', 'pharmacy_assistant', 'inventory_manager'];

        // if (!in_array($role, $allowedRoles)) {
        //     Yii::$app->session->setFlash('error', 'Access denied. Pharmacy access only.');
        //     return $this->redirect('index.php?r=site/login');
        // }

        return parent::beforeAction($action);
    }

    public function actionDashboard()
    {
        return $this->render('dashboard');
    }
}
