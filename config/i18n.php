<?php

return [
    'sourceLanguage' => 'en',
    'language' => 'en',
    'translations' => [
        'app*' => [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/messages',
            'fileMap' => [
                'app' => 'app.php',
                'app/error' => 'error.php',
                'app/settings' => 'settings.php',
                'app/users' => 'users.php',
                'app/modules' => 'modules.php',
                'app/roles' => 'roles.php',
            ],
        ],
        'yii' => [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@app/messages',
        ],
    ],
];
