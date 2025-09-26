<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'get'],
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
        if (in_array($action->id, ['login'])) {
            $this->enableCsrfValidation = false;
        }
        // if (!Yii::$app->session->has('user_array')) {
        //     if ((Yii::$app->controller->id != 'site' && $action->id != 'login') || (Yii::$app->controller->id . '/' . $action->id == 'site/index')) {
        //         Yii::$app->view->params['no_session'] = true;
        //         return $this->render('login', ['no_session' => true]);
        //     }
        // }

        return parent::beforeAction($action);
    }



    public function actionIndex()
    {

        $role = Yii::$app->user->identity->role ?? null;
        return $this->redirectByRole($role);
    }


    public function actionLogin()
    {
        date_default_timezone_set('Asia/Karachi');
        $this->layout = false;
        $session = Yii::$app->session;

        if (!Yii::$app->user->isGuest) {
            return $this->redirectByRole(Yii::$app->user->identity->role ?? null);
        }

        if (Yii::$app->request->isPost) {
            $username = Yii::$app->request->post('username');
            $password = Yii::$app->request->post('password');

            $userData = Yii::$app->db->createCommand('SELECT * FROM users WHERE username = :username', [
                ':username' => $username
            ])->queryOne();

            if ($userData) {
                if ($password == $userData['password_hash']) {
                    $identity = new \app\components\UserIdentity($userData);
                    // Yii::$app->user->login($identity, 3600 * 24 * 01);
                    Yii::$app->user->login($identity,   300);
                    $session->setFlash('notification', 'Login successful!');
                    Yii::$app->db->createCommand(
                        'UPDATE users SET last_login_at = :last_login_at WHERE id = :id',
                        [
                            ':last_login_at' => date('Y-m-d H:i:s'),
                            ':id' => $userData['id']
                        ]
                    )->execute();
                    Yii::$app->systemcomponent->userlogs('Login', 'System', 'logged in');
                    $role = $userData['role'] ?? null;
                    switch ($role) {
                        case 'hospital_admin':
                            return $this->redirect('index.php?r=hospital/dashboard');
                        case 'doctor':
                            return $this->redirect('index.php?r=doctor/dashboard');
                        case 'nurse':
                            return $this->redirect('index.php?r=nurse/dashboard');
                        case 'receptionist':
                            return $this->redirect('index.php?r=receptionist/dashboard');
                        case 'lab_technician':
                            return $this->redirect('index.php?r=lab_technician/dashboard');
                        case 'radiologist':
                            return $this->redirect('index.php?r=radiologist/dashboard');
                        case 'surgeon':
                            return $this->redirect('index.php?r=surgeon/dashboard');
                        case 'billing_staff':
                            return $this->redirect('index.php?r=billing_staff/dashboard');
                        case 'patient':
                            return $this->redirect('index.php?r=patient/dashboard');
                        case 'pharmacist':
                            return $this->redirect('index.php?r=pharmacist/dashboard');
                        case 'pharmacy_admin':
                            return $this->redirect('index.php?r=pharmacy_admin/dashboard');
                        case 'pharmacy_assistant':
                            return $this->redirect('index.php?r=pharmacy_assistant/dashboard');
                        case 'inventory_manager':
                            return $this->redirect('index.php?r=inventory_manager/dashboard');
                        case 'cashier':
                            return $this->redirect('index.php?r=cashier/dashboard');
                        case 'customer':
                            return $this->redirect('index.php?r=customer/dashboard');
                        case 'delivery_staff':
                            return $this->redirect('index.php?r=delivery_staff/dashboard');
                        default:
                            return $this->redirect('index.php?r=site/login');
                    }
                }
            }

            $session->setFlash('error', 'Invalid username or password.');
            return $this->refresh();
        }

        return $this->render('login');
    }

    protected function redirectByRole($role)
    {

        switch ($role) {
            case 'hospital_admin':

                return $this->redirect('index.php?r=hospital/dashboard');
            case 'doctor':
                return $this->redirect('index.php?r=doctor/dashboard');
            case 'nurse':
                return $this->redirect('index.php?r=nurse/dashboard');
            case 'receptionist':
                return $this->redirect('index.php?r=receptionist/dashboard');
            case 'lab_technician':
                return $this->redirect('index.php?r=lab_technician/dashboard');
            case 'radiologist':
                return $this->redirect('index.php?r=radiologist/dashboard');
            case 'surgeon':
                return $this->redirect('index.php?r=surgeon/dashboard');
            case 'billing_staff':
                return $this->redirect('index.php?r=billing_staff/dashboard');
            case 'patient':
                return $this->redirect('index.php?r=patient/dashboard');
            case 'pharmacist':
                return $this->redirect('index.php?r=pharmacist/dashboard');
            case 'pharmacy_admin':
                return $this->redirect('index.php?r=pharmacy_admin/dashboard');
            case 'pharmacy_assistant':
                return $this->redirect('index.php?r=pharmacy_assistant/dashboard');
            case 'inventory_manager':
                return $this->redirect('index.php?r=inventory_manager/dashboard');
            case 'cashier':
                return $this->redirect('index.php?r=cashier/dashboard');
            case 'customer':
                return $this->redirect('index.php?r=customer/dashboard');
            case 'delivery_staff':
                return $this->redirect('index.php?r=delivery_staff/dashboard');
            default:
                return $this->redirect('index.php?r=site/login');
        }
    }
    public function actionLogout()
    {
        date_default_timezone_set('Asia/Karachi');
        Yii::$app->systemcomponent->userlogs('Logout', 'System', 'logged out');

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionThreadingtest()
    {

        // If not AJAX, redirect or render as needed
        if (!\Yii::$app->request->isAjax) {
            return $this->goHome();
        }
    }

    public function actionAggrid()
    {
        $this->layout = false;
        return $this->render('aggrid');
    }
}
