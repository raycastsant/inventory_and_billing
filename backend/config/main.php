<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'language' => 'es',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\formatter',
            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
            'currencyCode' => '$',
        ]
    ],
    'modules' => [
        'inventario' => [
            'class' => 'backend\modules\Inventario\Inventario',
            ],
        'nomencladores' => [
            'class' => 'backend\modules\Nomencladores\Nomencladores',
            ],
        'facturacion' => [
            'class' => 'backend\modules\Facturacion\Facturacion',
            ],
        'economia' => [
            'class' => 'backend\modules\Economia\Economia',
            ],
        'reportes' => [
            'class' => 'backend\modules\Reportes\Reportes',
            ],
        'migracion' => [
                'class' => 'backend\modules\Migracion\Migracion',
                ],
        /*'gridview' => [
            'class' => '\kartik\grid\Module',
            ]*/
    ],
    'params' => $params,
];
