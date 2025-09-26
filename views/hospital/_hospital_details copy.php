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
    $todayRevenue = 0;
    $pendingPayments = 0;
    $totalRevenue = 0;
    $insuranceClaims = 0;
}
?>

<style>
    .hospital-details-container {
        background: #fff;
        border-radius: 16px;
        /* padding: 20px; */
        margin-bottom: 20px;
        /* box-shadow: 0 2px 12px rgba(100, 108, 255, 0.08); */
        /* border: 1px solid #f0f0f7; */
    }

    .details-header {
        margin-bottom: 20px;
        text-align: center;
        padding-bottom: 16px;
        border-bottom: 2px solid #f0f0f7;
    }

    .details-title {
        font-size: 18px;
        font-weight: 700;
        color: #232360;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .details-title i {
        color: #646cff;
        font-size: 20px;
    }

    .details-subtitle {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }

    .details-card {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        border-radius: 8px;
        padding: 12px;
        border-left: 3px solid #646cff;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .details-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(100, 108, 255, 0.12);
    }

    .details-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, rgba(100, 108, 255, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
        border-radius: 50%;
        transform: translate(12px, -12px);
    }

    .details-card.patients {
        border-left-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .details-card.appointments {
        border-left-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }

    .details-card.beds {
        border-left-color: #22c55e;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .details-card.staff {
        border-left-color: #8b5cf6;
        background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    }

    .details-card.emergency {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }

    .details-card.financial {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .card-icon {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #646cff;
        box-shadow: 0 2px 8px rgba(100, 108, 255, 0.2);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .patients .card-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .appointments .card-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }

    .beds .card-icon {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
    }

    .staff .card-icon {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
    }

    .emergency .card-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .financial .card-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .card-info {
        flex: 1;
        min-width: 0;
    }

    .card-label {
        font-size: 10px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        line-height: 1;
        margin-bottom: 2px;
    }

    .card-value {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    .card-status {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        border-radius: 8px;
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        margin-top: 4px;
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

    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: #232360;
        margin: 16px 0 12px 0;
        padding-bottom: 6px;
        border-bottom: 1px solid #f0f0f7;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 1200px) {
        .details-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .hospital-details-container {
            padding: 12px;
            margin: 0 16px 12px;
        }

        .details-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .card-value {
            font-size: 14px;
        }

        .card-label {
            font-size: 9px;
        }
    }

    @media (max-width: 480px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="hospital-details-container">

    <!-- Patient Management -->
    <div class="details-grid">
        <div class="details-card patients">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Total Patients</div>
                <div class="card-value"><?= number_format($totalPatients) ?></div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Active
                </div>
            </div>
        </div>

        <div class="details-card patients">
            <div class="card-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="card-info">
                <div class="card-label">New Today</div>
                <div class="card-value"><?= number_format($newPatientsToday) ?></div>
                <div class="card-status status-info">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Admissions
                </div>
            </div>
        </div>

        <div class="details-card patients">
            <div class="card-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Admitted</div>
                <div class="card-value"><?= number_format($admittedPatients) ?></div>
                <div class="card-status status-warning">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    In Care
                </div>
            </div>
        </div>

        <div class="details-card patients">
            <div class="card-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Discharged Today</div>
                <div class="card-value"><?= number_format($dischargedPatients) ?></div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Released
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Management -->
    <div class="details-grid">
        <div class="details-card appointments">
            <div class="card-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Total Appointments</div>
                <div class="card-value"><?= number_format($totalAppointments) ?></div>
                <div class="card-status status-info">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Scheduled
                </div>
            </div>
        </div>

        <div class="details-card appointments">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Completed Today</div>
                <div class="card-value"><?= number_format($completedAppointments) ?></div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Done
                </div>
            </div>
        </div>

        <div class="details-card appointments">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Pending</div>
                <div class="card-value"><?= number_format($pendingAppointments) ?></div>
                <div class="card-status status-warning">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Waiting
                </div>
            </div>
        </div>

        <div class="details-card appointments">
            <div class="card-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Cancelled Today</div>
                <div class="card-value"><?= number_format($cancelledAppointments) ?></div>
                <div class="card-status status-danger">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Cancelled
                </div>
            </div>
        </div>
    </div>

    <!-- Bed Management -->

    <div class="details-grid">
        <div class="details-card beds">
            <div class="card-icon">
                <i class="fas fa-bed"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Total Beds</div>
                <div class="card-value"><?= number_format($totalBeds) ?></div>
                <div class="card-status status-info">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Capacity
                </div>
            </div>
        </div>

        <div class="details-card beds">
            <div class="card-icon">
                <i class="fas fa-bed"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Available</div>
                <div class="card-value"><?= number_format($availableBeds) ?></div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Vacant
                </div>
            </div>
        </div>

        <div class="details-card beds">
            <div class="card-icon">
                <i class="fas fa-bed"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Occupied</div>
                <div class="card-value"><?= number_format($occupiedBeds) ?></div>
                <div class="card-status status-warning">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    In Use
                </div>
            </div>
        </div>

        <div class="details-card beds">
            <div class="card-icon">
                <i class="fas fa-tools"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Maintenance</div>
                <div class="card-value"><?= number_format($maintenanceBeds) ?></div>
                <div class="card-status status-danger">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Repair
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Management -->
    <div class="details-grid">
        <div class="details-card staff">
            <div class="card-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Total Doctors</div>
                <div class="card-value"><?= number_format($totalDoctors) ?></div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Medical
                </div>
            </div>
        </div>

        <div class="details-card staff">
            <div class="card-icon">
                <i class="fas fa-user-nurse"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Total Nurses</div>
                <div class="card-value"><?= number_format($totalNurses) ?></div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Care Staff
                </div>
            </div>
        </div>

        <div class="details-card staff">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Active Staff</div>
                <div class="card-value"><?= number_format($activeStaff) ?></div>
                <div class="card-status status-info">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Working
                </div>
            </div>
        </div>

        <div class="details-card staff">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-info">
                <div class="card-label">On Duty</div>
                <div class="card-value"><?= number_format($onDutyStaff) ?></div>
                <div class="card-status status-warning">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Current Shift
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency & Critical Care -->
    <div class="details-grid">
        <div class="details-card emergency">
            <div class="card-icon">
                <i class="fas fa-ambulance"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Emergency Cases</div>
                <div class="card-value"><?= number_format($emergencyCases) ?></div>
                <div class="card-status status-danger">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Urgent
                </div>
            </div>
        </div>

        <div class="details-card emergency">
            <div class="card-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Critical Cases</div>
                <div class="card-value"><?= number_format($criticalCases) ?></div>
                <div class="card-status status-danger">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Critical
                </div>
            </div>
        </div>

        <div class="details-card emergency">
            <div class="card-icon">
                <i class="fas fa-procedures"></i>
            </div>
            <div class="card-info">
                <div class="card-label">ICU Patients</div>
                <div class="card-value"><?= number_format($icuPatients) ?></div>
                <div class="card-status status-warning">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Intensive Care
                </div>
            </div>
        </div>

        <div class="details-card emergency">
            <div class="card-icon">
                <i class="fas fa-file-medical-alt"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Insurance Claims</div>
                <div class="card-value"><?= number_format($insuranceClaims) ?></div>
                <div class="card-status status-info">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Pending
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="details-grid">
        <div class="details-card financial">
            <div class="card-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Today's Revenue</div>
                <div class="card-value">$<?= number_format($todayRevenue) ?></div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Earnings
                </div>
            </div>
        </div>

        <div class="details-card financial">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Pending Payments</div>
                <div class="card-value">$<?= number_format($pendingPayments) ?></div>
                <div class="card-status status-warning">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Outstanding
                </div>
            </div>
        </div>

        <div class="details-card financial">
            <div class="card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Monthly Revenue</div>
                <div class="card-value">$<?= number_format($totalRevenue) ?></div>
                <div class="card-status status-info">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    This Month
                </div>
            </div>
        </div>

        <div class="details-card financial">
            <div class="card-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="card-info">
                <div class="card-label">Collection Rate</div>
                <div class="card-value">
                    <?= $totalRevenue > 0 ? number_format((($totalRevenue - $pendingPayments) / $totalRevenue) * 100, 1) : 0 ?>%
                </div>
                <div class="card-status status-good">
                    <i class="fas fa-circle" style="font-size: 4px;"></i>
                    Success
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />