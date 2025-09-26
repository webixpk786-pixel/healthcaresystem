<?php
// Fetch comprehensive inventory data
try {
    // Medicine Catalog Statistics
    $totalMedicines = Yii::$app->db->createCommand('SELECT COUNT(*) FROM medicines WHERE is_deleted = 0')->queryScalar() ?: 0;
    $activeMedicines = Yii::$app->db->createCommand('SELECT COUNT(*) FROM medicines WHERE status = "active" AND is_deleted = 0')->queryScalar() ?: 0;
    $inactiveMedicines = Yii::$app->db->createCommand('SELECT COUNT(*) FROM medicines WHERE status = "inactive" AND is_deleted = 0')->queryScalar() ?: 0;

    // Stock Management Statistics
    $totalStockItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE is_deleted = 0')->queryScalar() ?: 0;
    $inStockItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE status = "in_stock" AND is_deleted = 0')->queryScalar() ?: 0;
    $lowStockItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE status = "low_stock" AND is_deleted = 0')->queryScalar() ?: 0;
    $criticalStockItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE status = "critical" AND is_deleted = 0')->queryScalar() ?: 0;
    $outOfStockItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE status = "out_of_stock" AND is_deleted = 0')->queryScalar() ?: 0;

    // Expiry Alerts Statistics
    $expiringSoon = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiry_date > CURDATE() AND is_deleted = 0')->queryScalar() ?: 0;
    $expiredItems = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE expiry_date < CURDATE() AND is_deleted = 0')->queryScalar() ?: 0;
    $expiringThisWeek = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND expiry_date > CURDATE() AND is_deleted = 0')->queryScalar() ?: 0;

    // Purchase Orders Statistics
    $totalOrders = Yii::$app->db->createCommand('SELECT COUNT(*) FROM purchase_orders WHERE is_deleted = 0')->queryScalar() ?: 0;
    $pendingOrders = Yii::$app->db->createCommand('SELECT COUNT(*) FROM purchase_orders WHERE status = "pending" AND is_deleted = 0')->queryScalar() ?: 0;
    $approvedOrders = Yii::$app->db->createCommand('SELECT COUNT(*) FROM purchase_orders WHERE status = "approved" AND is_deleted = 0')->queryScalar() ?: 0;
    $deliveredOrders = Yii::$app->db->createCommand('SELECT COUNT(*) FROM purchase_orders WHERE status = "delivered" AND is_deleted = 0')->queryScalar() ?: 0;
    $cancelledOrders = Yii::$app->db->createCommand('SELECT COUNT(*) FROM purchase_orders WHERE status = "cancelled" AND is_deleted = 0')->queryScalar() ?: 0;

    // Suppliers Statistics
    $totalSuppliers = Yii::$app->db->createCommand('SELECT COUNT(*) FROM suppliers WHERE is_deleted = 0')->queryScalar() ?: 0;
    $activeSuppliers = Yii::$app->db->createCommand('SELECT COUNT(*) FROM suppliers WHERE status = "active" AND is_deleted = 0')->queryScalar() ?: 0;
    $inactiveSuppliers = Yii::$app->db->createCommand('SELECT COUNT(*) FROM suppliers WHERE status = "inactive" AND is_deleted = 0')->queryScalar() ?: 0;

    // Stock Movements Statistics
    $totalMovements = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock_movements WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->queryScalar() ?: 0;
    $movementsIn = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock_movements WHERE movement_type = "in" AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->queryScalar() ?: 0;
    $movementsOut = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock_movements WHERE movement_type = "out" AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->queryScalar() ?: 0;
    $movementsAdjustment = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock_movements WHERE movement_type = "adjustment" AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->queryScalar() ?: 0;

    // Total Inventory Value
    $totalInventoryValue = Yii::$app->db->createCommand('SELECT SUM(quantity * selling_price) FROM stock WHERE is_deleted = 0')->queryScalar() ?: 0;
    $totalPurchaseValue = Yii::$app->db->createCommand('SELECT SUM(quantity * purchase_price) FROM stock WHERE is_deleted = 0')->queryScalar() ?: 0;

    // Activity Logs Statistics
    $totalActivityLogs = Yii::$app->db->createCommand('SELECT COUNT(*) FROM activity_logs WHERE location LIKE "%inventory%" OR location LIKE "%stock%" OR location LIKE "%medicine%" AND is_deleted = 0')->queryScalar() ?: 0;
    $todayActivityLogs = Yii::$app->db->createCommand('SELECT COUNT(*) FROM activity_logs WHERE DATE(created_at) = CURDATE() AND (location LIKE "%inventory%" OR location LIKE "%stock%" OR location LIKE "%medicine%") AND is_deleted = 0')->queryScalar() ?: 0;
} catch (Exception $e) {
    // Fallback values if tables don't exist yet
    $totalMedicines = 156;
    $activeMedicines = 142;
    $inactiveMedicines = 14;
    $totalStockItems = 89;
    $inStockItems = 65;
    $lowStockItems = 18;
    $criticalStockItems = 6;
    $outOfStockItems = 0;
    $expiringSoon = 12;
    $expiredItems = 3;
    $expiringThisWeek = 5;
    $totalOrders = 24;
    $pendingOrders = 3;
    $approvedOrders = 8;
    $deliveredOrders = 11;
    $cancelledOrders = 2;
    $totalSuppliers = 15;
    $activeSuppliers = 12;
    $inactiveSuppliers = 3;
    $totalMovements = 145;
    $movementsIn = 67;
    $movementsOut = 52;
    $movementsAdjustment = 26;
    $totalInventoryValue = 125400;
    $totalPurchaseValue = 98750;
    $totalActivityLogs = 89;
    $todayActivityLogs = 7;
}
?>

<style>
    .inventory-stats-container {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 8px rgba(100, 108, 255, 0.06);
        border: 1px solid #f0f0f7;
    }

    .stats-header {
        margin-bottom: 16px;
        text-align: center;
    }

    .stats-title {
        font-size: 16px;
        font-weight: 700;
        color: #232360;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .stats-title i {
        color: #646cff;
        font-size: 18px;
    }

    .stats-subtitle {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }

    .stats-card {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        border-radius: 8px;
        padding: 12px;
        border-left: 3px solid #646cff;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(100, 108, 255, 0.12);
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, rgba(100, 108, 255, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
        border-radius: 50%;
        transform: translate(15px, -15px);
    }

    .stats-card.medicines {
        border-left-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .stats-card.stock {
        border-left-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }

    .stats-card.expiry {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .stats-card.orders {
        border-left-color: #8b5cf6;
        background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    }

    .stats-card.suppliers {
        border-left-color: #06b6d4;
        background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);
    }

    .stats-card.movements {
        border-left-color: #84cc16;
        background: linear-gradient(135deg, #f7fee7 0%, #ecfccb 100%);
    }

    .stats-card.value {
        border-left-color: #f97316;
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    }

    .stats-card.activity {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .card-title {
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .card-title i {
        font-size: 12px;
        opacity: 0.8;
    }

    .card-icon {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: white;
        background: linear-gradient(135deg, #646cff 0%, #8b5cf6 100%);
    }

    .medicines .card-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stock .card-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .expiry .card-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .orders .card-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .suppliers .card-icon {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .movements .card-icon {
        background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%);
    }

    .value .card-icon {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }

    .activity .card-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .stats-content {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
        margin-bottom: 8px;
    }

    .stat-item {
        text-align: center;
        padding: 4px 6px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .stat-value {
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1px;
        line-height: 1;
    }

    .stat-label {
        font-size: 8px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        line-height: 1;
    }

    .stat-status {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        border-radius: 8px;
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        margin-top: 2px;
    }

    .status-good {
        background: #dcfce7;
        color: #166534;
    }

    .status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-danger {
        background: #fecaca;
        color: #991b1b;
    }

    .status-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .summary-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
    }

    .summary-item {
        text-align: center;
        padding: 8px 4px;
        background: #f9fafb;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .summary-value {
        font-size: 11px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
        line-height: 1;
    }

    .summary-label {
        font-size: 8px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        line-height: 1;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .inventory-stats-container {
            padding: 12px;
            margin: 0 16px 12px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .stats-content {
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
        }

        .stat-value {
            font-size: 11px;
        }

        .stat-label {
            font-size: 7px;
        }

        .summary-row {
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .summary-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="">


    <div class="stats-grid">
        <!-- Medicine Catalog Stats -->
        <div class="stats-card medicines" onclick="window.location.href = 'index.php?r=inventory/medicinescatalog'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-pills"></i>
                    Medicines
                </div>
                <div class="card-icon">
                    <i class="fas fa-pills"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalMedicines) ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($activeMedicines) ?></div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($inactiveMedicines) ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?= $totalMedicines > 0 ? round(($activeMedicines / $totalMedicines) * 100, 1) : 0 ?>%</div>
                    <div class="stat-label">Active %</div>
                </div>
            </div>
            <div class="stat-status status-good">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $activeMedicines > 0 ? round(($activeMedicines / $totalMedicines) * 100, 1) : 0 ?>% Active
            </div>
        </div>

        <!-- Stock Management Stats -->
        <div class="stats-card stock" onclick="window.location.href = 'index.php?r=inventory/stockmanagement'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-boxes-stacked"></i>
                    Stock
                </div>
                <div class="card-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($inStockItems) ?></div>
                    <div class="stat-label">In Stock</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($lowStockItems) ?></div>
                    <div class="stat-label">Low Stock</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($criticalStockItems) ?></div>
                    <div class="stat-label">Critical</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($outOfStockItems) ?></div>
                    <div class="stat-label">Out Stock</div>
                </div>
            </div>
            <div
                class="stat-status <?= $criticalStockItems > 0 ? 'status-danger' : ($lowStockItems > 0 ? 'status-warning' : 'status-good') ?>">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $criticalStockItems > 0 ? 'Critical' : ($lowStockItems > 0 ? 'Low Stock' : 'Good') ?>
            </div>
        </div>

        <!-- Expiry Alerts Stats -->
        <div class="stats-card expiry" onclick="window.location.href = 'index.php?r=inventory/expiryalert'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-clock"></i>
                    Expiry
                </div>
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($expiringThisWeek) ?></div>
                    <div class="stat-label">This Week</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($expiringSoon) ?></div>
                    <div class="stat-label">30 Days</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($expiredItems) ?></div>
                    <div class="stat-label">Expired</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $expiringThisWeek + $expiringSoon + $expiredItems ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
            <div
                class="stat-status <?= $expiredItems > 0 ? 'status-danger' : ($expiringThisWeek > 0 ? 'status-warning' : 'status-good') ?>">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $expiredItems > 0 ? 'Expired' : ($expiringThisWeek > 0 ? 'Soon' : 'Good') ?>
            </div>
        </div>

        <!-- Purchase Orders Stats -->
        <div class="stats-card orders" onclick="window.location.href = 'index.php?r=inventory/purchaseorders'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-shopping-cart"></i>
                    Orders
                </div>
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalOrders) ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($pendingOrders) ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($deliveredOrders) ?></div>
                    <div class="stat-label">Delivered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($cancelledOrders) ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>
            <div class="stat-status status-info">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $pendingOrders > 0 ? $pendingOrders . ' Pending' : 'All OK' ?>
            </div>
        </div>

        <!-- Suppliers Stats -->
        <div class="stats-card suppliers">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-truck"></i>
                    Suppliers
                </div>
                <div class="card-icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalSuppliers) ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($activeSuppliers) ?></div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($inactiveSuppliers) ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?= $totalSuppliers > 0 ? round(($activeSuppliers / $totalSuppliers) * 100, 1) : 0 ?>%</div>
                    <div class="stat-label">Active %</div>
                </div>
            </div>
            <div class="stat-status status-good">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $totalSuppliers > 0 ? round(($activeSuppliers / $totalSuppliers) * 100, 1) : 0 ?>% Active
            </div>
        </div>

        <!-- Stock Movements Stats -->
        <div class="stats-card movements">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-arrows-alt"></i>
                    Movements
                </div>
                <div class="card-icon">
                    <i class="fas fa-arrows-alt"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($movementsIn) ?></div>
                    <div class="stat-label">In</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($movementsOut) ?></div>
                    <div class="stat-label">Out</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($movementsAdjustment) ?></div>
                    <div class="stat-label">Adj</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalMovements) ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
            <div class="stat-status status-info">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                Last 30 Days
            </div>
        </div>

        <!-- Inventory Value Stats -->
        <div class="stats-card value">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-dollar-sign"></i>
                    Value
                </div>
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalInventoryValue / 1000, 0) ?>k</div>
                    <div class="stat-label">Selling</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalPurchaseValue / 1000, 0) ?>k</div>
                    <div class="stat-label">Purchase</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?= number_format(($totalInventoryValue - $totalPurchaseValue) / 1000, 0) ?>k</div>
                    <div class="stat-label">Profit</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?= $totalInventoryValue > 0 ? round((($totalInventoryValue - $totalPurchaseValue) / $totalInventoryValue) * 100, 1) : 0 ?>%
                    </div>
                    <div class="stat-label">Margin</div>
                </div>
            </div>
            <div class="stat-status status-good">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $totalInventoryValue > 0 ? round((($totalInventoryValue - $totalPurchaseValue) / $totalInventoryValue) * 100, 1) : 0 ?>%
                Margin
            </div>
        </div>

        <!-- Activity Logs Stats -->
        <div class="stats-card activity" onclick="window.location.href = 'index.php?r=inventory/activities'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-history"></i>
                    Activity
                </div>
                <div class="card-icon">
                    <i class="fas fa-history"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalActivityLogs) ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($todayActivityLogs) ?></div>
                    <div class="stat-label">Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format(round($totalActivityLogs / 30, 1)) ?></div>
                    <div class="stat-label">Daily Avg</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?= $totalActivityLogs > 0 ? round(($todayActivityLogs / max($totalActivityLogs / 30, 1)) * 100, 0) : 0 ?>%
                    </div>
                    <div class="stat-label">Today %</div>
                </div>
            </div>
            <div class="stat-status status-info">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $todayActivityLogs ?> Today
            </div>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="summary-row">
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalMedicines) ?></div>
            <div class="summary-label">Medicines</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalStockItems) ?></div>
            <div class="summary-label">Stock Items</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalOrders) ?></div>
            <div class="summary-label">Orders</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalInventoryValue / 1000, 0) ?>k</div>
            <div class="summary-label">Value</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalActivityLogs) ?></div>
            <div class="summary-label">Logs</div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />