<?php
$user = Yii::$app->user->identity;
$attr = $user->attributes;
$last_login = $attr['last_login_at'] ?? null;
$last_login = date('d M Y H:i:s', strtotime($last_login));
$users = Yii::$app->db->createCommand('SELECT COUNT(*) FROM users WHERE id_deleted = 0')->queryScalar();
$roles = Yii::$app->db->createCommand('SELECT COUNT(*) FROM roles WHERE id_deleted = 0')->queryScalar();
$last_updated = Yii::$app->db->createCommand('SELECT MAX(created_at) as last_update FROM `activity_logs`  WHERE location = "Users" LIMIT 1')->queryScalar();
$last_updated = date('d M Y H:i:s', strtotime($last_updated));
?>
<div class="col-md-5">
    <div class="dashboard-flex-center">
        <div style="flex: 4 1 0%; display: flex; align-items: center; justify-content: center;">
            <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                <div style="margin-bottom: 18px; padding: 0px 20px; text-align: center;">
                    <div style="color: rgb(100, 108, 255); font-size: 17px; margin-top: 6px; font-weight: 500;">
                        All your core operations, at a glance</div>
                    <div style="color: rgb(136, 136, 136); font-size: 14px; margin-top: 6px;">
                        Last updated: <?= $last_updated ?>
                    </div>
                </div>

                <div
                    style="display: flex; flex-wrap: wrap; gap: 18px; margin: 0px 40px 18px; justify-content: center; align-items: stretch;">
                    <div
                        style="background: rgb(245, 246, 250); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(100, 108, 255, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(230, 234, 255); border-radius: 8px; padding: 6px;"><svg
                                stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#646cff" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(100, 108, 255);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3 1 9l11 6 9-4.91V17h2V9L12 3z">
                                </path>
                            </svg></div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85;">Roles</div>
                            <div style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px;">
                                <?= $roles ?> </div>
                        </div>
                    </div>
                    <div
                        style="background: rgb(245, 246, 250); color: rgb(35, 35, 96); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; box-shadow: rgba(100, 108, 255, 0.06) 0px 1px 4px; font-weight: 600; font-size: 15px; min-width: 155px; flex: 1 1 125px; max-width: 180px;">
                        <div style="background: rgb(230, 234, 255); border-radius: 8px; padding: 6px;"><svg
                                stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                color="#646cff" height="20" width="20" xmlns="http://www.w3.org/2000/svg"
                                style="color: rgb(100, 108, 255);">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z">
                                </path>
                            </svg></div>
                        <div>
                            <div style="text-align: left; font-size: 13px; opacity: 0.85;">Users</div>
                            <div style="text-align: left; font-size: 18px; font-weight: 800; margin-top: 2px;">
                                <?= $users ?> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>