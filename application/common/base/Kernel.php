<?php

namespace common\base;

use yii;
use common\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

class Kernel
{
    /**
     * @var
     */
    private $app;
    /**
     * @var bool
     */
    private $installed = false;

    /**
     * Kernel constructor.
     * @param $application
     * @param array $config
     */
    public function __construct(\yii\base\Application $application, array &$config)
    {
        $this->app = $application;

        $this->preInit($config);

        $this->registerModules($config);
    }

    /**
     * @param array $config
     * @throws InvalidConfigException
     * @throws \Exception
     */
    protected function preInit(array &$config)
    {
        if (!isset($config['basePath']) || !is_dir($config['basePath'])) {
            throw new InvalidConfigException('The "basePath" configuration for the Application is required.');
        }

        if (!isset($config['runtimePath'])) {
            $this->app->setRuntimePath($config['basePath'] . '/runtime');
        }

        if (file_exists(Yii::getAlias('@common') . '/config/configuration.php') && file_exists($config['basePath'] . '/config/configuration.php')) {
            $this->initialized = true;
            $config = ArrayHelper::merge(
                require_once(Yii::getAlias('@common') . '/config/configuration.php'),
                require_once($config['basePath'] . '/config/configuration.php'),
                $config
            );
        }

        if (file_exists(Yii::getAlias('@common') . '/config/configuration-local.php')) {
            $config = ArrayHelper::merge(
                require_once(Yii::getAlias('@common') . '/config/configuration-local.php'),
                $config
            );
        }

        if (file_exists($config['basePath'] . '/config/configuration-local.php')) {
            $config = ArrayHelper::merge(
                require_once($config['basePath'] . '/config/configuration-local.php'),
                $config
            );
        }

        if (!isset($config['vendorPath'])) {
            $config['vendorPath'] = dirname(dirname($config['basePath'])) . DIRECTORY_SEPARATOR . 'vendor';
        }

        if (!file_exists($config['vendorPath'] . '/autoload.php')) {
            throw new \Exception(
                'Vendor autoload is not found. Please run \'composer install\' under application root directory.'
            );
        }

        if (isset($config['modules']) && array_key_exists('kernel', $config['modules'])) {
            throw new InvalidConfigException(__('kernel', 'Module name "kernel" is reserved by the system and can not be used.'));
        }

        if (!isset($config['bootstrap'])) {
            $config['bootstrap'] = ['kernel'];
        } else {
            if (!is_array($config['bootstrap'])) {
                $config['bootstrap'] = (array)$config['bootstrap'];
            }

            $config['bootstrap'] = ArrayHelper::merge(['kernel'], $config['bootstrap']);
        }

        $config['modules']['kernel'] = [
            'class' => $this->installed() ? 'frontend\modules\kernel\Run' : 'backend\modules\kernel\Run'
        ];
    }

    /**
     * @param array $config
     */
    protected function registerModules(array &$config)
    {
        if (isset($config['modules'])) {
            $modulesCache = $this->app->getRuntimePath() . '/kernel/modules.php';

            if (file_exists($modulesCache) && ($modules = require_once($this->app->getRuntimePath() . '/kernel/modules.php'))) {
                if (isset($modules['events'])) {
                    $this->registerEvents($modules['events']);
                }
            } else {
                $events = [];
                foreach ($config['modules'] as $id => $module) {
                    if (!isset($module['active']) || $module['active'] === true) {
                        $this->app->setModule($id, $module);
                        if ($this->app->hasModule($id)) {
                            //TODO сделать добавлени евентов
                        }
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function registerEvents(array $handlers)
    {

    }

    /**
     * @return bool
     */
    public function installed() : bool
    {
        return $this->installed;
    }
}