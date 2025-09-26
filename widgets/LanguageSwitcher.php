<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\components\LanguageManager;

class LanguageSwitcher extends Widget
{
    public $showFlags = true;
    public $showNames = true;
    public $dropdown = true;
    public $position = 'top-right'; // top-right, top-left, bottom-right, bottom-left

    public function run()
    {
        $currentLang = LanguageManager::getCurrentLanguage();
        $languages = LanguageManager::getAvailableLanguages();

        return $this->render('language-switcher', [
            'currentLang' => $currentLang,
            'languages' => $languages,
            'showFlags' => $this->showFlags,
            'showNames' => $this->showNames,
            'dropdown' => $this->dropdown,
            'position' => $this->position
        ]);
    }
}
