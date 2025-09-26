<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class PharmacyreportingController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['dashboard', 'stock-report', 'sales-report', 'expiry-report', 'inventory-report', 'supplier-report'],
                'rules' => [
                    [
                        'actions' => ['dashboard', 'stock-report', 'sales-report', 'expiry-report', 'inventory-report', 'supplier-report'],
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
                    'stock-report' => ['get'],
                    'sales-report' => ['get'],
                    'expiry-report' => ['get'],
                    'inventory-report' => ['get'],
                    'supplier-report' => ['get'],
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
        // Add role-based access control if needed
        // $allowedRoles = ['pharmacy_admin', 'pharmacist', 'pharmacy_assistant', 'inventory_manager'];

        // if (!in_array($role, $allowedRoles)) {
        //     Yii::$app->session->setFlash('error', 'Access denied. Pharmacy reporting access only.');
        //     return $this->redirect('index.php?r=site/login');
        // }

        return parent::beforeAction($action);
    }

    public function actionDashboard()
    {
        // Define available pharmacy reports
        $reports = [
            [
                'id' => 'stock-report',
                'title' => 'Stock Level Report',
                'description' => 'Current stock levels, low stock alerts, and inventory status',
                'icon' => 'fas fa-boxes-stacked',
                'color' => '#10B981',
                'category' => 'Inventory',
                'url' => 'pharmacyreporting/stock-report',
                'last_generated' => $this->getLastGeneratedDate('stock_report'),
                'frequency' => 'Daily',
                'status' => 'active'
            ],
            [
                'id' => 'sales-report',
                'title' => 'Sales Analytics Report',
                'description' => 'Sales performance, revenue analysis, and top-selling medicines',
                'icon' => 'fas fa-chart-line',
                'color' => '#22C55E',
                'category' => 'Sales',
                'url' => 'pharmacyreporting/sales-report',
                'last_generated' => $this->getLastGeneratedDate('sales_report'),
                'frequency' => 'Weekly',
                'status' => 'active'
            ],
            [
                'id' => 'expiry-report',
                'title' => 'Expiry Alert Report',
                'description' => 'Medicines nearing expiry, expired items, and expiry tracking',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => '#F59E0B',
                'category' => 'Inventory',
                'url' => 'pharmacyreporting/expiry-report',
                'last_generated' => $this->getLastGeneratedDate('expiry_report'),
                'frequency' => 'Daily',
                'status' => 'active'
            ],
            [
                'id' => 'inventory-report',
                'title' => 'Inventory Valuation Report',
                'description' => 'Total inventory value, cost analysis, and asset valuation',
                'icon' => 'fas fa-calculator',
                'color' => '#8B5CF6',
                'category' => 'Financial',
                'url' => 'pharmacyreporting/inventory-report',
                'last_generated' => $this->getLastGeneratedDate('inventory_report'),
                'frequency' => 'Monthly',
                'status' => 'active'
            ],
            [
                'id' => 'supplier-report',
                'title' => 'Supplier Performance Report',
                'description' => 'Supplier delivery times, quality metrics, and purchase analysis',
                'icon' => 'fas fa-truck',
                'color' => '#06B6D4',
                'category' => 'Procurement',
                'url' => 'pharmacyreporting/supplier-report',
                'last_generated' => $this->getLastGeneratedDate('supplier_report'),
                'frequency' => 'Monthly',
                'status' => 'active'
            ],
            [
                'id' => 'movement-report',
                'title' => 'Stock Movement Report',
                'description' => 'Stock in/out movements, transfers, and adjustments tracking',
                'icon' => 'fas fa-exchange-alt',
                'color' => '#EF4444',
                'category' => 'Inventory',
                'url' => 'pharmacyreporting/movement-report',
                'last_generated' => $this->getLastGeneratedDate('movement_report'),
                'frequency' => 'Weekly',
                'status' => 'active'
            ],
            [
                'id' => 'purchase-report',
                'title' => 'Purchase Order Report',
                'description' => 'Purchase orders, supplier performance, and procurement analysis',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#F97316',
                'category' => 'Procurement',
                'url' => 'pharmacyreporting/purchase-report',
                'last_generated' => $this->getLastGeneratedDate('purchase_report'),
                'frequency' => 'Monthly',
                'status' => 'active'
            ],
            [
                'id' => 'category-report',
                'title' => 'Category Analysis Report',
                'description' => 'Performance analysis by medicine categories and therapeutic groups',
                'icon' => 'fas fa-layer-group',
                'color' => '#84CC16',
                'category' => 'Analytics',
                'url' => 'pharmacyreporting/category-report',
                'last_generated' => $this->getLastGeneratedDate('category_report'),
                'frequency' => 'Monthly',
                'status' => 'active'
            ]
        ];

        // Get report statistics
        $stats = $this->getReportStatistics();

        return $this->render('dashboard', [
            'reports' => $reports,
            'stats' => $stats
        ]);
    }

    public function actionStockReport()
    {
        // Generate stock level report
        $this->layout = 'main';
        return $this->render('stock-report');
    }

    public function actionSalesReport()
    {
        // Generate sales analytics report
        $this->layout = 'main';
        return $this->render('sales-report');
    }

    public function actionExpiryReport()
    {
        // Generate expiry alert report
        $this->layout = 'main';
        return $this->render('expiry-report');
    }

    public function actionInventoryReport()
    {
        // Generate inventory valuation report
        $this->layout = 'main';
        return $this->render('inventory-report');
    }

    public function actionSupplierReport()
    {
        // Generate supplier performance report
        $this->layout = 'main';
        return $this->render('supplier-report');
    }

    private function getLastGeneratedDate($reportType)
    {
        // This would typically query a reports_log table
        // For now, return a mock date
        $dates = [
            'stock_report' => '2024-01-15 10:30:00',
            'sales_report' => '2024-01-14 09:15:00',
            'expiry_report' => '2024-01-15 08:45:00',
            'inventory_report' => '2024-01-12 14:20:00',
            'supplier_report' => '2024-01-11 16:00:00',
            'movement_report' => '2024-01-14 11:30:00',
            'purchase_report' => '2024-01-10 13:45:00',
            'category_report' => '2024-01-09 10:00:00'
        ];

        return $dates[$reportType] ?? 'Never';
    }

    private function getReportStatistics()
    {
        try {
            // Get database statistics
            $totalMedicines = Yii::$app->db->createCommand('SELECT COUNT(*) FROM medicines WHERE is_deleted = 0')->queryScalar() ?: 0;
            $totalStock = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE is_deleted = 0')->queryScalar() ?: 0;
            $lowStockItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE status IN ("low_stock", "critical") AND is_deleted = 0')->queryScalar() ?: 0;
            $expiringSoon = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND is_deleted = 0')->queryScalar() ?: 0;

            return [
                'total_medicines' => $totalMedicines,
                'total_stock_items' => $totalStock,
                'low_stock_items' => $lowStockItems,
                'expiring_soon' => $expiringSoon
            ];
        } catch (\Exception $e) {
            // Return default values if database query fails
            return [
                'total_medicines' => 0,
                'total_stock_items' => 0,
                'low_stock_items' => 0,
                'expiring_soon' => 0
            ];
        }
    }
}