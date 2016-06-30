<?php

namespace console;

use common\helpers\ArrayHelper;

class Application extends \yii\console\Application
{
    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config = ArrayHelper::merge($config, $this->getConfig());

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function getConfig() : array
    {
        $config = [
            'id' => 'frontend',
            'basePath' => dirname(__DIR__),
            'runtimePath' => __DIR__ . DIRECTORY_SEPARATOR . 'runtime'
        ];

        if (YII_ENV_DEV) {
            $config['bootstrap'][] = 'debug';
            $config['modules']['debug'] = 'yii\debug\Module';

            $config['bootstrap'][] = 'gii';
            $config['modules']['gii'] = 'yii\gii\Module';
        }

        if ($main_local = $this->getConfigFile('app')) {
            $config = ArrayHelper::merge($config, $main_local);
        }

        return $config;
    }

    /**
     * @param $file
     * @return array
     */
    protected function getConfigFile($file) : array
    {
        $path = APP_PATH . DIRECTORY_SEPARATOR . 'config';
        $file = $path . DIRECTORY_SEPARATOR . $file . '.php';

        if (file_exists($file)) {
            return require $file;
        }

        return [];
    }
}