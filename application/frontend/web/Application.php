<?php

namespace frontend\web;

use common\helpers\ArrayHelper;

class Application extends \common\web\Application
{
    /**
     * @inheritdoc
     */
    protected function getKernelConfig() : array
    {
        $config = [
            'id' => 'frontend',
            'basePath' => dirname(__DIR__),
            'vendorPath' => dirname(APP_PATH) . DIRECTORY_SEPARATOR . 'vendor',
            'bootstrap' => [
                'kernel'
            ]
        ];

        if (YII_ENV_DEV) {
            $config['bootstrap'][] = 'debug';
            $config['modules']['debug'] = 'yii\debug\Module';

            $config['bootstrap'][] = 'gii';
            $config['modules']['gii'] = 'yii\gii\Module';
        }

        if ($main_local = $this->getKernelConfigFile('web', 'frontend')) {
            $config = ArrayHelper::merge($config, $main_local);
        }

        return $config;
    }
}