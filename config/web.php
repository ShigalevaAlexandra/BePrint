<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',

    //--add name and language-//
    'name'=>'BePrint',
    'language'=>'ru-RU',
    //------------------------//

    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [

        //--add cookieValidationKey and parsers--------------//
        'request' => [
            'cookieValidationKey' => 'shigaleva',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],        
        ],
        //---------------------------------------------------//

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false, 

        //--edit rules for urlManager------------------//
        'rules' => [
            'POST registration'=>'users/create',
            'POST authorization'=>'users/login',
            'GET account' => 'users/view',
            'POST services/add' => 'services/create',
            'GET services' => 'services/view',
            'PATCH services/edit' => 'services/edit',
            'GET services/search' => 'services/search',
            'POST cart/add' => 'cart/new',
            'GET cart' =>'cart/items',
            'DELETE cart/del' => 'cart/delete',
            'POST order/add' => 'orders/create',
            'GET orders' => 'orders/view',
            ],
        ],
        //---------------------------------------------//
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        
        //--add '*' for allowedIPs----------------//
        'allowedIPs' => ['127.0.0.1', '::1','*'],
        //----------------------------------------//

    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        
        //--add '*' for allowedIPs----------------//
        'allowedIPs' => ['127.0.0.1', '::1','*'],
        //----------------------------------------//

    ];
}

return $config;