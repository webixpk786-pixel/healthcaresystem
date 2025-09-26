<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\db\Query;

class InventoryController extends Controller
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
                'actions' => [
                    'logout' => ['post'],
                    'create-purchase-order' => ['post'],
                    'update-order-status' => ['post'],
                    'delete-order' => ['post'],
                    'add-medicine' => ['post'],
                    'update-medicine' => ['post'],
                    'delete-medicine' => ['post'],
                    'add-stock' => ['post'],
                    'update-stock' => ['post'],
                    'add-stock-quantity' => ['post'],
                    'transfer-stock' => ['post'],
                    'delete-stock' => ['post'],
                    'bulk-update-stock' => ['post'],
                    'update-stock-status' => ['post'],
                    'bulk-transfer-stock' => ['post'],
                    'get-suppliers' => ['post'],
                    'get-medicines' => ['get'],
                    'get-order-details' => ['get'],
                    // Configuration management actions
                    'add-category' => ['post'],
                    'update-category' => ['post'],
                    'delete-category' => ['post'],
                    'add-supplier' => ['post'],
                    'update-supplier' => ['post'],
                    'delete-supplier' => ['post'],
                    'add-manufacturer' => ['post'],
                    'update-manufacturer' => ['post'],
                    'delete-manufacturer' => ['post'],
                    'add-medicine-form' => ['post'],
                    'update-medicine-form' => ['post'],
                    'delete-medicine-form' => ['post'],
                    'get-configurations' => ['get'],
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
        if (Yii::$app->user->isGuest) {
            Yii::$app->response->redirect(['site/login'])->send();
            return false;
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
        Yii::$app->view->params['parent_id'] = 25;
        Yii::$app->view->params['controller'] = 'inventory';
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionDashboard()
    {
        // Get comprehensive dashboard data
        $totalItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM medicines WHERE is_deleted = 0')->queryScalar();
        $lowStockItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE status IN ("low_stock", "critical") AND is_deleted = 0')->queryScalar();
        $totalValue = Yii::$app->db->createCommand('SELECT SUM(quantity * purchase_price) FROM stock WHERE is_deleted = 0')->queryScalar() ?: 0;
        $pendingOrders = Yii::$app->db->createCommand('SELECT COUNT(*) FROM purchase_orders WHERE status = "pending" AND is_deleted = 0')->queryScalar();
        $expiringSoon = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND is_deleted = 0')->queryScalar();

        // Get recent stock movements
        $recentMovements = Yii::$app->db->createCommand('
            SELECT 
                sm.*,
                m.name as medicine_name,
                m.strength,
                m.form,
                l1.name as from_location,
                l2.name as to_location,
                u.username as created_by_name
            FROM stock_movements sm
            LEFT JOIN stock s ON sm.stock_id = s.id
            LEFT JOIN medicines m ON s.medicine_id = m.id
            LEFT JOIN locations l1 ON sm.from_location_id = l1.id
            LEFT JOIN locations l2 ON sm.to_location_id = l2.id
            LEFT JOIN users u ON sm.created_by = u.id
            WHERE sm.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ORDER BY sm.created_at DESC
            LIMIT 10
        ')->queryAll();

        // Get low stock items with details
        $lowStockDetails = Yii::$app->db->createCommand('
            SELECT 
                s.*,
                m.name as medicine_name,
                m.strength,
                m.form,
                l.name as location_name
            FROM stock s
            LEFT JOIN medicines m ON s.medicine_id = m.id
            LEFT JOIN locations l ON s.location_id = l.id
            WHERE s.status IN ("low_stock", "critical") AND s.is_deleted = 0
            ORDER BY s.quantity ASC
            LIMIT 10
        ')->queryAll();

        // Get expiring items
        $expiringItems = Yii::$app->db->createCommand('
            SELECT 
                s.*,
                m.name as medicine_name,
                m.strength,
                m.form,
                l.name as location_name,
                DATEDIFF(s.expiry_date, CURDATE()) as days_to_expiry
            FROM stock s
            LEFT JOIN medicines m ON s.medicine_id = m.id
            LEFT JOIN locations l ON s.location_id = l.id
            WHERE s.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
            AND s.expiry_date > CURDATE() 
            AND s.is_deleted = 0
            ORDER BY s.expiry_date ASC
            LIMIT 10
        ')->queryAll();

        // Get inventory analytics data for chart
        $analyticsData = Yii::$app->db->createCommand('
            SELECT 
                DATE(created_at) as date,
                SUM(CASE WHEN movement_type = "in" THEN quantity ELSE 0 END) as stock_in,
                SUM(CASE WHEN movement_type = "out" THEN quantity ELSE 0 END) as stock_out
            FROM stock_movements 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ')->queryAll();

        // Get top categories by value
        $topCategories = Yii::$app->db->createCommand('
            SELECT 
                c.id,
                c.name as category_name,
                COUNT(*) as item_count,
                SUM(s.quantity * s.purchase_price) as total_value
            FROM medicines m
            LEFT JOIN categories c ON m.category_id = c.id
            LEFT JOIN stock s ON m.id = s.medicine_id
            WHERE m.is_deleted = 0 AND s.is_deleted = 0
            GROUP BY c.id
            ORDER BY total_value DESC
            LIMIT 5
        ')->queryAll();

        // Get system statistics
        $systemStats = [
            'totalSuppliers' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM suppliers WHERE is_deleted = 0')->queryScalar(),
            'totalLocations' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM locations WHERE is_deleted = 0')->queryScalar(),
            'totalMovements' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock_movements WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)')->queryScalar(),
            'avgStockValue' => Yii::$app->db->createCommand('SELECT AVG(quantity * purchase_price) FROM stock WHERE is_deleted = 0')->queryScalar() ?: 0,
        ];

        $dashboardData = [
            'totalItems' => $totalItems,
            'lowStockItems' => $lowStockItems,
            'totalValue' => $totalValue,
            'pendingOrders' => $pendingOrders,
            'expiringSoon' => $expiringSoon,
            'recentMovements' => $recentMovements,
            'lowStockDetails' => $lowStockDetails,
            'expiringItems' => $expiringItems,
            'analyticsData' => $analyticsData,
            'topCategories' => $topCategories,
            'systemStats' => $systemStats,
        ];

        return $this->render('dashboard', [
            'dashboardData' => $dashboardData
        ]);
    }

    public function actionMedicinescatalog()
    {
        $medicines = Yii::$app->db->createCommand('
            SELECT medicines.*, categories.name as category FROM medicines 
            LEFT JOIN categories ON medicines.category_id = categories.id
            WHERE medicines.is_deleted = 0  AND categories.is_deleted = 0 AND medicines.is_deleted = 0 AND categories.is_deleted = 0
            ORDER BY medicines.name ASC
        ')->queryAll();

        // Get unique categories from medicines
        $categories = Yii::$app->db->createCommand('
            SELECT * FROM categories
            WHERE is_deleted = 0 AND is_deleted = 0
            ORDER BY name ASC
        ')->queryAll();

        return $this->render('medicines-catalog', [
            'medicines' => $medicines,
            'categories' => $categories
        ]);
    }

    public function actionStockmanagement()
    {
        $medicines = Yii::$app->db->createCommand('
            SELECT * FROM medicines
            WHERE is_deleted = 0 AND is_deleted = 0
            ORDER BY name ASC
        ')->queryAll();

        $locations = Yii::$app->db->createCommand('
            SELECT * FROM locations
            WHERE is_deleted = 0 AND status = "active" AND is_deleted = 0
            ORDER BY name ASC
        ')->queryAll();

        $stockItems = Yii::$app->db->createCommand('
            SELECT s.*, m.name as medicine_name, m.generic_name, m.manufacturer, m.strength, m.form,
                   sup.name as supplier_name, l.name as location, l.type as location_type
            FROM stock s
            LEFT JOIN medicines m ON s.medicine_id = m.id
            LEFT JOIN suppliers sup ON s.supplier_id = sup.id
            LEFT JOIN locations l ON s.location_id = l.id
            WHERE s.is_deleted = 0 AND m.is_deleted = 0 AND sup.is_deleted = 0 AND l.is_deleted = 0
            ORDER BY s.updated_at DESC
        ')->queryAll();

        $lowStockItems = array_filter($stockItems, function ($item) {
            return $item['status'] === 'low_stock' || $item['status'] === 'critical';
        });

        return $this->render('stock-management', [
            'stockItems' => $stockItems,
            'lowStockItems' => $lowStockItems,
            'medicines' => $medicines,
            'locations' => $locations
        ]);
    }

    public function actionExpiryalert()
    {
        $expiryAlerts = Yii::$app->db->createCommand('
            SELECT s.*, m.name as medicine_name, m.generic_name, sup.name as supplier_name,
                   DATEDIFF(s.expiry_date, CURDATE()) as days_left
            FROM stock s
            LEFT JOIN medicines m ON s.medicine_id = m.id
            LEFT JOIN suppliers sup ON s.supplier_id = sup.id
            WHERE s.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY) 
            AND s.is_deleted = 0 AND m.is_deleted = 0 AND sup.is_deleted = 0
            ORDER BY s.expiry_date ASC
        ')->queryAll();

        return $this->render('expiry-alert', [
            'expiryAlerts' => $expiryAlerts
        ]);
    }

    public function actionPurchaseorders()
    {
        $purchaseOrders = Yii::$app->db->createCommand('
            SELECT po.*, s.name as supplier_name, s.contact_person, s.phone as supplier_phone,
                   u1.first_name as created_by_name, u2.first_name as approved_by_name,
                   COUNT(poi.id) as item_count
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.id
            LEFT JOIN users u1 ON po.created_by = u1.id
            LEFT JOIN users u2 ON po.approved_by = u2.id
            LEFT JOIN purchase_order_items poi ON po.id = poi.purchase_order_id AND poi.is_deleted = 0
            WHERE po.is_deleted = 0 
            AND s.is_deleted = 0 
            AND (u1.id_deleted = 0 OR u1.id_deleted IS NULL)
            AND (u2.id_deleted = 0 OR u2.id_deleted IS NULL)
            AND poi.is_deleted = 0
            GROUP BY po.id
            ORDER BY po.created_at DESC
        ')->queryAll();

        $pendingOrders = array_filter($purchaseOrders, function ($order) {
            return $order['status'] === 'pending';
        });

        return $this->render('purchase-orders', [
            'purchaseOrders' => $purchaseOrders,
            'pendingOrders' => $pendingOrders
        ]);
    }

    // AJAX Actions for dynamic functionality
    public function actionGetMedicines()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $medicines = Yii::$app->db->createCommand('
            SELECT id, name, generic_name, strength, form, manufacturer
            FROM medicines 
            WHERE is_deleted = 0 AND status = "active" AND is_deleted = 0
            ORDER BY name ASC
        ')->queryAll();

        return ['success' => true, 'data' => $medicines];
    }

    public function actionGetSuppliers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $suppliers = Yii::$app->db->createCommand('
            SELECT id, name, contact_person, phone, email
            FROM suppliers 
            WHERE is_deleted = 0 AND status = "active" AND is_deleted = 0
            ORDER BY name ASC
        ')->queryAll();

        return ['success' => true, 'data' => $suppliers];
    }

    public function actionCreatePurchaseOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $supplier_id = Yii::$app->request->post('supplier_id');
            $expected_delivery = Yii::$app->request->post('expected_delivery');
            $notes = Yii::$app->request->post('notes');
            $items = Yii::$app->request->post('items');

            if (!$supplier_id || !$expected_delivery || !$items) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Parse items if it's a JSON string
            if (is_string($items)) {
                $items = json_decode($items, true);
            }

            if (!is_array($items) || empty($items)) {
                return ['success' => false, 'message' => 'No items provided'];
            }

            // Generate order number
            $orderNumber = 'PO-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Create purchase order
            $orderId = Yii::$app->db->createCommand()->insert('purchase_orders', [
                'order_number' => $orderNumber,
                'supplier_id' => $supplier_id,
                'order_date' => date('Y-m-d'),
                'expected_delivery' => $expected_delivery,
                'total_amount' => 0,
                'status' => 'pending',
                'notes' => $notes,
                'created_by' => Yii::$app->user->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();

            $purchaseOrderId = Yii::$app->db->getLastInsertID();
            $totalAmount = 0;

            // Add order items
            foreach ($items as $item) {
                if ($item['medicine_id'] && $item['quantity'] && $item['unit_price']) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $totalAmount += $itemTotal;

                    Yii::$app->db->createCommand()->insert('purchase_order_items', [
                        'purchase_order_id' => $purchaseOrderId,
                        'medicine_id' => $item['medicine_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $itemTotal,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ])->execute();
                }
            }

            // Update total amount
            Yii::$app->db->createCommand()->update('purchase_orders', [
                'total_amount' => $totalAmount,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $purchaseOrderId])->execute();

            // Log activity
            $this->logActivity('Create', 'Purchase Orders', $purchaseOrderId, "Created purchase order: {$orderNumber}");

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Purchase order created successfully',
                'order_id' => $orderNumber
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error creating purchase order: ' . $e->getMessage()];
        }
    }

    public function actionUpdateOrderStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orderId = Yii::$app->request->post('order_id');
        $status = Yii::$app->request->post('status');

        if (!$orderId || !$status) {
            return ['success' => false, 'message' => 'Missing required parameters'];
        }

        try {
            $updateData = [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($status === 'approved') {
                $updateData['approved_by'] = Yii::$app->user->id;
                $updateData['approved_at'] = date('Y-m-d H:i:s');
            }

            Yii::$app->db->createCommand()->update('purchase_orders', $updateData, ['id' => $orderId])->execute();

            // Log activity
            $this->logActivity('Update', 'Purchase Orders', $orderId, "Updated order status to: {$status}");

            return [
                'success' => true,
                'message' => 'Order status updated successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error updating order status: ' . $e->getMessage()];
        }
    }

    public function actionDeleteOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orderId = Yii::$app->request->post('order_id');

        if (!$orderId) {
            return ['success' => false, 'message' => 'Order ID is required'];
        }

        try {
            $transaction = Yii::$app->db->beginTransaction();

            // Soft delete order items
            Yii::$app->db->createCommand()->update('purchase_order_items', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['purchase_order_id' => $orderId])->execute();

            // Soft delete order
            Yii::$app->db->createCommand()->update('purchase_orders', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $orderId])->execute();

            // Log activity
            $this->logActivity('Delete', 'Purchase Orders', $orderId, "Deleted purchase order");

            $transaction->commit();

            return [
                'success' => true,
                'message' => 'Order deleted successfully'
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error deleting order: ' . $e->getMessage()];
        }
    }

    public function actionGetOrderDetails()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $orderId = Yii::$app->request->get('order_id');

        if (!$orderId) {
            return ['success' => false, 'message' => 'Order ID is required'];
        }

        try {
            $order = Yii::$app->db->createCommand('
                SELECT po.*, s.name as supplier_name, s.contact_person, s.phone as supplier_phone,
                       u1.first_name as created_by_name, u2.first_name as approved_by_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                LEFT JOIN users u1 ON po.created_by = u1.id
                LEFT JOIN users u2 ON po.approved_by = u2.id
                WHERE po.id = :id AND po.is_deleted = 0
            ', [':id' => $orderId])->queryOne();

            if (!$order) {
                return ['success' => false, 'message' => 'Order not found'];
            }

            $items = Yii::$app->db->createCommand('
                SELECT poi.*, m.name as medicine_name, m.generic_name, m.strength, m.form
                FROM purchase_order_items poi
                LEFT JOIN medicines m ON poi.medicine_id = m.id
                WHERE poi.purchase_order_id = :id AND poi.is_deleted = 0
            ', [':id' => $orderId])->queryAll();

            return [
                'success' => true,
                'data' => [
                    'order' => $order,
                    'items' => $items
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching order details: ' . $e->getMessage()];
        }
    }

    // Medicine Management Actions (Fixed for correct database structure)
    public function actionAddMedicine()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $name = Yii::$app->request->post('name');
            $generic_name = Yii::$app->request->post('generic_name');
            $category = Yii::$app->request->post('category');
            $manufacturer = Yii::$app->request->post('manufacturer');
            $strength = Yii::$app->request->post('strength');
            $form = Yii::$app->request->post('form');
            $unit_price = Yii::$app->request->post('unit_price');
            $pack_price = Yii::$app->request->post('pack_price');
            $barcode = Yii::$app->request->post('barcode');
            $status = Yii::$app->request->post('status');
            $description = Yii::$app->request->post('description');

            if (!$name || !$generic_name || !$category || !$manufacturer || !$strength || !$form || !$unit_price || !$pack_price || !$status) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if medicine with same name and strength already exists
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM medicines 
                WHERE name = :name AND strength = :strength AND is_deleted = 0
            ', [':name' => $name, ':strength' => $strength])->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Medicine with same name and strength already exists'];
            }

            // Insert new medicine
            $medicineId = Yii::$app->db->createCommand()->insert('medicines', [
                'name' => $name,
                'generic_name' => $generic_name,
                'category' => $category,
                'manufacturer' => $manufacturer,
                'strength' => $strength,
                'form' => $form,
                'unit_price' => $unit_price,
                'pack_price' => $pack_price,
                'barcode' => $barcode,
                'status' => $status,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();

            $newMedicineId = Yii::$app->db->getLastInsertID();

            // Log activity
            $this->logActivity('Create', 'Medicines', $newMedicineId, "Added new medicine: {$name} ({$strength})");

            return [
                'success' => true,
                'message' => 'Medicine added successfully',
                'medicine_id' => $newMedicineId
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error adding medicine: ' . $e->getMessage()];
        }
    }

    public function actionUpdateMedicine()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $name = Yii::$app->request->post('name');
            $generic_name = Yii::$app->request->post('generic_name');
            $category = Yii::$app->request->post('category');
            $manufacturer = Yii::$app->request->post('manufacturer');
            $strength = Yii::$app->request->post('strength');
            $form = Yii::$app->request->post('form');
            $unit_price = Yii::$app->request->post('unit_price');
            $pack_price = Yii::$app->request->post('pack_price');
            $barcode = Yii::$app->request->post('barcode');
            $status = Yii::$app->request->post('status');
            $description = Yii::$app->request->post('description');

            if (!$id || !$name || !$generic_name || !$category || !$manufacturer || !$strength || !$form || !$unit_price || !$pack_price || !$status) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if medicine exists
            $existing = Yii::$app->db->createCommand('
                SELECT id, name FROM medicines 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$existing) {
                return ['success' => false, 'message' => 'Medicine not found'];
            }

            // Check if another medicine with same name and strength already exists
            $duplicate = Yii::$app->db->createCommand('
                SELECT id FROM medicines 
                WHERE name = :name AND strength = :strength AND id != :id AND is_deleted = 0
            ', [':name' => $name, ':strength' => $strength, ':id' => $id])->queryOne();

            if ($duplicate) {
                return ['success' => false, 'message' => 'Another medicine with same name and strength already exists'];
            }

            // Update medicine
            Yii::$app->db->createCommand()->update('medicines', [
                'name' => $name,
                'generic_name' => $generic_name,
                'category_id' => $category,
                'manufacturer' => $manufacturer,
                'strength' => $strength,
                'form' => $form,
                'unit_price' => $unit_price,
                'pack_price' => $pack_price,
                'barcode' => $barcode,
                'status' => $status,
                'description' => $description,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Update', 'Medicines', $id, "Updated medicine: {$name} ({$strength})");

            return [
                'success' => true,
                'message' => 'Medicine updated successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error updating medicine: ' . $e->getMessage()];
        }
    }

    public function actionDeleteMedicine()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');

            if (!$id) {
                return ['success' => false, 'message' => 'Medicine ID is required'];
            }

            // Check if medicine exists
            $medicine = Yii::$app->db->createCommand('
                SELECT id, name, strength FROM medicines 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$medicine) {
                return ['success' => false, 'message' => 'Medicine not found'];
            }

            // Check if medicine is used in any stock records
            $stockCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock 
                WHERE medicine_id = :id AND is_deleted = 0
            ', [':id' => $id])->queryScalar();

            if ($stockCount > 0) {
                return ['success' => false, 'message' => 'Cannot delete medicine. It is being used in stock records.'];
            }

            // Check if medicine is used in any purchase order items
            $orderCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM purchase_order_items poi
                JOIN purchase_orders po ON poi.purchase_order_id = po.id
                WHERE poi.medicine_id = :id AND poi.is_deleted = 0 AND po.is_deleted = 0
            ', [':id' => $id])->queryScalar();

            if ($orderCount > 0) {
                return ['success' => false, 'message' => 'Cannot delete medicine. It is being used in purchase orders.'];
            }

            // Soft delete medicine
            Yii::$app->db->createCommand()->update('medicines', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Delete', 'Medicines', $id, "Deleted medicine: {$medicine['name']} ({$medicine['strength']})");

            return [
                'success' => true,
                'message' => 'Medicine deleted successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error deleting medicine: ' . $e->getMessage()];
        }
    }

    public function actionGetMedicineDetails()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->get('id');

        if (!$id) {
            return ['success' => false, 'message' => 'Medicine ID is required'];
        }

        try {
            $medicine = Yii::$app->db->createCommand('
                SELECT * FROM medicines 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$medicine) {
                return ['success' => false, 'message' => 'Medicine not found'];
            }

            // Log activity
            $this->logActivity('View', 'Medicines', $id, "Viewed medicine details: {$medicine['name']}");

            return [
                'success' => true,
                'data' => $medicine
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching medicine details: ' . $e->getMessage()];
        }
    }

    // Stock Management Actions (keeping existing functionality)
    public function actionAddStock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $medicine_id = Yii::$app->request->post('medicine_id');
            $batch_number = Yii::$app->request->post('batch_number');
            $quantity = Yii::$app->request->post('quantity');
            $unit = Yii::$app->request->post('unit');
            $location_id = Yii::$app->request->post('location');
            $expiry_date = Yii::$app->request->post('expiry_date');
            $min_stock = Yii::$app->request->post('min_stock');
            $max_stock = Yii::$app->request->post('max_stock');
            $purchase_price = Yii::$app->request->post('purchase_price');
            $selling_price = Yii::$app->request->post('selling_price');
            $supplier_id = Yii::$app->request->post('supplier_id');

            if (!$medicine_id || !$batch_number || !$quantity || !$unit || !$location_id || !$expiry_date) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if stock with same medicine, batch, and location already exists
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM stock 
                WHERE medicine_id = :medicine_id AND batch_number = :batch_number 
                AND location_id = :location_id AND is_deleted = 0
            ', [
                ':medicine_id' => $medicine_id,
                ':batch_number' => $batch_number,
                ':location_id' => $location_id
            ])->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Stock with same medicine, batch, and location already exists'];
            }

            // Determine status based on quantity and max_stock
            $status = 'in_stock';
            if ($max_stock > 0) {
                $stockPercentage = ($quantity / $max_stock) * 100;
                if ($stockPercentage <= 30) {
                    $status = 'critical';
                } elseif ($stockPercentage <= 70) {
                    $status = 'low_stock';
                }
            }

            // Insert new stock
            $stockId = Yii::$app->db->createCommand()->insert('stock', [
                'medicine_id' => $medicine_id,
                'batch_number' => $batch_number,
                'quantity' => $quantity,
                'unit' => $unit,
                'location_id' => $location_id,
                'expiry_date' => $expiry_date,
                'min_stock' => $min_stock ?: 0,
                'max_stock' => $max_stock ?: 0,
                'purchase_price' => $purchase_price ?: 0.00,
                'selling_price' => $selling_price ?: 0.00,
                'supplier_id' => $supplier_id,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();

            $newStockId = Yii::$app->db->getLastInsertID();

            // Log stock movement
            $this->logStockMovement($newStockId, 'in', $quantity, 'purchase', null, null, $location_id, 'Initial stock entry');

            // Log activity
            $this->logActivity('Create', 'Stock', $newStockId, "Added new stock: {$batch_number}");

            return [
                'success' => true,
                'message' => 'Stock added successfully',
                'stock_id' => $newStockId
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error adding stock: ' . $e->getMessage()];
        }
    }

    // Helper method to log stock movements
    private function logStockMovement($stockId, $movementType, $quantity, $referenceType = null, $fromLocationId = null, $toLocationId = null, $locationId = null, $notes = null)
    {
        try {
            Yii::$app->db->createCommand()->insert('stock_movements', [
                'stock_id' => $stockId,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocationId,
                'notes' => $notes,
                'created_by' => Yii::$app->user->id,
                'created_at' => date('Y-m-d H:i:s')
            ])->execute();
        } catch (\Exception $e) {
            // Log error but don't break the main operation
            Yii::error('Failed to log stock movement: ' . $e->getMessage());
        }
    }

    // Helper method to log activities
    private function logActivity($action, $location, $recordId = null, $description = null)
    {
        try {
            Yii::$app->db->createCommand()->insert('activity_logs', [
                'user_id' => Yii::$app->user->id,
                'action' => $action,
                'location' => $location,
                'record_id' => $recordId,
                'description' => $description,
                'ip_address' => Yii::$app->request->userIP,
                'user_agent' => Yii::$app->request->userAgent,
                'created_at' => date('Y-m-d H:i:s'),
                'is_deleted' => 0
            ])->execute();
        } catch (\Exception $e) {
            // Log error but don't break the main operation
            Yii::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    // Include all other existing methods from the original controller...
    // (I'll include the rest of the methods to maintain full functionality)

    public function actionUpdateStock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $batch_number = Yii::$app->request->post('batch_number');
            $quantity = Yii::$app->request->post('quantity');
            $location_id = Yii::$app->request->post('location');
            $expiry_date = Yii::$app->request->post('expiry_date');
            $min_stock = Yii::$app->request->post('min_stock');
            $max_stock = Yii::$app->request->post('max_stock');
            $purchase_price = Yii::$app->request->post('purchase_price');

            if (!$id || !$batch_number || !$quantity || !$location_id || !$expiry_date) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if stock exists
            $existing = Yii::$app->db->createCommand('
                SELECT id, quantity, location_id FROM stock 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$existing) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            // Determine status based on quantity and max_stock
            $status = 'in_stock';
            if ($max_stock > 0) {
                $stockPercentage = ($quantity / $max_stock) * 100;
                if ($stockPercentage <= 30) {
                    $status = 'critical';
                } elseif ($stockPercentage <= 70) {
                    $status = 'low_stock';
                }
            }

            // Update stock
            Yii::$app->db->createCommand()->update('stock', [
                'batch_number' => $batch_number,
                'quantity' => $quantity,
                'location_id' => $location_id,
                'expiry_date' => $expiry_date,
                'min_stock' => $min_stock ?: 0,
                'max_stock' => $max_stock ?: 0,
                'purchase_price' => $purchase_price ?: 0.00,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log stock movement if location changed
            if ($existing['location_id'] != $location_id) {
                $this->logStockMovement($id, 'transfer', $quantity, 'transfer', $existing['location_id'], $location_id, null, 'Location transfer');
            }

            // Log activity
            $this->logActivity('Update', 'Stock', $id, "Updated stock: {$batch_number}");

            return [
                'success' => true,
                'message' => 'Stock updated successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error updating stock: ' . $e->getMessage()];
        }
    }

    public function actionAddStockQuantity()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $stock_id = Yii::$app->request->post('stock_id');
            $quantity = Yii::$app->request->post('quantity');
            $reason = Yii::$app->request->post('reason');

            if (!$stock_id || !$quantity) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if stock exists
            $stock = Yii::$app->db->createCommand('
                SELECT id, quantity, max_stock FROM stock 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $stock_id])->queryOne();

            if (!$stock) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            $newQuantity = $stock['quantity'] + $quantity;

            // Determine status based on new quantity and max_stock
            $status = 'in_stock';
            if ($stock['max_stock'] > 0) {
                $stockPercentage = ($newQuantity / $stock['max_stock']) * 100;
                if ($stockPercentage <= 30) {
                    $status = 'critical';
                } elseif ($stockPercentage <= 70) {
                    $status = 'low_stock';
                }
            }

            // Update stock quantity
            Yii::$app->db->createCommand()->update('stock', [
                'quantity' => $newQuantity,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $stock_id])->execute();

            // Log stock movement
            $this->logStockMovement($stock_id, 'in', $quantity, 'adjustment', null, null, null, $reason ?: 'Quantity added');

            // Log activity
            $this->logActivity('Update', 'Stock', $stock_id, "Added {$quantity} units to stock");

            return [
                'success' => true,
                'message' => "Added {$quantity} units successfully"
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error adding quantity: ' . $e->getMessage()];
        }
    }

    public function actionTransferStock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $stock_id = Yii::$app->request->post('stock_id');
            $quantity = Yii::$app->request->post('quantity');
            $to_location_id = Yii::$app->request->post('to_location');
            $reason = Yii::$app->request->post('reason');

            if (!$stock_id || !$quantity || !$to_location_id) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if stock exists
            $stock = Yii::$app->db->createCommand('
                SELECT id, quantity, location_id, max_stock FROM stock 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $stock_id])->queryOne();

            if (!$stock) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            if ($quantity > $stock['quantity']) {
                return ['success' => false, 'message' => 'Transfer quantity exceeds available stock'];
            }

            if ($stock['location_id'] == $to_location_id) {
                return ['success' => false, 'message' => 'Cannot transfer to the same location'];
            }

            $newQuantity = $stock['quantity'] - $quantity;

            // Determine status based on new quantity and max_stock
            $status = 'in_stock';
            if ($stock['max_stock'] > 0) {
                $stockPercentage = ($newQuantity / $stock['max_stock']) * 100;
                if ($stockPercentage <= 30) {
                    $status = 'critical';
                } elseif ($stockPercentage <= 70) {
                    $status = 'low_stock';
                }
            }

            // Update stock
            Yii::$app->db->createCommand()->update('stock', [
                'quantity' => $newQuantity,
                'location_id' => $to_location_id,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $stock_id])->execute();

            // Log stock movement
            $this->logStockMovement($stock_id, 'transfer', $quantity, 'transfer', $stock['location_id'], $to_location_id, null, $reason ?: 'Stock transfer');

            // Log activity
            $this->logActivity('Update', 'Stock', $stock_id, "Transferred {$quantity} units to new location");

            return [
                'success' => true,
                'message' => "Transferred {$quantity} units successfully"
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error transferring stock: ' . $e->getMessage()];
        }
    }

    public function actionGetStockDetails()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->get('id');

        if (!$id) {
            return ['success' => false, 'message' => 'Stock ID is required'];
        }

        try {
            $stock = Yii::$app->db->createCommand('
                SELECT s.*, m.name as medicine_name, m.generic_name, m.manufacturer, m.strength, m.form,
                       sup.name as supplier_name, l.name as location_name, l.type as location_type
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                LEFT JOIN locations l ON s.location_id = l.id
                WHERE s.id = :id AND s.is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$stock) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            // Get stock movements
            $movements = Yii::$app->db->createCommand('
                SELECT sm.*, u.first_name as created_by_name, 
                       fl.name as from_location_name, tl.name as to_location_name
                FROM stock_movements sm
                LEFT JOIN users u ON sm.created_by = u.id
                LEFT JOIN locations fl ON sm.from_location_id = fl.id
                LEFT JOIN locations tl ON sm.to_location_id = tl.id
                WHERE sm.stock_id = :stock_id
                ORDER BY sm.created_at DESC
                LIMIT 10
            ', [':stock_id' => $id])->queryAll();

            return [
                'success' => true,
                'data' => [
                    'stock' => $stock,
                    'movements' => $movements
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching stock details: ' . $e->getMessage()];
        }
    }

    public function actionGetLocations()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $locations = Yii::$app->db->createCommand('
            SELECT id, name, type, description
            FROM locations 
            WHERE is_deleted = 0 AND status = "active"
            ORDER BY name ASC
        ')->queryAll();

        return ['success' => true, 'data' => $locations];
    }

    public function actionDeleteStock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');

            if (!$id) {
                return ['success' => false, 'message' => 'Stock ID is required'];
            }

            // Check if stock exists
            $stock = Yii::$app->db->createCommand('
                SELECT id, batch_number, quantity FROM stock 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$stock) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            // Check if stock has any movements
            $movementCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock_movements 
                WHERE stock_id = :id
            ', [':id' => $id])->queryScalar();

            if ($movementCount > 0) {
                return ['success' => false, 'message' => 'Cannot delete stock with movement history. Consider marking as inactive instead.'];
            }

            // Soft delete stock
            Yii::$app->db->createCommand()->update('stock', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Delete', 'Stock', $id, "Deleted stock: {$stock['batch_number']}");

            return [
                'success' => true,
                'message' => 'Stock deleted successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error deleting stock: ' . $e->getMessage()];
        }
    }

    public function actionGetStockMovements()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $stockId = Yii::$app->request->get('stock_id');
        $limit = Yii::$app->request->get('limit', 50);

        try {
            $query = '
                SELECT sm.*, u.first_name as created_by_name, 
                       fl.name as from_location_name, tl.name as to_location_name,
                       s.batch_number, m.name as medicine_name
                FROM stock_movements sm
                LEFT JOIN users u ON sm.created_by = u.id
                LEFT JOIN locations fl ON sm.from_location_id = fl.id
                LEFT JOIN locations tl ON sm.to_location_id = tl.id
                LEFT JOIN stock s ON sm.stock_id = s.id
                LEFT JOIN medicines m ON s.medicine_id = m.id
                WHERE 1=1
            ';

            $params = [];

            if ($stockId) {
                $query .= ' AND sm.stock_id = :stock_id';
                $params[':stock_id'] = $stockId;
            }

            $query .= ' ORDER BY sm.created_at DESC LIMIT :limit';
            $params[':limit'] = (int)$limit;

            $movements = Yii::$app->db->createCommand($query, $params)->queryAll();

            return [
                'success' => true,
                'data' => $movements
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching stock movements: ' . $e->getMessage()];
        }
    }

    public function actionGetStockSummary()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Get total items
            $totalItems = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock WHERE is_deleted = 0
            ')->queryScalar();

            // Get low stock items
            $lowStockItems = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock 
                WHERE status IN ("low_stock", "critical") AND is_deleted = 0
            ')->queryScalar();

            // Get expiring soon items (within 30 days)
            $expiringSoon = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock 
                WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                AND expiry_date > CURDATE() AND is_deleted = 0
            ')->queryScalar();

            // Get unique locations
            $uniqueLocations = Yii::$app->db->createCommand('
                SELECT COUNT(DISTINCT location_id) FROM stock 
                WHERE is_deleted = 0
            ')->queryScalar();

            // Get total value
            $totalValue = Yii::$app->db->createCommand('
                SELECT SUM(quantity * purchase_price) FROM stock 
                WHERE is_deleted = 0
            ')->queryScalar() ?: 0;

            return [
                'success' => true,
                'data' => [
                    'totalItems' => $totalItems,
                    'lowStockItems' => $lowStockItems,
                    'expiringSoon' => $expiringSoon,
                    'uniqueLocations' => $uniqueLocations,
                    'totalValue' => $totalValue
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching stock summary: ' . $e->getMessage()];
        }
    }

    public function actionBulkUpdateStock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $updates = Yii::$app->request->post('updates');
            $operation = Yii::$app->request->post('operation'); // 'add', 'subtract', 'set'

            if (!$updates || !is_array($updates)) {
                return ['success' => false, 'message' => 'No updates provided'];
            }

            $transaction = Yii::$app->db->beginTransaction();
            $updatedCount = 0;

            foreach ($updates as $update) {
                $stockId = $update['id'];
                $quantity = $update['quantity'];

                if (!$stockId || !$quantity) {
                    continue;
                }

                // Get current stock
                $stock = Yii::$app->db->createCommand('
                    SELECT id, quantity, max_stock FROM stock 
                    WHERE id = :id AND is_deleted = 0
                ', [':id' => $stockId])->queryOne();

                if (!$stock) {
                    continue;
                }

                // Calculate new quantity based on operation
                switch ($operation) {
                    case 'add':
                        $newQuantity = $stock['quantity'] + $quantity;
                        break;
                    case 'subtract':
                        $newQuantity = max(0, $stock['quantity'] - $quantity);
                        break;
                    case 'set':
                        $newQuantity = $quantity;
                        break;
                    default:
                        continue 2;
                }

                // Determine status based on new quantity and max_stock
                $status = 'in_stock';
                if ($stock['max_stock'] > 0) {
                    $stockPercentage = ($newQuantity / $stock['max_stock']) * 100;
                    if ($stockPercentage <= 30) {
                        $status = 'critical';
                    } elseif ($stockPercentage <= 70) {
                        $status = 'low_stock';
                    }
                }

                // Update stock
                Yii::$app->db->createCommand()->update('stock', [
                    'quantity' => $newQuantity,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $stockId])->execute();

                // Log stock movement
                $this->logStockMovement($stockId, 'adjustment', $quantity, 'bulk_update', null, null, null, "Bulk {$operation} operation");

                $updatedCount++;
            }

            $transaction->commit();

            // Log activity
            $this->logActivity('Update', 'Stock', null, "Bulk updated {$updatedCount} stock items");

            return [
                'success' => true,
                'message' => "Successfully updated {$updatedCount} stock items"
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error in bulk update: ' . $e->getMessage()];
        }
    }

    public function actionExportStock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $filters = Yii::$app->request->get();

            $query = '
                SELECT s.*, m.name as medicine_name, m.generic_name, m.manufacturer, m.strength, m.form,
                       sup.name as supplier_name, l.name as location_name, l.type as location_type
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                LEFT JOIN locations l ON s.location_id = l.id
                WHERE s.is_deleted = 0
            ';

            $params = [];

            // Apply filters
            if (!empty($filters['medicine'])) {
                $query .= ' AND m.name LIKE :medicine';
                $params[':medicine'] = '%' . $filters['medicine'] . '%';
            }

            if (!empty($filters['location'])) {
                $query .= ' AND l.name = :location';
                $params[':location'] = $filters['location'];
            }

            if (!empty($filters['status'])) {
                $query .= ' AND s.status = :status';
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['expiry_filter'])) {
                switch ($filters['expiry_filter']) {
                    case 'expiring_soon':
                        $query .= ' AND s.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)';
                        break;
                    case 'expiring_medium':
                        $query .= ' AND s.expiry_date BETWEEN DATE_ADD(CURDATE(), INTERVAL 31 DAY) AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)';
                        break;
                    case 'expiring_later':
                        $query .= ' AND s.expiry_date > DATE_ADD(CURDATE(), INTERVAL 90 DAY)';
                        break;
                }
            }

            $query .= ' ORDER BY s.updated_at DESC';

            $stockItems = Yii::$app->db->createCommand($query, $params)->queryAll();

            // Generate CSV content
            $csvContent = "Medicine,Batch Number,Quantity,Unit,Location,Stock Level,Expiry Date,Status,Supplier,Purchase Price,Selling Price\n";

            foreach ($stockItems as $item) {
                $stockPercentage = $item['max_stock'] > 0 ? ($item['quantity'] / $item['max_stock']) * 100 : 0;
                $csvContent .= sprintf(
                    '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                    $item['medicine_name'],
                    $item['batch_number'],
                    $item['quantity'],
                    $item['unit'],
                    $item['location_name'],
                    round($stockPercentage) . '%',
                    $item['expiry_date'],
                    $item['status'],
                    $item['supplier_name'],
                    $item['purchase_price'],
                    $item['selling_price']
                );
            }

            // Log activity
            $this->logActivity('Export', 'Stock', null, "Exported stock data - " . count($stockItems) . " items");

            return [
                'success' => true,
                'data' => base64_encode($csvContent),
                'filename' => 'stock_export_' . date('Y-m-d_H-i-s') . '.csv',
                'count' => count($stockItems)
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error exporting stock: ' . $e->getMessage()];
        }
    }

    public function actionGetStockAlerts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Low stock alerts
            $lowStockAlerts = Yii::$app->db->createCommand('
                SELECT s.*, m.name as medicine_name, l.name as location_name,
                       ((s.quantity / s.max_stock) * 100) as stock_percentage
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN locations l ON s.location_id = l.id
                WHERE s.is_deleted = 0 AND s.max_stock > 0 
                AND ((s.quantity / s.max_stock) * 100) <= 30
                ORDER BY stock_percentage ASC
            ')->queryAll();

            // Expiry alerts
            $expiryAlerts = Yii::$app->db->createCommand('
                SELECT s.*, m.name as medicine_name, l.name as location_name,
                       DATEDIFF(s.expiry_date, CURDATE()) as days_left
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN locations l ON s.location_id = l.id
                WHERE s.is_deleted = 0 AND s.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                AND s.expiry_date > CURDATE()
                ORDER BY s.expiry_date ASC
            ')->queryAll();

            return [
                'success' => true,
                'data' => [
                    'lowStock' => $lowStockAlerts,
                    'expiry' => $expiryAlerts
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching stock alerts: ' . $e->getMessage()];
        }
    }

    public function actionUpdateStockStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $status = Yii::$app->request->post('status');

            if (!$id || !$status) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if stock exists
            $stock = Yii::$app->db->createCommand('
                SELECT id, batch_number FROM stock 
                WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$stock) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            // Update status
            Yii::$app->db->createCommand()->update('stock', [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Update', 'Stock', $id, "Updated stock status to: {$status}");

            return [
                'success' => true,
                'message' => 'Stock status updated successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error updating stock status: ' . $e->getMessage()];
        }
    }

    public function actionGetStockHistory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $stockId = Yii::$app->request->get('stock_id');
        $limit = Yii::$app->request->get('limit', 100);

        if (!$stockId) {
            return ['success' => false, 'message' => 'Stock ID is required'];
        }

        try {
            $history = Yii::$app->db->createCommand('
                SELECT sm.*, u.first_name as created_by_name, 
                       fl.name as from_location_name, tl.name as to_location_name
                FROM stock_movements sm
                LEFT JOIN users u ON sm.created_by = u.id
                LEFT JOIN locations fl ON sm.from_location_id = fl.id
                LEFT JOIN locations tl ON sm.to_location_id = tl.id
                WHERE sm.stock_id = :stock_id
                ORDER BY sm.created_at DESC
                LIMIT :limit
            ', [':stock_id' => $stockId, ':limit' => (int)$limit])->queryAll();

            return [
                'success' => true,
                'data' => $history
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching stock history: ' . $e->getMessage()];
        }
    }

    public function actionBulkTransferStock()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $transfers = Yii::$app->request->post('transfers');
            $toLocationId = Yii::$app->request->post('to_location_id');
            $reason = Yii::$app->request->post('reason');

            if (!$transfers || !is_array($transfers) || !$toLocationId) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            $transaction = Yii::$app->db->beginTransaction();
            $transferredCount = 0;

            foreach ($transfers as $transfer) {
                $stockId = $transfer['id'];
                $quantity = $transfer['quantity'];

                if (!$stockId || !$quantity) {
                    continue;
                }

                // Check if stock exists
                $stock = Yii::$app->db->createCommand('
                    SELECT id, quantity, location_id, max_stock FROM stock 
                    WHERE id = :id AND is_deleted = 0
                ', [':id' => $stockId])->queryOne();

                if (!$stock) {
                    continue;
                }

                if ($quantity > $stock['quantity']) {
                    continue; // Skip if not enough quantity
                }

                if ($stock['location_id'] == $toLocationId) {
                    continue; // Skip if same location
                }

                $newQuantity = $stock['quantity'] - $quantity;

                // Determine status based on new quantity and max_stock
                $status = 'in_stock';
                if ($stock['max_stock'] > 0) {
                    $stockPercentage = ($newQuantity / $stock['max_stock']) * 100;
                    if ($stockPercentage <= 30) {
                        $status = 'critical';
                    } elseif ($stockPercentage <= 70) {
                        $status = 'low_stock';
                    }
                }

                // Update stock
                Yii::$app->db->createCommand()->update('stock', [
                    'quantity' => $newQuantity,
                    'location_id' => $toLocationId,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $stockId])->execute();

                // Log stock movement
                $this->logStockMovement($stockId, 'transfer', $quantity, 'bulk_transfer', $stock['location_id'], $toLocationId, null, $reason ?: 'Bulk transfer');

                $transferredCount++;
            }

            $transaction->commit();

            // Log activity
            $this->logActivity('Update', 'Stock', null, "Bulk transferred {$transferredCount} stock items");

            return [
                'success' => true,
                'message' => "Successfully transferred {$transferredCount} stock items"
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error in bulk transfer: ' . $e->getMessage()];
        }
    }

    // Expiry Alert Management Actions
    public function actionGetExpiryAlerts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $filters = Yii::$app->request->get();

            // Get alert settings
            $settings = Yii::$app->db->createCommand('
                SELECT * FROM expiry_alert_settings ORDER BY id DESC LIMIT 1
            ')->queryOne();

            if (!$settings) {
                // Create default settings if none exist
                Yii::$app->db->createCommand()->insert('expiry_alert_settings', [
                    'critical_threshold' => 30,
                    'warning_threshold' => 90,
                    'notice_threshold' => 180,
                    'email_notifications' => 1,
                    'sms_notifications' => 0,
                    'daily_reports' => 1,
                    'weekly_reports' => 0,
                    'auto_reorder' => 0,
                    'auto_transfer' => 0,
                    'auto_discount' => 0,
                    'auto_archive' => 0,
                    'created_by' => Yii::$app->user->id,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();

                $settings = [
                    'critical_threshold' => 30,
                    'warning_threshold' => 90,
                    'notice_threshold' => 180
                ];
            }

            $query = '
                SELECT s.*, m.name as medicine_name, m.generic_name, m.manufacturer, m.strength, m.form,
                       sup.name as supplier_name, l.name as location,
                       DATEDIFF(s.expiry_date, CURDATE()) as days_left,
                       CASE 
                           WHEN s.expiry_date <= CURDATE() THEN "expired"
                           WHEN DATEDIFF(s.expiry_date, CURDATE()) <= :critical THEN "critical"
                           WHEN DATEDIFF(s.expiry_date, CURDATE()) <= :warning THEN "warning"
                           WHEN DATEDIFF(s.expiry_date, CURDATE()) <= :notice THEN "notice"
                           ELSE "good"
                       END as status
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                LEFT JOIN locations l ON s.location_id = l.id
                WHERE s.is_deleted = 0
                AND s.expiry_date <= DATE_ADD(CURDATE(), INTERVAL :notice DAY)
            ';

            $params = [
                ':critical' => $settings['critical_threshold'],
                ':warning' => $settings['warning_threshold'],
                ':notice' => $settings['notice_threshold']
            ];

            // Apply filters
            if (!empty($filters['medicine'])) {
                $query .= ' AND (m.name LIKE :medicine OR m.generic_name LIKE :medicine)';
                $params[':medicine'] = '%' . $filters['medicine'] . '%';
            }

            if (!empty($filters['status'])) {
                switch ($filters['status']) {
                    case 'expired':
                        $query .= ' AND s.expiry_date <= CURDATE()';
                        break;
                    case 'critical':
                        $query .= ' AND s.expiry_date > CURDATE() AND DATEDIFF(s.expiry_date, CURDATE()) <= :critical';
                        break;
                    case 'warning':
                        $query .= ' AND DATEDIFF(s.expiry_date, CURDATE()) > :critical AND DATEDIFF(s.expiry_date, CURDATE()) <= :warning';
                        break;
                    case 'notice':
                        $query .= ' AND DATEDIFF(s.expiry_date, CURDATE()) > :warning AND DATEDIFF(s.expiry_date, CURDATE()) <= :notice';
                        break;
                }
            }

            if (!empty($filters['days'])) {
                switch ($filters['days']) {
                    case 'expired':
                        $query .= ' AND s.expiry_date <= CURDATE()';
                        break;
                    case 'critical':
                        $query .= ' AND s.expiry_date > CURDATE() AND DATEDIFF(s.expiry_date, CURDATE()) <= :critical';
                        break;
                    case 'warning':
                        $query .= ' AND DATEDIFF(s.expiry_date, CURDATE()) > :critical AND DATEDIFF(s.expiry_date, CURDATE()) <= :warning';
                        break;
                    case 'notice':
                        $query .= ' AND DATEDIFF(s.expiry_date, CURDATE()) > :warning AND DATEDIFF(s.expiry_date, CURDATE()) <= :notice';
                        break;
                }
            }

            if (!empty($filters['location'])) {
                $query .= ' AND l.name = :location';
                $params[':location'] = $filters['location'];
            }

            if (!empty($filters['batch'])) {
                $query .= ' AND s.batch_number LIKE :batch';
                $params[':batch'] = '%' . $filters['batch'] . '%';
            }

            $query .= ' ORDER BY s.expiry_date ASC';

            $alerts = Yii::$app->db->createCommand($query, $params)->queryAll();

            return [
                'success' => true,
                'data' => $alerts,
                'settings' => $settings
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching expiry alerts: ' . $e->getMessage()];
        }
    }

    public function actionExtendExpiry()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $stockId = Yii::$app->request->post('stock_id');
            $newExpiryDate = Yii::$app->request->post('new_expiry_date');
            $reason = Yii::$app->request->post('reason');

            if (!$stockId || !$newExpiryDate) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if stock exists
            $stock = Yii::$app->db->createCommand('
                SELECT s.*, m.name as medicine_name, m.generic_name
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                WHERE s.id = :id AND s.is_deleted = 0
            ', [':id' => $stockId])->queryOne();

            if (!$stock) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            $oldExpiryDate = $stock['expiry_date'];

            // Update expiry date
            Yii::$app->db->createCommand()->update('stock', [
                'expiry_date' => $newExpiryDate,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $stockId])->execute();

            // Log the action
            Yii::$app->db->createCommand()->insert('expiry_alert_actions', [
                'stock_id' => $stockId,
                'action_type' => 'extend_expiry',
                'old_expiry_date' => $oldExpiryDate,
                'new_expiry_date' => $newExpiryDate,
                'reason' => $reason,
                'handled_by' => Yii::$app->user->id,
                'handled_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ])->execute();

            // Log activity
            $this->logActivity('Update', 'Stock', $stockId, "Extended expiry date for {$stock['medicine_name']} from {$oldExpiryDate} to {$newExpiryDate}");

            return [
                'success' => true,
                'message' => 'Expiry date extended successfully',
                'notification' => 'Expiry date extended successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error extending expiry date: ' . $e->getMessage()];
        }
    }

    public function actionMarkAlertHandled()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $stockId = Yii::$app->request->post('stock_id');
            $reason = Yii::$app->request->post('reason');

            if (!$stockId) {
                return ['success' => false, 'message' => 'Stock ID is required'];
            }

            // Check if stock exists
            $stock = Yii::$app->db->createCommand('
                SELECT s.*, m.name as medicine_name, m.generic_name
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                WHERE s.id = :id AND s.is_deleted = 0
            ', [':id' => $stockId])->queryOne();

            if (!$stock) {
                return ['success' => false, 'message' => 'Stock not found'];
            }

            // Log the action
            Yii::$app->db->createCommand()->insert('expiry_alert_actions', [
                'stock_id' => $stockId,
                'action_type' => 'mark_handled',
                'reason' => $reason,
                'handled_by' => Yii::$app->user->id,
                'handled_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ])->execute();

            // Log activity
            $this->logActivity('Update', 'Stock', $stockId, "Marked expiry alert as handled for {$stock['medicine_name']}");

            return [
                'success' => true,
                'message' => 'Alert marked as handled successfully',
                'notification' => 'Alert marked as handled successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error marking alert as handled: ' . $e->getMessage()];
        }
    }

    public function actionGetAlertSettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $settings = Yii::$app->db->createCommand('
                SELECT * FROM expiry_alert_settings ORDER BY id DESC LIMIT 1
            ')->queryOne();

            if (!$settings) {
                // Create default settings
                Yii::$app->db->createCommand()->insert('expiry_alert_settings', [
                    'critical_threshold' => 30,
                    'warning_threshold' => 90,
                    'notice_threshold' => 180,
                    'email_notifications' => 1,
                    'sms_notifications' => 0,
                    'daily_reports' => 1,
                    'weekly_reports' => 0,
                    'auto_reorder' => 0,
                    'auto_transfer' => 0,
                    'auto_discount' => 0,
                    'auto_archive' => 0,
                    'created_by' => Yii::$app->user->id,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();

                $settings = [
                    'critical_threshold' => 30,
                    'warning_threshold' => 90,
                    'notice_threshold' => 180,
                    'email_notifications' => 1,
                    'sms_notifications' => 0,
                    'daily_reports' => 1,
                    'weekly_reports' => 0,
                    'auto_reorder' => 0,
                    'auto_transfer' => 0,
                    'auto_discount' => 0,
                    'auto_archive' => 0
                ];
            }

            return [
                'success' => true,
                'data' => $settings
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching alert settings: ' . $e->getMessage()];
        }
    }

    public function actionSaveAlertSettings()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $criticalThreshold = Yii::$app->request->post('critical_threshold');
            $warningThreshold = Yii::$app->request->post('warning_threshold');
            $noticeThreshold = Yii::$app->request->post('notice_threshold');
            $emailNotifications = Yii::$app->request->post('email_notifications') ? 1 : 0;
            $smsNotifications = Yii::$app->request->post('sms_notifications') ? 1 : 0;
            $dailyReports = Yii::$app->request->post('daily_reports') ? 1 : 0;
            $weeklyReports = Yii::$app->request->post('weekly_reports') ? 1 : 0;
            $autoReorder = Yii::$app->request->post('auto_reorder') ? 1 : 0;
            $autoTransfer = Yii::$app->request->post('auto_transfer') ? 1 : 0;
            $autoDiscount = Yii::$app->request->post('auto_discount') ? 1 : 0;
            $autoArchive = Yii::$app->request->post('auto_archive') ? 1 : 0;

            if (!$criticalThreshold || !$warningThreshold || !$noticeThreshold) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if settings exist
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM expiry_alert_settings ORDER BY id DESC LIMIT 1
            ')->queryOne();

            $settingsData = [
                'critical_threshold' => $criticalThreshold,
                'warning_threshold' => $warningThreshold,
                'notice_threshold' => $noticeThreshold,
                'email_notifications' => $emailNotifications,
                'sms_notifications' => $smsNotifications,
                'daily_reports' => $dailyReports,
                'weekly_reports' => $weeklyReports,
                'auto_reorder' => $autoReorder,
                'auto_transfer' => $autoTransfer,
                'auto_discount' => $autoDiscount,
                'auto_archive' => $autoArchive,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($existing) {
                // Update existing settings
                Yii::$app->db->createCommand()->update('expiry_alert_settings', $settingsData, ['id' => $existing['id']])->execute();
            } else {
                // Create new settings
                $settingsData['created_by'] = Yii::$app->user->id;
                $settingsData['created_at'] = date('Y-m-d H:i:s');
                Yii::$app->db->createCommand()->insert('expiry_alert_settings', $settingsData)->execute();
            }

            // Log activity
            $this->logActivity('Update', 'Expiry Alert Settings', null, 'Updated expiry alert settings');

            return [
                'success' => true,
                'message' => 'Alert settings saved successfully',
                'notification' => 'Alert settings saved successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error saving alert settings: ' . $e->getMessage()];
        }
    }

    public function actionExportExpiryAlerts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $filters = Yii::$app->request->get();

            // Get alert settings
            $settings = Yii::$app->db->createCommand('
                SELECT * FROM expiry_alert_settings ORDER BY id DESC LIMIT 1
            ')->queryOne();

            if (!$settings) {
                $settings = [
                    'critical_threshold' => 30,
                    'warning_threshold' => 90,
                    'notice_threshold' => 180
                ];
            }

            $query = '
                SELECT s.*, m.name as medicine_name, m.generic_name, m.manufacturer, m.strength, m.form,
                       sup.name as supplier_name, l.name as location,
                       DATEDIFF(s.expiry_date, CURDATE()) as days_left,
                       CASE 
                           WHEN s.expiry_date <= CURDATE() THEN "Expired"
                           WHEN DATEDIFF(s.expiry_date, CURDATE()) <= :critical THEN "Critical"
                           WHEN DATEDIFF(s.expiry_date, CURDATE()) <= :warning THEN "Warning"
                           WHEN DATEDIFF(s.expiry_date, CURDATE()) <= :notice THEN "Notice"
                           ELSE "Good"
                       END as status
                FROM stock s
                LEFT JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                LEFT JOIN locations l ON s.location_id = l.id
                WHERE s.is_deleted = 0
                AND s.expiry_date <= DATE_ADD(CURDATE(), INTERVAL :notice DAY)
            ';

            $params = [
                ':critical' => $settings['critical_threshold'],
                ':warning' => $settings['warning_threshold'],
                ':notice' => $settings['notice_threshold']
            ];

            // Apply same filters as in GetExpiryAlerts
            if (!empty($filters['medicine'])) {
                $query .= ' AND (m.name LIKE :medicine OR m.generic_name LIKE :medicine)';
                $params[':medicine'] = '%' . $filters['medicine'] . '%';
            }

            if (!empty($filters['status'])) {
                switch ($filters['status']) {
                    case 'expired':
                        $query .= ' AND s.expiry_date <= CURDATE()';
                        break;
                    case 'critical':
                        $query .= ' AND s.expiry_date > CURDATE() AND DATEDIFF(s.expiry_date, CURDATE()) <= :critical';
                        break;
                    case 'warning':
                        $query .= ' AND DATEDIFF(s.expiry_date, CURDATE()) > :critical AND DATEDIFF(s.expiry_date, CURDATE()) <= :warning';
                        break;
                    case 'notice':
                        $query .= ' AND DATEDIFF(s.expiry_date, CURDATE()) > :warning AND DATEDIFF(s.expiry_date, CURDATE()) <= :notice';
                        break;
                }
            }

            if (!empty($filters['location'])) {
                $query .= ' AND l.name = :location';
                $params[':location'] = $filters['location'];
            }

            if (!empty($filters['batch'])) {
                $query .= ' AND s.batch_number LIKE :batch';
                $params[':batch'] = '%' . $filters['batch'] . '%';
            }

            $query .= ' ORDER BY s.expiry_date ASC';

            $alerts = Yii::$app->db->createCommand($query, $params)->queryAll();

            // Generate CSV content
            $csvContent = "Medicine Name,Generic Name,Batch Number,Expiry Date,Days Left,Location,Status,Supplier,Quantity,Unit\n";

            foreach ($alerts as $alert) {
                $daysLeft = $alert['days_left'] <= 0 ? 'Expired' : $alert['days_left'] . ' days';
                $csvContent .= sprintf(
                    '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                    $alert['medicine_name'],
                    $alert['generic_name'],
                    $alert['batch_number'],
                    $alert['expiry_date'],
                    $daysLeft,
                    $alert['location'],
                    $alert['status'],
                    $alert['supplier_name'],
                    $alert['quantity'],
                    $alert['unit']
                );
            }

            // Log activity
            $this->logActivity('Export', 'Expiry Alerts', null, "Exported expiry alerts - " . count($alerts) . " items");

            return [
                'success' => true,
                'data' => base64_encode($csvContent),
                'filename' => 'expiry_alerts_' . date('Y-m-d_H-i-s') . '.csv',
                'count' => count($alerts),
                'notification' => 'Expiry alerts exported successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error exporting expiry alerts: ' . $e->getMessage()];
        }
    }

    public function actionGetLocationsForFilter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $locations = Yii::$app->db->createCommand('
                SELECT DISTINCT l.name
                FROM locations l
                INNER JOIN stock s ON l.id = s.location_id
                WHERE l.is_deleted = 0 AND l.status = "active" AND s.is_deleted = 0
                ORDER BY l.name ASC
            ')->queryAll();

            return [
                'success' => true,
                'data' => array_column($locations, 'name')
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching locations: ' . $e->getMessage()];
        }
    }

    public function actionGetAlertStats()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Get alert settings
            $settings = Yii::$app->db->createCommand('
                SELECT * FROM expiry_alert_settings ORDER BY id DESC LIMIT 1
            ')->queryOne();

            if (!$settings) {
                $settings = [
                    'critical_threshold' => 30,
                    'warning_threshold' => 90,
                    'notice_threshold' => 180
                ];
            }

            // Get counts for each category
            $criticalCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock s
                WHERE s.is_deleted = 0 
                AND s.expiry_date > CURDATE() 
                AND DATEDIFF(s.expiry_date, CURDATE()) <= :critical
            ', [':critical' => $settings['critical_threshold']])->queryScalar();

            $warningCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock s
                WHERE s.is_deleted = 0 
                AND DATEDIFF(s.expiry_date, CURDATE()) > :critical 
                AND DATEDIFF(s.expiry_date, CURDATE()) <= :warning
            ', [
                ':critical' => $settings['critical_threshold'],
                ':warning' => $settings['warning_threshold']
            ])->queryScalar();

            $noticeCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock s
                WHERE s.is_deleted = 0 
                AND DATEDIFF(s.expiry_date, CURDATE()) > :warning 
                AND DATEDIFF(s.expiry_date, CURDATE()) <= :notice
            ', [
                ':warning' => $settings['warning_threshold'],
                ':notice' => $settings['notice_threshold']
            ])->queryScalar();

            $expiredCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock s
                WHERE s.is_deleted = 0 
                AND s.expiry_date <= CURDATE()
            ')->queryScalar();

            $totalCount = $criticalCount + $warningCount + $noticeCount + $expiredCount;

            return [
                'success' => true,
                'data' => [
                    'critical' => $criticalCount,
                    'warning' => $warningCount,
                    'notice' => $noticeCount,
                    'expired' => $expiredCount,
                    'total' => $totalCount
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error fetching alert stats: ' . $e->getMessage()];
        }
    }

    public function actionConfigurations()
    {
        // Get all configuration data
        $categories = Yii::$app->db->createCommand('
            SELECT * FROM categories WHERE is_deleted = 0 ORDER BY name ASC
        ')->queryAll();

        $suppliers = Yii::$app->db->createCommand('
            SELECT * FROM suppliers WHERE is_deleted = 0 ORDER BY name ASC
        ')->queryAll();

        $manufacturers = Yii::$app->db->createCommand('
            SELECT * FROM manufacturers WHERE is_deleted = 0 ORDER BY name ASC
        ')->queryAll();

        $medicineForms = Yii::$app->db->createCommand('
            SELECT * FROM medicine_forms WHERE is_deleted = 0 ORDER BY name ASC
        ')->queryAll();

        $expiryStatuses = ['expired', 'critical', 'warning', 'notice', 'good'];
        $purchaseOrderStatuses = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];

        return $this->render('configurations', [
            'categories' => $categories,
            'suppliers' => $suppliers,
            'manufacturers' => $manufacturers,
            'medicineForms' => $medicineForms,
            'expiryStatuses' => $expiryStatuses,
            'purchaseOrderStatuses' => $purchaseOrderStatuses,
        ]);
    }

    // Category Management Actions
    public function actionAddCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $name = Yii::$app->request->post('name');
            $description = Yii::$app->request->post('description', '');

            if (!$name) {
                return ['success' => false, 'message' => 'Category name is required'];
            }

            // Check if category already exists
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM categories WHERE name = :name AND is_deleted = 0
            ', [':name' => $name])->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Category with this name already exists'];
            }

            // Insert new category
            Yii::$app->db->createCommand()->insert('categories', [
                'name' => $name,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();

            $categoryId = Yii::$app->db->getLastInsertID();

            // Log activity
            $this->logActivity('Create', 'Categories', $categoryId, "Added new category: {$name}");

            return [
                'success' => true,
                'message' => 'Category added successfully',
                'category_id' => $categoryId
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error adding category: ' . $e->getMessage()];
        }
    }

    public function actionUpdateCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $name = Yii::$app->request->post('name');
            $description = Yii::$app->request->post('description', '');

            if (!$id || !$name) {
                return ['success' => false, 'message' => 'Category ID and name are required'];
            }

            // Check if category exists
            $existing = Yii::$app->db->createCommand('
                SELECT id, name FROM categories WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$existing) {
                return ['success' => false, 'message' => 'Category not found'];
            }

            // Check if another category with same name exists
            $duplicate = Yii::$app->db->createCommand('
                SELECT id FROM categories WHERE name = :name AND id != :id AND is_deleted = 0
            ', [':name' => $name, ':id' => $id])->queryOne();

            if ($duplicate) {
                return ['success' => false, 'message' => 'Another category with this name already exists'];
            }

            // Update category
            Yii::$app->db->createCommand()->update('categories', [
                'name' => $name,
                'description' => $description,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Update', 'Categories', $id, "Updated category: {$name}");

            return [
                'success' => true,
                'message' => 'Category updated successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error updating category: ' . $e->getMessage()];
        }
    }

    public function actionDeleteCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');

            if (!$id) {
                return ['success' => false, 'message' => 'Category ID is required'];
            }

            // Check if category exists
            $category = Yii::$app->db->createCommand('
                SELECT id, name FROM categories WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$category) {
                return ['success' => false, 'message' => 'Category not found'];
            }

            // Check if category is used in medicines
            $medicineCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM medicines WHERE category_id = :id AND is_deleted = 0
            ', [':id' => $id])->queryScalar();

            if ($medicineCount > 0) {
                return ['success' => false, 'message' => 'Cannot delete category. It is being used in medicines.'];
            }

            // Soft delete category
            Yii::$app->db->createCommand()->update('categories', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Delete', 'Categories', $id, "Deleted category: {$category['name']}");

            return [
                'success' => true,
                'message' => 'Category deleted successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error deleting category: ' . $e->getMessage()];
        }
    }

    // Supplier Management Actions
    public function actionAddSupplier()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $name = Yii::$app->request->post('name');
            $contact_person = Yii::$app->request->post('contact_person', '');
            $phone = Yii::$app->request->post('phone', '');
            $email = Yii::$app->request->post('email', '');
            $address = Yii::$app->request->post('address', '');
            $status = Yii::$app->request->post('status', 'active');

            if (!$name) {
                return ['success' => false, 'message' => 'Supplier name is required'];
            }

            // Check if supplier already exists
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM suppliers WHERE name = :name AND is_deleted = 0
            ', [':name' => $name])->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Supplier with this name already exists'];
            }

            // Insert new supplier
            Yii::$app->db->createCommand()->insert('suppliers', [
                'name' => $name,
                'contact_person' => $contact_person,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();

            $supplierId = Yii::$app->db->getLastInsertID();

            // Log activity
            $this->logActivity('Create', 'Suppliers', $supplierId, "Added new supplier: {$name}");

            return [
                'success' => true,
                'message' => 'Supplier added successfully',
                'supplier_id' => $supplierId
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error adding supplier: ' . $e->getMessage()];
        }
    }

    public function actionUpdateSupplier()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $name = Yii::$app->request->post('name');
            $contact_person = Yii::$app->request->post('contact_person', '');
            $phone = Yii::$app->request->post('phone', '');
            $email = Yii::$app->request->post('email', '');
            $address = Yii::$app->request->post('address', '');
            $status = Yii::$app->request->post('status', 'active');

            if (!$id || !$name) {
                return ['success' => false, 'message' => 'Supplier ID and name are required'];
            }

            // Check if supplier exists
            $existing = Yii::$app->db->createCommand('
                SELECT id, name FROM suppliers WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$existing) {
                return ['success' => false, 'message' => 'Supplier not found'];
            }

            // Check if another supplier with same name exists
            $duplicate = Yii::$app->db->createCommand('
                SELECT id FROM suppliers WHERE name = :name AND id != :id AND is_deleted = 0
            ', [':name' => $name, ':id' => $id])->queryOne();

            if ($duplicate) {
                return ['success' => false, 'message' => 'Another supplier with this name already exists'];
            }

            // Update supplier
            Yii::$app->db->createCommand()->update('suppliers', [
                'name' => $name,
                'contact_person' => $contact_person,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Update', 'Suppliers', $id, "Updated supplier: {$name}");

            return [
                'success' => true,
                'message' => 'Supplier updated successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error updating supplier: ' . $e->getMessage()];
        }
    }

    public function actionDeleteSupplier()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');

            if (!$id) {
                return ['success' => false, 'message' => 'Supplier ID is required'];
            }

            // Check if supplier exists
            $supplier = Yii::$app->db->createCommand('
                SELECT id, name FROM suppliers WHERE id = :id AND is_deleted = 0
            ', [':id' => $id])->queryOne();

            if (!$supplier) {
                return ['success' => false, 'message' => 'Supplier not found'];
            }

            // Check if supplier is used in stock or purchase orders
            $stockCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM stock WHERE supplier_id = :id AND is_deleted = 0
            ', [':id' => $id])->queryScalar();

            $orderCount = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM purchase_orders WHERE supplier_id = :id AND is_deleted = 0
            ', [':id' => $id])->queryScalar();

            if ($stockCount > 0 || $orderCount > 0) {
                return ['success' => false, 'message' => 'Cannot delete supplier. It is being used in stock records or purchase orders.'];
            }

            // Soft delete supplier
            Yii::$app->db->createCommand()->update('suppliers', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            // Log activity
            $this->logActivity('Delete', 'Suppliers', $id, "Deleted supplier: {$supplier['name']}");

            return [
                'success' => true,
                'message' => 'Supplier deleted successfully'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error deleting supplier: ' . $e->getMessage()];
        }
    }

    // Manufacturer Management Actions
    public function actionAddManufacturer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $name = Yii::$app->request->post('manufacturer');
            $description = Yii::$app->request->post('description', '');
            $contact_person = Yii::$app->request->post('contact_person', '');
            $phone = Yii::$app->request->post('phone', '');
            $email = Yii::$app->request->post('email', '');
            $address = Yii::$app->request->post('address', '');
            $website = Yii::$app->request->post('website', '');
            $country = Yii::$app->request->post('country', '');
            $status = Yii::$app->request->post('status', 'active');

            if (empty($name)) {
                return ['success' => false, 'message' => 'Manufacturer name is required'];
            }

            // Check if manufacturer already exists
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM manufacturers WHERE name = :name AND is_deleted = 0
            ')->bindValue(':name', $name)->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Manufacturer with this name already exists'];
            }

            // Insert new manufacturer
            Yii::$app->db->createCommand()->insert('manufacturers', [
                'name' => $name,
                'description' => $description,
                'contact_person' => $contact_person,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'website' => $website,
                'country' => $country,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();

            $manufacturerId = Yii::$app->db->getLastInsertID();

            // Log activity
            $this->logActivity('Create', 'Manufacturers', $manufacturerId, "Added new manufacturer: {$name}");

            return [
                'success' => true,
                'message' => 'Manufacturer added successfully',
                'manufacturer_id' => $manufacturerId
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionUpdateManufacturer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $name = Yii::$app->request->post('manufacturer');
            $description = Yii::$app->request->post('description', '');
            $contact_person = Yii::$app->request->post('contact_person', '');
            $phone = Yii::$app->request->post('phone', '');
            $email = Yii::$app->request->post('email', '');
            $address = Yii::$app->request->post('address', '');
            $website = Yii::$app->request->post('website', '');
            $country = Yii::$app->request->post('country', '');
            $status = Yii::$app->request->post('status', 'active');

            if (empty($name)) {
                return ['success' => false, 'message' => 'Manufacturer name is required'];
            }

            // Check if manufacturer exists
            $manufacturer = Yii::$app->db->createCommand('
                SELECT * FROM manufacturers WHERE id = :id AND is_deleted = 0
            ')->bindValue(':id', $id)->queryOne();

            if (!$manufacturer) {
                return ['success' => false, 'message' => 'Manufacturer not found'];
            }

            // Check if name is already taken by another manufacturer
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM manufacturers WHERE name = :name AND id != :id AND is_deleted = 0
            ')->bindValues([':name' => $name, ':id' => $id])->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Manufacturer with this name already exists'];
            }

            // Update manufacturer
            $result = Yii::$app->db->createCommand()->update('manufacturers', [
                'name' => $name,
                'description' => $description,
                'contact_person' => $contact_person,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'website' => $website,
                'country' => $country,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            if ($result) {
                // Log activity
                $this->logActivity('Update', 'Manufacturers', $id, "Updated manufacturer: {$name}");
                return ['success' => true, 'message' => 'Manufacturer updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update manufacturer'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionDeleteManufacturer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');

            // Check if manufacturer exists
            $manufacturer = Yii::$app->db->createCommand('
                SELECT * FROM manufacturers WHERE id = :id AND is_deleted = 0
            ')->bindValue(':id', $id)->queryOne();

            if (!$manufacturer) {
                return ['success' => false, 'message' => 'Manufacturer not found'];
            }

            // Check if manufacturer is used in medicines
            $usedInMedicines = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM medicines WHERE manufacturer = :name AND is_deleted = 0
            ')->bindValue(':name', $manufacturer['name'])->queryScalar();

            if ($usedInMedicines > 0) {
                return ['success' => false, 'message' => 'Cannot delete manufacturer. It is being used in ' . $usedInMedicines . ' medicine(s)'];
            }

            // Soft delete manufacturer
            $result = Yii::$app->db->createCommand()->update('manufacturers', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            if ($result) {
                // Log activity
                $this->logActivity('Delete', 'Manufacturers', $id, "Deleted manufacturer: {$manufacturer['name']}");
                return ['success' => true, 'message' => 'Manufacturer deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete manufacturer'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Medicine Form Management Actions
    public function actionAddMedicineForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $name = Yii::$app->request->post('form');
            $description = Yii::$app->request->post('description', '');
            $unit_type = Yii::$app->request->post('unit_type', 'unit');
            $status = Yii::$app->request->post('status', 'active');

            if (empty($name)) {
                return ['success' => false, 'message' => 'Form name is required'];
            }

            // Check if form already exists
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM medicine_forms WHERE name = :name AND is_deleted = 0
            ')->bindValue(':name', $name)->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Medicine form with this name already exists'];
            }

            // Insert new form
            Yii::$app->db->createCommand()->insert('medicine_forms', [
                'name' => $name,
                'description' => $description,
                'unit_type' => $unit_type,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ])->execute();

            $formId = Yii::$app->db->getLastInsertID();

            // Log activity
            $this->logActivity('Create', 'Medicine Forms', $formId, "Added new medicine form: {$name}");

            return [
                'success' => true,
                'message' => 'Medicine form added successfully',
                'form_id' => $formId
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionUpdateMedicineForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');
            $name = Yii::$app->request->post('form');
            $description = Yii::$app->request->post('description', '');
            $unit_type = Yii::$app->request->post('unit_type', 'unit');
            $status = Yii::$app->request->post('status', 'active');

            if (empty($name)) {
                return ['success' => false, 'message' => 'Form name is required'];
            }

            // Check if form exists
            $form = Yii::$app->db->createCommand('
                SELECT * FROM medicine_forms WHERE id = :id AND is_deleted = 0
            ')->bindValue(':id', $id)->queryOne();

            if (!$form) {
                return ['success' => false, 'message' => 'Medicine form not found'];
            }

            // Check if name is already taken by another form
            $existing = Yii::$app->db->createCommand('
                SELECT id FROM medicine_forms WHERE name = :name AND id != :id AND is_deleted = 0
            ')->bindValues([':name' => $name, ':id' => $id])->queryOne();

            if ($existing) {
                return ['success' => false, 'message' => 'Medicine form with this name already exists'];
            }

            // Update form
            $result = Yii::$app->db->createCommand()->update('medicine_forms', [
                'name' => $name,
                'description' => $description,
                'unit_type' => $unit_type,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            if ($result) {
                // Log activity
                $this->logActivity('Update', 'Medicine Forms', $id, "Updated medicine form: {$name}");
                return ['success' => true, 'message' => 'Medicine form updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update medicine form'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionDeleteMedicineForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $id = Yii::$app->request->post('id');

            // Check if form exists
            $form = Yii::$app->db->createCommand('
                SELECT * FROM medicine_forms WHERE id = :id AND is_deleted = 0
            ')->bindValue(':id', $id)->queryOne();

            if (!$form) {
                return ['success' => false, 'message' => 'Medicine form not found'];
            }

            // Check if form is used in medicines
            $usedInMedicines = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM medicines WHERE form = :name AND is_deleted = 0
            ')->bindValue(':name', $form['name'])->queryScalar();

            if ($usedInMedicines > 0) {
                return ['success' => false, 'message' => 'Cannot delete medicine form. It is being used in ' . $usedInMedicines . ' medicine(s)'];
            }

            // Soft delete form
            $result = Yii::$app->db->createCommand()->update('medicine_forms', [
                'is_deleted' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id])->execute();

            if ($result) {
                // Log activity
                $this->logActivity('Delete', 'Medicine Forms', $id, "Deleted medicine form: {$form['name']}");
                return ['success' => true, 'message' => 'Medicine form deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete medicine form'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionGetConfigurations()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $categories = Yii::$app->db->createCommand('
                SELECT * FROM categories WHERE is_deleted = 0 ORDER BY name ASC
            ')->queryAll();

            $suppliers = Yii::$app->db->createCommand('
                SELECT * FROM suppliers WHERE is_deleted = 0 ORDER BY name ASC
            ')->queryAll();

            $manufacturers = Yii::$app->db->createCommand('
                SELECT * FROM manufacturers WHERE is_deleted = 0 ORDER BY name ASC
            ')->queryAll();

            $medicineForms = Yii::$app->db->createCommand('
                SELECT * FROM medicine_forms WHERE is_deleted = 0 ORDER BY name ASC
            ')->queryAll();

            return [
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'suppliers' => $suppliers,
                    'manufacturers' => $manufacturers,
                    'medicineForms' => $medicineForms
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionActivities()
    {
        try {
            // Get inventory-related activity logs
            $activities = Yii::$app->db->createCommand("
                SELECT 
                    al.id,
                    al.user_id,
                    al.action,
                    al.location,
                    al.record_id,
                    al.description,
                    al.ip_address,
                    al.user_agent,
                    al.created_at,
                    u.username,
                    u.email
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.is_deleted = 0
                AND al.location IN 
                ('Purchase Orders', 'Medicines', 'Stock', 'Categories', 
                    'Suppliers', 'Manufacturers', 'Medicine Forms', 
                    'Expiry Alert Settings', 'Expiry Alerts' 
                )
                ORDER BY al.created_at DESC
            ")->queryAll();

            return $this->render('activities', [
                'activities' => $activities
            ]);
        } catch (\Exception $e) {
            Yii::error('Error loading inventory activities: ' . $e->getMessage());
            return $this->render('activities', [
                'activities' => []
            ]);
        }
    }
}
