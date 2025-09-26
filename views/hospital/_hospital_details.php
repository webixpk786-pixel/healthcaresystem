<?php
// Fetch comprehensive hospital data
try {
    // Patient Management Statistics
    $totalPatients = Yii::$app->db->createCommand('SELECT COUNT(*) FROM patients WHERE is_deleted = 0')->queryScalar() ?: 0;
    $newPatientsToday = Yii::$app->db->createCommand('SELECT COUNT(*) FROM patients WHERE DATE(created_at) = CURDATE()')->queryScalar() ?: 0;
    $dischargedPatients = Yii::$app->db->createCommand('SELECT COUNT(*) FROM patients WHERE status = "discharged" AND DATE(updated_at) = CURDATE()')->queryScalar() ?: 0;
    $admittedPatients = Yii::$app->db->createCommand('SELECT COUNT(*) FROM patients WHERE status = "admitted" AND is_deleted = 0')->queryScalar() ?: 0;

    // Appointment Statistics
    $totalAppointments = Yii::$app->db->createCommand('SELECT COUNT(*) FROM appointments WHERE appointment_date >= CURDATE()')->queryScalar() ?: 0;
    $completedAppointments = Yii::$app->db->createCommand('SELECT COUNT(*) FROM appointments WHERE status = "completed" AND DATE(appointment_date) = CURDATE()')->queryScalar() ?: 0;
    $cancelledAppointments = Yii::$app->db->createCommand('SELECT COUNT(*) FROM appointments WHERE status = "cancelled" AND DATE(appointment_date) = CURDATE()')->queryScalar() ?: 0;
    $pendingAppointments = Yii::$app->db->createCommand('SELECT COUNT(*) FROM appointments WHERE status = "pending" AND appointment_date >= CURDATE()')->queryScalar() ?: 0;

    // Bed Management Statistics
    $totalBeds = Yii::$app->db->createCommand('SELECT COUNT(*) FROM beds WHERE is_deleted = 0')->queryScalar() ?: 0;
    $occupiedBeds = Yii::$app->db->createCommand('SELECT COUNT(*) FROM beds WHERE status = "occupied" AND is_deleted = 0')->queryScalar() ?: 0;
    $availableBeds = Yii::$app->db->createCommand('SELECT COUNT(*) FROM beds WHERE status = "available" AND is_deleted = 0')->queryScalar() ?: 0;
    $maintenanceBeds = Yii::$app->db->createCommand('SELECT COUNT(*) FROM beds WHERE status = "maintenance" AND is_deleted = 0')->queryScalar() ?: 0;

    // Staff Statistics
    $totalDoctors = Yii::$app->db->createCommand('SELECT COUNT(*) FROM doctors WHERE is_deleted = 0')->queryScalar() ?: 0;
    $totalNurses = Yii::$app->db->createCommand('SELECT COUNT(*) FROM nurses WHERE is_deleted = 0')->queryScalar() ?: 0;
    $activeStaff = Yii::$app->db->createCommand('SELECT COUNT(*) FROM staff WHERE status = "active" AND is_deleted = 0')->queryScalar() ?: 0;
    $onDutyStaff = Yii::$app->db->createCommand('SELECT COUNT(*) FROM staff WHERE shift_status = "on_duty" AND is_deleted = 0')->queryScalar() ?: 0;

    // Emergency & Critical Care
    $emergencyCases = Yii::$app->db->createCommand('SELECT COUNT(*) FROM emergency_cases WHERE status = "active" AND created_at >= CURDATE()')->queryScalar() ?: 0;
    $criticalCases = Yii::$app->db->createCommand('SELECT COUNT(*) FROM patients WHERE priority = "critical" AND status = "admitted" AND is_deleted = 0')->queryScalar() ?: 0;
    $icuPatients = Yii::$app->db->createCommand('SELECT COUNT(*) FROM patients WHERE department = "ICU" AND status = "admitted" AND is_deleted = 0')->queryScalar() ?: 0;
    $ambulanceCalls = Yii::$app->db->createCommand('SELECT COUNT(*) FROM ambulance_calls WHERE DATE(created_at) = CURDATE()')->queryScalar() ?: 0;

    // Financial Statistics
    $todayRevenue = Yii::$app->db->createCommand('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_date = CURDATE()')->queryScalar() ?: 0;
    $pendingPayments = Yii::$app->db->createCommand('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = "pending"')->queryScalar() ?: 0;
    $totalRevenue = Yii::$app->db->createCommand('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE MONTH(payment_date) = MONTH(CURDATE())')->queryScalar() ?: 0;
    $insuranceClaims = Yii::$app->db->createCommand('SELECT COUNT(*) FROM insurance_claims WHERE status = "pending" AND is_deleted = 0')->queryScalar() ?: 0;
} catch (Exception $e) {
    // Fallback values if tables don't exist yet
    $totalPatients = 0;
    $newPatientsToday = 0;
    $dischargedPatients = 0;
    $admittedPatients = 0;
    $totalAppointments = 0;
    $completedAppointments = 0;
    $cancelledAppointments = 0;
    $pendingAppointments = 0;
    $totalBeds = 0;
    $occupiedBeds = 0;
    $availableBeds = 0;
    $maintenanceBeds = 0;
    $totalDoctors = 0;
    $totalNurses = 0;
    $activeStaff = 0;
    $onDutyStaff = 0;
    $emergencyCases = 0;
    $criticalCases = 0;
    $icuPatients = 0;
    $ambulanceCalls = 0;
    $todayRevenue = 0;
    $pendingPayments = 0;
    $totalRevenue = 0;
    $insuranceClaims = 0;
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
        grid-template-columns: repeat(3, 1fr);
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
        cursor: pointer;
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

    .stats-card.patients {
        border-left-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .stats-card.appointments {
        border-left-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }

    .stats-card.beds {
        border-left-color: #22c55e;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .stats-card.doctors {
        border-left-color: #8b5cf6;
        background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    }

    .stats-card.emergency {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }

    .stats-card.revenue {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
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

    .patients .card-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .appointments .card-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .beds .card-icon {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    }

    .doctors .card-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .emergency .card-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .revenue .card-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
        grid-template-columns: repeat(6, 1fr);
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
        <!-- Patient Management Stats -->
        <div class="stats-card patients" onclick="window.location.href = 'index.php?r=hospital/patients'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-users"></i>
                    Patients
                </div>
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalPatients) ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($admittedPatients) ?></div>
                    <div class="stat-label">Admitted</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($newPatientsToday) ?></div>
                    <div class="stat-label">New Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($dischargedPatients) ?></div>
                    <div class="stat-label">Discharged</div>
                </div>
            </div>
            <div class="stat-status status-good">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $totalPatients > 0 ? round(($admittedPatients / $totalPatients) * 100, 1) : 0 ?>% Admitted
            </div>
        </div>

        <!-- Appointment Management Stats -->
        <div class="stats-card appointments" onclick="window.location.href = 'index.php?r=hospital/appointments'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-calendar-check"></i>
                    Appointments
                </div>
                <div class="card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalAppointments) ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($completedAppointments) ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($pendingAppointments) ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($cancelledAppointments) ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>
            <div class="stat-status status-info">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $pendingAppointments > 0 ? $pendingAppointments . ' Pending' : 'All Scheduled' ?>
            </div>
        </div>

        <!-- Bed Management Stats -->
        <div class="stats-card beds" onclick="window.location.href = 'index.php?r=hospital/beds'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-bed"></i>
                    Beds
                </div>
                <div class="card-icon">
                    <i class="fas fa-bed"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalBeds) ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($availableBeds) ?></div>
                    <div class="stat-label">Available</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($occupiedBeds) ?></div>
                    <div class="stat-label">Occupied</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($maintenanceBeds) ?></div>
                    <div class="stat-label">Maintenance</div>
                </div>
            </div>
            <div
                class="stat-status <?= $availableBeds < ($totalBeds * 0.1) ? 'status-danger' : ($availableBeds < ($totalBeds * 0.3) ? 'status-warning' : 'status-good') ?>">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $totalBeds > 0 ? round(($availableBeds / $totalBeds) * 100, 1) : 0 ?>% Available
            </div>
        </div>

        <!-- Doctors & Staff Stats -->
        <div class="stats-card doctors" onclick="window.location.href = 'index.php?r=hospital/staff'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-user-md"></i>
                    Doctors
                </div>
                <div class="card-icon">
                    <i class="fas fa-user-md"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalDoctors) ?></div>
                    <div class="stat-label">Doctors</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalNurses) ?></div>
                    <div class="stat-label">Nurses</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($activeStaff) ?></div>
                    <div class="stat-label">Active Staff</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($onDutyStaff) ?></div>
                    <div class="stat-label">On Duty</div>
                </div>
            </div>
            <div class="stat-status status-good">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $onDutyStaff ?> On Duty Now
            </div>
        </div>

        <!-- Emergency Cases Stats -->
        <div class="stats-card emergency" onclick="window.location.href = 'index.php?r=hospital/emergency'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-ambulance"></i>
                    Emergency
                </div>
                <div class="card-icon">
                    <i class="fas fa-ambulance"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($emergencyCases) ?></div>
                    <div class="stat-label">Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($criticalCases) ?></div>
                    <div class="stat-label">Critical</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($icuPatients) ?></div>
                    <div class="stat-label">ICU</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($ambulanceCalls) ?></div>
                    <div class="stat-label">Ambulance</div>
                </div>
            </div>
            <div
                class="stat-status <?= $criticalCases > 0 ? 'status-danger' : ($emergencyCases > 0 ? 'status-warning' : 'status-good') ?>">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $criticalCases > 0 ? 'Critical' : ($emergencyCases > 0 ? 'Active' : 'All Clear') ?>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="stats-card revenue" onclick="window.location.href = 'index.php?r=hospital/revenue'">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-dollar-sign"></i>
                    Revenue
                </div>
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($todayRevenue / 1000, 0) ?>k</div>
                    <div class="stat-label">Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($totalRevenue / 1000, 0) ?>k</div>
                    <div class="stat-label">This Month</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($pendingPayments / 1000, 0) ?>k</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($insuranceClaims) ?></div>
                    <div class="stat-label">Claims</div>
                </div>
            </div>
            <div class="stat-status status-good">
                <i class="fas fa-circle" style="font-size: 4px;"></i>
                <?= $totalRevenue > 0 ? round((($totalRevenue - $pendingPayments) / $totalRevenue) * 100, 1) : 0 ?>%
                Collected
            </div>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="summary-row">
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalPatients) ?></div>
            <div class="summary-label">Patients</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalAppointments) ?></div>
            <div class="summary-label">Appointments</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalBeds) ?></div>
            <div class="summary-label">Beds</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalDoctors + $totalNurses) ?></div>
            <div class="summary-label">Staff</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($emergencyCases) ?></div>
            <div class="summary-label">Emergency</div>
        </div>
        <div class="summary-item">
            <div class="summary-value"><?= number_format($totalRevenue / 1000, 0) ?>k</div>
            <div class="summary-label">Revenue</div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />