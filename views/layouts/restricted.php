<style>
.restricted-message {
    position: fixed;
    top: 5px;
    right: 1%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 20px 24px;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 16px;
    max-width: 380px;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    animation: slideInDown 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    /* position: relative; */
    overflow: hidden;
}

.restricted-message::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    animation: shimmer 3s infinite;
}

.restricted-message .icon {
    font-size: 24px;
    min-width: 32px;
    text-align: center;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    animation: pulse 2s infinite;
}

.restricted-message .content {
    flex: 1;
    line-height: 1.5;
}

.restricted-message .main-message {
    display: block;
    margin-bottom: 6px;
    font-weight: 700;
    font-size: 15px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.restricted-message .status-message {
    display: block;
    font-size: 13px;
    opacity: 0.9;
    font-weight: 400;
    margin-top: 4px;
}

.restricted-message .status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-left: 8px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    animation: glow 2s ease-in-out infinite alternate;
}

.restricted-message .status-active {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.4) 0%, rgba(56, 142, 60, 0.4) 100%);
    color: #c8e6c9;
    box-shadow: 0 0 20px rgba(76, 175, 80, 0.3);
}

.restricted-message .status-maintenance {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.4) 0%, rgba(255, 87, 34, 0.4) 100%);
    color: #ffe0b2;
    box-shadow: 0 0 20px rgba(255, 152, 0, 0.3);
}

.restricted-message .status-restricted {
    background: linear-gradient(135deg, rgba(244, 67, 54, 0.4) 0%, rgba(198, 40, 40, 0.4) 100%);
    color: #ffcdd2;
    box-shadow: 0 0 20px rgba(244, 67, 54, 0.3);
}

.restricted-message .close-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    margin-left: 8px;
    backdrop-filter: blur(10px);
}

.restricted-message .close-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.15) rotate(90deg);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.restricted-message .progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.4) 100%);
    border-radius: 0 0 16px 16px;
    animation: progress 5s linear forwards;
}

@keyframes slideInDown {
    0% {
        transform: translateX(-50%) translateY(-100%) scale(0.8);
        opacity: 0;
    }

    50% {
        transform: translateX(-50%) translateY(10px) scale(1.05);
        opacity: 0.8;
    }

    100% {
        transform: translateX(-50%) translateY(0) scale(1);
        opacity: 1;
    }
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(100%);
    }
}

@keyframes pulse {

    0%,
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
    }

    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
    }
}

@keyframes glow {
    0% {
        box-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
    }

    100% {
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
    }
}

@keyframes progress {
    0% {
        width: 100%;
    }

    100% {
        width: 0%;
    }
}

@media (max-width: 768px) {
    .restricted-message {
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        right: auto;
        max-width: calc(100vw - 30px);
        padding: 18px 20px;
    }

    .restricted-message .icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }

    .restricted-message .main-message {
        font-size: 14px;
    }

    .restricted-message .status-message {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .restricted-message {
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        right: auto;
        max-width: calc(100vw - 20px);
        padding: 16px 18px;
        gap: 12px;
    }

    .restricted-message .icon {
        width: 36px;
        height: 36px;
        font-size: 18px;
    }
}
</style>

<div class="restricted-message" id="restrictedNotice">
    <span class="icon">🔒</span>
    <div class="content">
        <span class="main-message"><?= Yii::$app->view->params['message'] ?? "Module Access" ?></span>
    </div>
    <button class="close-btn" onclick="this.parentElement.style.display='none'">×</button>
    <div class="progress-bar"></div>
</div>

<script>
// Auto-hide notification after 5 seconds
setTimeout(function() {
    const notice = document.getElementById('restrictedNotice');
    if (notice) {
        notice.style.animation = 'slideOutUp 0.5s ease-in forwards';
        setTimeout(() => {
            notice.style.display = 'none';
        }, 100);
    }
}, 2000);

// Add slide out animation
const style = document.createElement('style');
style.textContent = `
     @keyframes slideOutUp {
         0% {
             transform: translateX(-50%) translateY(0) scale(1);
             opacity: 1;
         }
         100% {
             transform: translateX(-50%) translateY(-100%) scale(0.8);
             opacity: 0;
         }
     }
 `;
document.head.appendChild(style);
</script>