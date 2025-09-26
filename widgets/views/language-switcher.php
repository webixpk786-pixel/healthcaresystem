<?php

use app\components\LanguageManager;

return;
?>

<div class="language-switcher"
    style="position: fixed; <?= $position === 'top-right' ? 'top: 20px; right: 20px;' : ($position === 'top-left' ? 'top: 20px; left: 20px;' : ($position === 'bottom-right' ? 'bottom: 20px; right: 20px;' : ($position === 'bottom-left' ? 'bottom: 30%; left: 20px;' : 'bottom: 20px; left: 20px;'))) ?> z-index: 1000; direction: <?= \app\components\LanguageManager::getDirection() ?>;">

    <?php if ($dropdown): ?>
        <div class="language-dropdown">
            <button class="language-btn" onclick="toggleLanguageDropdown()"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 12px; padding: 10px 16px; cursor: pointer; display: flex; align-items: center; gap: 10px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: all 0.3s ease; transform: scale(1); color: white; font-weight: 600; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s;"
                    class="shimmer-effect"></div>
                <?php if ($showFlags): ?>
                    <span style="font-size: 16px;"><?= $languages[$currentLang]['flag'] ?></span>
                <?php endif; ?>
                <?php if ($showNames): ?>
                    <span
                        style="font-weight: 600; color: white; text-shadow: 0 1px 2px rgba(0,0,0,0.1);"><?= $languages[$currentLang]['name'] ?></span>
                <?php endif; ?>
                <span style="color: rgba(255,255,255,0.8); font-size: 12px; transition: transform 0.3s ease;"
                    class="dropdown-arrow">▼</span>
            </button>

            <div id="languageDropdown" class="language-dropdown-content"
                style="display: none; position: absolute; top: 100%; <?= \app\components\LanguageManager::isRTL() ? 'left: 0;' : 'right: 0;' ?> background: white; border: 1px solid #e0e6f7; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); min-width: 150px; margin-top: 4px; direction: <?= \app\components\LanguageManager::getDirection() ?>;">
                <?php foreach ($languages as $code => $lang): ?>
                    <a href="#" onclick="changeLanguage('<?= $code ?>')" class="language-option"
                        style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; text-decoration: none; color: #232360; transition: background 0.2s; <?= $code === $currentLang ? 'background: #f0f6ff; font-weight: 600;' : '' ?>">
                        <?php if ($showFlags): ?>
                            <span style="font-size: 16px;"><?= $lang['flag'] ?></span>
                        <?php endif; ?>
                        <?php if ($showNames): ?>
                            <span><?= $lang['name'] ?></span>
                        <?php endif; ?>
                        <?php if ($code === $currentLang): ?>
                            <span style="margin-left: auto; color: #646cff;">✓</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="language-buttons" style="display: flex; gap: 4px;">
            <?php foreach ($languages as $code => $lang): ?>
                <button onclick="changeLanguage('<?= $code ?>')" class="language-btn-small"
                    style="background: <?= $code === $currentLang ? '#646cff' : 'white' ?>; color: <?= $code === $currentLang ? 'white' : '#232360' ?>; border: 1px solid #e0e6f7; border-radius: 6px; padding: 6px 8px; cursor: pointer; font-size: 12px; transition: all 0.2s;">
                    <?php if ($showFlags): ?>
                        <span style="font-size: 14px;"><?= $lang['flag'] ?></span>
                    <?php endif; ?>
                    <?php if ($showNames): ?>
                        <span style="margin-left: 4px;"><?= $code ?></span>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    <?php if (\app\components\LanguageManager::isRTL()): ?>

    /* RTL Support for Language Switcher */
    .language-switcher {
        direction: rtl;
    }

    .language-btn {
        direction: rtl;
        text-align: right;
    }

    .language-dropdown-content {
        direction: rtl;
        text-align: right;
    }

    .language-option {
        direction: rtl;
        text-align: right;
    }

    .language-buttons {
        direction: rtl;
    }

    <?php endif;
    ?>

    /* Popping Animation Effects */
    .language-btn {
        animation: popIn 0.6s ease-out;
    }

    .language-btn:hover {
        transform: scale(1.05) translateY(-2px) !important;
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4) !important;
    }

    .language-btn:hover .shimmer-effect {
        left: 100%;
    }

    .language-btn:hover .dropdown-arrow {
        transform: rotate(180deg);
    }

    .language-btn:active {
        transform: scale(0.95) !important;
        transition: transform 0.1s ease;
    }

    .language-dropdown-content {
        animation: slideDown 0.3s ease-out;
        transform-origin: top center;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
    }

    .language-option {
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .language-option:hover {
        background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .language-option::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.3s ease;
    }

    .language-option:hover::before {
        left: 100%;
    }

    .language-btn-small:hover {
        transform: scale(1.1) translateY(-1px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    /* Keyframe Animations */
    @keyframes popIn {
        0% {
            opacity: 0;
            transform: scale(0.8) translateY(20px);
        }

        50% {
            transform: scale(1.1) translateY(-5px);
        }

        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes slideDown {
        0% {
            opacity: 0;
            transform: scaleY(0) translateY(-10px);
        }

        100% {
            opacity: 1;
            transform: scaleY(1) translateY(0);
        }
    }

    /* Pulse effect for attention */
    .language-switcher {
        animation: gentlePulse 3s ease-in-out infinite;
    }

    @keyframes gentlePulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.02);
        }
    }

    .language-dropdown-content a:hover {
        background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
    }
</style>

<script>
    function toggleLanguageDropdown() {
        var dropdown = document.getElementById('languageDropdown');
        var btn = document.querySelector('.language-btn');

        if (dropdown.style.display === 'none') {
            dropdown.style.display = 'block';
            btn.style.transform = 'scale(1.05)';
            setTimeout(() => {
                btn.style.transform = 'scale(1)';
            }, 200);
        } else {
            dropdown.style.display = 'none';
            btn.style.transform = 'scale(0.95)';
            setTimeout(() => {
                btn.style.transform = 'scale(1)';
            }, 200);
        }
    }

    function changeLanguage(langCode) {
        // Add pop effect to the button
        var btn = document.querySelector('.language-btn');
        btn.style.transform = 'scale(0.9)';
        setTimeout(() => {
            btn.style.transform = 'scale(1.1)';
            setTimeout(() => {
                btn.style.transform = 'scale(1)';
            }, 150);
        }, 100);

        // Close dropdown
        document.getElementById('languageDropdown').style.display = 'none';

        // Show loading
        if (typeof showGlobalAlert === 'function') {
            showGlobalAlert('Changing language...', 'info');
        }

        // Send AJAX request to change language
        $.ajax({
            url: 'index.php?r=users/changelanguage',
            method: 'POST',
            data: {
                language: langCode
            },
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    // Reload page to apply new language
                    location.reload();
                } else {
                    if (typeof showGlobalAlert === 'function') {
                        showGlobalAlert(resp.message || 'Failed to change language.', 'error');
                    }
                }
            },
            error: function() {
                if (typeof showGlobalAlert === 'function') {
                    showGlobalAlert('Failed to change language.', 'error');
                }
            }
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        var dropdown = document.getElementById('languageDropdown');
        var btn = document.querySelector('.language-btn');
        if (dropdown && !dropdown.contains(event.target) && !btn.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>