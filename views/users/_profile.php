<?php

$role = Yii::$app->db->createCommand('SELECT * FROM modules WHERE id = 1 AND parent_id IS NULL AND id_deleted = 0')->queryOne();
$initials = strtoupper(mb_substr($role['name'], 0, 1));
$icon = $role['icon'] ?? 'fa fa-user';
?>

<div class="col-md-3">
    <div
        style="width: 100%; padding: 28px 24px 24px; border-radius: 18px; display: flex; flex-direction: column; gap: 0px;">
        <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 10px;">
            <div
                style="width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: rgb(255, 255, 255); font-weight: 800; font-size: 30px; border: 3px solid rgb(255, 255, 255); flex-shrink: 0; overflow: hidden;">
                <img alt="Profile" src="supersystem/web/systemimages/transparent.png"
                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: none;">
                <div
                    style="display: flex; width: 100%; background: rgb(100, 108, 255); height: 100%; box-shadow: rgba(100, 108, 255, 0.13) 0px 2px 8px; align-items: center; justify-content: center;">
                    <?= $initials ?></div>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: center;">
                <div style="font-weight: 900; color: rgb(35, 35, 96); font-size: 22px; line-height: 1.2;">
                    <?= $role['name'] ?>
                </div>
                <span
                    style="display: inline-block; background: rgb(230, 234, 255); color: rgb(100, 108, 255); font-weight: 600; font-size: 13px; border-radius: 12px; padding: 2px 12px; margin-top: 6px; vertical-align: middle; text-transform: capitalize;">
                    <i class="<?= $icon ?>"></i>
                </span>
            </div>
        </div>
        <div style="height: 1px; background: rgb(240, 240, 247); margin: 0px 0px 10px; width: 100%;"></div>
        <div style="display: flex; gap: 8px;">
            <span style="color: rgb(85, 85, 85); font-size: 14px; text-align: left;">
                <?= $role['description'] ?>
            </span>
        </div>
    </div>
</div>