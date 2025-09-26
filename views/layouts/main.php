<?php
date_default_timezone_set('Asia/Karachi');

use app\assets\AppAsset;

AppAsset::register($this);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);

$this->registerLinkTag([
    'rel' => 'icon',
    'type' => 'image/x-icon',
    'href' => Yii::getAlias('@web/systemimages/transparent.png'),
]);



?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>webixSystem</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/site.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/styles.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/dashboard.css') ?>" />


    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>
    <?= $this->render('nav') ?>

    <main id="main" class="flex-shrink-0" role="main" style="padding: 20px;">

        <?= $content ?>

    </main>

    <?php $this->endBody() ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function showGlobalAlert(message, type) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type || 'info',
            title: message,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'swal2-toast'
            }
        });
    }
    if (window.jQuery) {
        $(document).ajaxSuccess(function(event, xhr, settings) {
            try {
                var resp = JSON.parse(xhr.responseText);
                if (resp.notification) {
                    var type = resp.type || 'success';
                    showGlobalAlert(resp.notification, type);
                }
            } catch (e) {}
        });
    }
    </script>
</body>

</html>
<?php $this->endPage() ?>