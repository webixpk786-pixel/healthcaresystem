<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<style>
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .site-error-bg {
        /* min-height: 100vh; */
        /* height: 100vh; */
        width: 100vw;
        /* background: linear-gradient(135deg, #e3f0ff 0%, #f8fafc 100%); */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin: 0;
        overflow: hidden;
    }

    .site-error-icon {
        font-size: 92px;
        color: #1976d2;
        margin-bottom: 24px;
        display: inline-block;
    }

    .site-error-title {
        font-size: 2.8rem;
        font-weight: 800;
        color: #222;
        margin-bottom: 18px;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 12px rgba(25, 118, 210, 0.08);
    }

    .site-error-message {
        font-size: 1.25rem;
        color: #d32f2f;
        background: rgba(255, 243, 243, 0.7);
        border-radius: 12px;
        padding: 16px 28px;
        margin-bottom: 22px;
        /* border: 1.5px solid #ffd6d6; */
        word-break: break-word;
        box-shadow: 0 2px 12px 0 rgba(211, 47, 47, 0.04);
        max-width: 600px;
    }

    .site-error-desc {
        color: #555;
        font-size: 1.08rem;
        margin-bottom: 10px;
    }

    .site-error-contact {
        color: #888;
        font-size: 1.01rem;
        margin-bottom: 18px;
    }

    .site-error-btn {
        margin-top: 18px;
        display: inline-block;
        background: #1976d2;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 12px 36px;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px 0 rgba(25, 118, 210, 0.10);
        cursor: pointer;
        text-decoration: none;
        transition: background 0.18s, box-shadow 0.18s;
    }

    .site-error-btn:hover {
        background: #1256a3;
        box-shadow: 0 4px 16px 0 rgba(25, 118, 210, 0.13);
    }
</style>
<div class="site-error-bg">
    <div class="site-error-icon">
        <svg width="92" height="92" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" fill="#e3f0ff" />
            <path d="M12 8v4" stroke="#1976d2" stroke-width="2.5" stroke-linecap="round" />
            <circle cx="12" cy="16" r="1.7" fill="#1976d2" />
        </svg>
    </div>
    <div class="site-error-title">
        <?= Html::encode($this->title) ?>
    </div>
    <div class="site-error-message">
        <?= nl2br(Html::encode($message)) ?>
    </div>
    <div class="site-error-desc">
        The above error occurred while the Web server was processing your request.
    </div>
    <div class="site-error-contact">
        Please contact us if you think this is a server error. Thank you.
    </div>
    <a href="index.php" class="site-error-btn">Go to Dashboard</a>
</div>