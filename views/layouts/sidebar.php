<?php
date_default_timezone_set('Asia/Karachi');

use app\assets\AppAsset;

$parent_id = Yii::$app->view->params['parent_id'] ?? 1;

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
<html lang="<?= \app\components\LanguageManager::getCurrentLanguage() ?>"
    dir="<?= \app\components\LanguageManager::getDirection() ?>">



<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>webixSystem</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/site.css') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/styles.css?v=1') ?>">
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/dashboard.css') ?>" />

    <style>
    <?php if (\app\components\LanguageManager::isRTL()): ?>

    /* RTL Support for Layout */
    html,
    body {
        direction: rtl;
        text-align: right;
    }

    .users-layout-content {
        direction: rtl;
    }

    .users-layout-sidebar {
        direction: rtl;
    }

    <?php endif;

    ?>html,
    body {
        height: 100%;
        min-height: 100vh;
        overflow: hidden;
    }

    .users-layout-root {
        margin-top: 5%;
        display: flex;
        min-height: 100vh;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
    }

    .users-layout-sidebar {
        width: 240px;
        min-width: 220px;
        max-width: 260px;
        padding: 0;
        position: sticky;
        top: 0;
        height: 100vh;
        z-index: 10;
        display: flex;
        flex-direction: column;
        overflow: visible;
    }

    .users-layout-content {
        flex: 1 1 0%;
        padding: 40px 48px 32px 48px;
        min-width: 0;
        height: 100vh;
        /* background: #f5f7fa; */
        overflow: visible;
    }

    @media (max-width: 900px) {
        .users-layout-sidebar {
            display: none;
        }

        .users-layout-content {
            padding: 24px 8px;
        }
    }
    </style>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <?= $this->render('nav') ?>

    <?php if (Yii::$app->session->hasFlash('notification')): ?>
    <div class="notification notification--show" id="notification-box"
        style="position:fixed;top:30px;right:30px;z-index:9999;background:#646cff;color:#fff;padding:16px 32px;border-radius:8px;font-size:16px;box-shadow:0 2px 12px rgba(100,108,255,0.18);">
        <?= htmlspecialchars(Yii::$app->session->getFlash('notification')) ?>
        <button class="notification__close"
            onclick="document.getElementById('notification-box').style.display='none'">&times;</button>
    </div>
    <?php endif; ?>

    <div class="users-layout-root">
        <div class="users-layout-sidebar">
            <?= $this->render('sidebar_menu', ['parent_id' => $parent_id]) ?>
        </div>
        <div class="users-layout-content">
            <?= $content ?>
        </div>
    </div>
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