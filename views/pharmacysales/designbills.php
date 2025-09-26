<?php
// Trigger PDF generation first
exec('php ' . Yii::getAlias('@webroot') . '/pdf/invoice_output.php');
?>

<div style="width: 800px; height: 70vh; border: 1px solid #ccc; overflow: hidden;">
    <iframe src="<?= Yii::$app->request->baseUrl ?>/pdf/invoice_output.php" width="100%" height="100%"
        style="border: none;">
    </iframe>
</div>