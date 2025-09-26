<?php
$user = Yii::$app->user->identity;
$attr = $user->attributes;
$last_login = $attr['last_login_at'] ?? null;
$last_login = date('d M Y H:i:s', strtotime($last_login));

// Fetch hospital statistics
try {
    // Total patients count
    $totalPatients = Yii::$app->db->createCommand('SELECT COUNT(*) FROM patients WHERE is_deleted = 0')->queryScalar() ?: 0;

    // Active appointments count
    $activeAppointments = Yii::$app->db->createCommand('SELECT COUNT(*) FROM appointments WHERE status = "scheduled" AND appointment_date >= CURDATE()')->queryScalar() ?: 0;

    // Available beds count
    $availableBeds = Yii::$app->db->createCommand('SELECT COUNT(*) FROM beds WHERE status = "available" AND is_deleted = 0')->queryScalar() ?: 0;

    // Total doctors count
    $totalDoctors = Yii::$app->db->createCommand('SELECT COUNT(*) FROM doctors WHERE is_deleted = 0')->queryScalar() ?: 0;

    // Emergency cases count
    $emergencyCases = Yii::$app->db->createCommand('SELECT COUNT(*) FROM emergency_cases WHERE status = "active" AND created_at >= CURDATE()')->queryScalar() ?: 0;

    // Revenue today
    $todayRevenue = Yii::$app->db->createCommand('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_date = CURDATE()')->queryScalar() ?: 0;
} catch (Exception $e) {
    // Fallback values if tables don't exist yet
    $totalPatients = 0;
    $activeAppointments = 0;
    $availableBeds = 0;
    $totalDoctors = 0;
    $emergencyCases = 0;
    $todayRevenue = 0;
}
?>

<div class="col-md-12">
    <div class="dashboard-flex-center">
        <div style="flex: 4 1 0%; display: flex; align-items: center; justify-content: center;">
            <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                <div style="margin-bottom: 18px; text-align: center;">
                    <div style="color: rgb(100, 108, 255); font-size: 17px; margin-top: 6px; font-weight: 500;">
                        Hospital Operations Dashboard</div>
                    <div style="color: rgb(136, 136, 136); font-size: 14px; margin-top: 6px;">Last login:
                        <?= $last_login ?> | System Status: <span style="color: rgb(76, 175, 80); font-weight: 600;">All
                            Systems Operational</span>
                    </div>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 18px;  justify-content: center; align-items: stretch;">

                    <!-- Total Patients -->
                    <div
                        style="background: rgb(245, 246, 250); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(100, 108, 255, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(230, 234, 255); border-radius: 8px; padding: 6px;">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#646cff" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(100, 108, 255);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85; font-size: 11px;">Total
                                Patients</div>
                            <div style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px;">
                                <?= number_format($totalPatients) ?></div>
                        </div>
                    </div>

                    <!-- Active Appointments -->
                    <div
                        style="background: rgb(239, 246, 255); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(59, 130, 246, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(219, 234, 254); border-radius: 8px; padding: 6px;">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#3b82f6" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(59, 130, 246);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85; font-size: 11px;">
                                Appointments</div>
                            <div
                                style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px; color: rgb(59, 130, 246);">
                                <?= $activeAppointments ?></div>
                        </div>
                    </div>

                    <!-- Available Beds -->
                    <div
                        style="background: rgb(240, 253, 244); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(34, 197, 94, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(220, 252, 231); border-radius: 8px; padding: 6px;">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#22c55e" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(34, 197, 94);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M21 10c-1.1 0-2 .9-2 2v3H5v-3c0-1.1-.9-2-2-2s-2 .9-2 2v5c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2v-5c0-1.1-.9-2-2-2zm-3-5H6c-1.1 0-2 .9-2 2v2.15c1.16.41 2 1.53 2 2.81V14h12v-2.04c0-1.28.84-2.4 2-2.81V7c0-1.1-.9-2-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85; font-size: 11px;">Available
                                Beds</div>
                            <div
                                style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px; color: rgb(34, 197, 94);">
                                <?= $availableBeds ?></div>
                        </div>
                    </div>

                    <!-- Total Doctors -->
                    <div
                        style="background: rgb(255, 251, 235); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(245, 158, 11, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(254, 243, 199); border-radius: 8px; padding: 6px;">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#f59e0b" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(245, 158, 11);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85; font-size: 11px;">Total
                                Doctors</div>
                            <div
                                style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px; color: rgb(245, 158, 11);">
                                <?= $totalDoctors ?></div>
                        </div>
                    </div>

                    <!-- Emergency Cases -->
                    <div
                        style="background: rgb(255, 245, 245); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(239, 68, 68, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(254, 226, 226); border-radius: 8px; padding: 6px;">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#ef4444" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(239, 68, 68);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85; font-size: 11px;">Emergency
                            </div>
                            <div
                                style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px; color: rgb(239, 68, 68);">
                                <?= $emergencyCases ?></div>
                        </div>
                    </div>

                    <!-- Today's Revenue -->
                    <div
                        style="background: rgb(245, 243, 255); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(147, 51, 234, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(233, 213, 255); border-radius: 8px; padding: 6px;">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#9333ea" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(147, 51, 234);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85; font-size: 11px;">
                                Revenue</div>
                            <div
                                style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px; color: rgb(147, 51, 234);">
                                <?= number_format($todayRevenue) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>