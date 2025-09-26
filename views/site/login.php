<?php

$error = isset(
    Yii::$app->session
) && Yii::$app->session->hasFlash('error') ? Yii::$app->session->getFlash('error') : '';
?>

<link rel="stylesheet" href="css/styles.css" />
<div class="center-screen">
    <form class="card" method="post" action="index.php?r=site/login">
        <input type="hidden" name="_csrf-token" value="<?= Yii::$app->request->csrfToken ?>">
        <h1>Login Required</h1>
        <h2>Please enter your username and password to continue</h2>
        <input class="input" type="text" name="username" placeholder="Username or Email" required autofocus />
        <input class="input" type="password" name="password" placeholder="Password" required />
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <button class="btn" type="submit">Login</button>
    </form>
</div>