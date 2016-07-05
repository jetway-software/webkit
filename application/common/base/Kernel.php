<?php

namespace common\base;

use yii;
use common\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

defined('WEBKIT_ENABLE_ERROR_HANDLER') or define('WEBKIT_ENABLE_ERROR_HANDLER', true);

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
        if (!isset($config['id'])) {
            throw new InvalidConfigException('The "id" configuration for the Application is required.');
        }

        if (isset($config['basePath']) && is_dir($config['basePath'])) {
            $this->app->setBasePath($config['basePath']);
            unset($config['basePath']);
        } else {
            throw new InvalidConfigException('The "basePath" configuration for the Application is required.');
        }

        if (!isset($config['runtimePath'])) {
            $this->app->setRuntimePath($this->app->getBasePath() . '/runtime');
        }

        if (file_exists(Yii::getAlias('@common') . '/config/configuration.php') && file_exists($this->app->getBasePath() . '/config/configuration.php')) {
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

        if (file_exists($this->app->getBasePath() . '/config/configuration-local.php')) {
            $config = ArrayHelper::merge(
                require_once($this->app->getBasePath() . '/config/configuration-local.php'),
                $config
            );
        }

        if (!isset($config['vendorPath'])) {
            $config['vendorPath'] = dirname(dirname($this->app->getBasePath())) . DIRECTORY_SEPARATOR . 'vendor';
        }

        if (!file_exists($config['vendorPath'] . '/autoload.php')) {
            throw new \Exception(
                'Vendor autoload is not found. Please run \'composer install\' under application root directory.'
            );
        }

        if (isset($config['modules']) && array_key_exists('kernel', $config['modules'])) {
            throw new InvalidConfigException(__('kernel', 'Module name "kernel" is reserved by the system and can not be used.'));
        }

        $config['modules']['kernel'] = [
            'class' => $this->installed() ? 'frontend\modules\kernel\Run' : 'backend\modules\kernel\Run',
        ];

        // merge core components with custom components
        foreach ($this->app->coreComponents() as $id => $component) {
            if (!isset($config['components'][$id])) {
                $config['components'][$id] = $component;
            } elseif (is_array($config['components'][$id]) && !isset($config['components'][$id]['class'])) {
                $config['components'][$id]['class'] = $component['class'];
            }
        }

        if (YII_ENABLE_ERROR_HANDLER) {
            echo "Error: YII_ENABLE_ERROR_HANDLER must be false.\n";
            exit(1);
        }

        $this->registerErrorHandler($config);

        if (isset($config['components'])) {
            $this->app->setComponents($config['components']);
            unset($config['components']);
        }
    }

    /**
     * @param array $config
     */
    protected function registerModules(array &$config)
    {
        if (isset($config['modules'])) {
            $data = [];
            if (!file_exists($this->app->getRuntimePath() . '/kernel/modules.php') || !is_array(($data = require_once($this->app->getRuntimePath() . '/kernel/modules.php')))) {
                $this->processModules($config['modules'], $data);
            }

            if ($data['bootstrap']) {
                if (isset($config['bootstrap'])) {
                    $config['bootstrap'] = ArrayHelper::merge($data['bootstrap'], $config['bootstrap']);
                } else {
                    $config['bootstrap'] = $data['bootstrap'];
                }
            }

            if (isset($data['events'])) {
                $this->registerEvents($data['events']);
            }
        }
    }

    /**
     * @param array $modules
     * @param array $data
     * @param null $parent
     */
    protected function processModules(array $modules, array &$data, $parent = null)
    {
        foreach ($modules as $id => $module) {
            if (!isset($module['active']) || $module['active'] === true) {
                $this->app->setModule($id, $module);
                if ($this->app->hasModule($id)) {
                    $uniqueId = $parent ? $parent . '/' . $this->app->getModule($id)->getUniqueId() : $this->app->getModule($id)->getUniqueId();
                    $reflection = new \ReflectionClass($this->app->getModule($id));

                    if ($this->app->getModule($id) instanceof yii\base\BootstrapInterface) {
                        $data['bootstrap'][] = $uniqueId;
                    }

                    if ($reflection->hasMethod('attachEvents')) {
                        $data['events'][] = $this->app->getModule($id)->attachEvents();
                    }

                    if ($sub_modules = $this->app->getModule($id)->getModules()) {
                        $this->processModules($sub_modules, $data, $uniqueId);
                    }
                }
            }
        }
    }

    /**
     * @param array $events
     */
    protected function registerEvents(array $events)
    {
        foreach ($events as $groupEvents) {
            foreach ($groupEvents as $event => $callbacks) {
                if (!is_array($callbacks)) {
                    $callbacks = [$callbacks];
                }
                foreach ($callbacks as $callback) {
                    $this->app->on($event, $callback);
                }
            }
        }
    }

    /**
     * Registers the errorHandler component as a PHP error handler.
     * @param array $config application config
     */
    protected function registerErrorHandler(&$config)
    {
        if (WEBKIT_ENABLE_ERROR_HANDLER) {
            if (!isset($config['components']['errorHandler']['class'])) {
                echo "Error: no errorHandler component is configured.\n";
                exit(1);
            }
            $this->app->set('errorHandler', $config['components']['errorHandler']);
            unset($config['components']['errorHandler']);
            $this->app->getErrorHandler()->register();
        }
    }

    /**
     * @return bool
     */
    public function installed() : bool
    {
        return $this->installed;
    }
}