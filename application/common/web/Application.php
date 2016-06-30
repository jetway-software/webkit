<?php

namespace common\web;

use yii;
use common\base\Config;
use common\helpers\ArrayHelper;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\web\NotFoundHttpException;

abstract class Application extends \yii\web\Application
{
    const KERNEL_CACHE_MODULES_EVENTS_KEY = 'app:kernel:modules:events';
    const KERNEL_CACHE_MODULES_RULES_KEY = 'app:kernel:modules:rules';

    /**
     * @var array
     */
    private $_handlers = [];
    /**
     * @var bool
     */
    private $_isInstalled;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config = ArrayHelper::merge($config, $this->getKernelConfig());

        parent::__construct($config);
    }

    /**
     * @param array $config
     * @throws InvalidConfigException
     */
    public function preInit(&$config)
    {
        parent::preInit($config);

        $this->initKernel($config);
    }

    /**
     * @return array
     */
    abstract protected function getKernelConfig() : array;

    /**
     * @param $file
     * @param $from
     * @return array
     */
    protected function getKernelConfigFile($file, $from) : array
    {
        $path = APP_PATH . DIRECTORY_SEPARATOR . $from . DIRECTORY_SEPARATOR . 'config';
        $file = $path . DIRECTORY_SEPARATOR . $file . '.php';

        if (file_exists($file)) {
            return require $file;
        }

        return [];
    }

    /**
     * @param $config
     * @throws InvalidConfigException
     */
    protected function initKernel(&$config)
    {
        if (array_key_exists('kernel', $config['modules'])) {
            throw new InvalidConfigException(__('kernel', 'Module name "kernel" is reserved by the system and can not be used.'));
        }

        if (isset($config['components']['cache'])) {
            $this->set('cache', $config['components']['cache']);
            unset($config['components']['cache']);
        } else {
            $this->set('cache', ['class' => 'yii\caching\FileCache']);
        }

        if (!$this->isInstalled()) {
            $this->setModule('kernel', 'backend\modules\kernel\Run');
            $this->catchAll = ['kernel/setup/index'];
        } else {
            if (isset($config['components']['urlManager'])) {
                $this->set('urlManager', $config['components']['urlManager']);
                unset($config['components']['urlManager']);
            } else {
                $this->set('cache', ['class' => 'common\web\UrlManager']);
            }

            $this->setModule('kernel', 'frontend\modules\kernel\Run');

            $events = $this->getCache()->get(self::KERNEL_CACHE_MODULES_EVENTS_KEY);

            if ($events === false) {
                $this->registerModules($config, true);
                $this->getCache()->set(self::KERNEL_CACHE_MODULES_EVENTS_KEY, $this->_handlers);
            } else {
                $this->registerModules($config, false);
                $this->_handlers = $events;
            }

            $this->registerHandlers($this->_handlers);
        }
    }

    /**
     * @param $config
     * @param bool $loadEvents
     */
    protected function registerModules(&$config, $loadEvents = true)
    {
        $modules = is_array($config['modules']) ? $config['modules'] : [];
        $path = $this->getBasePath() . DIRECTORY_SEPARATOR . 'config';

        if (file_exists($path . DIRECTORY_SEPARATOR . 'web-modules.php')) {
            $modulesList = require $path . DIRECTORY_SEPARATOR . 'web-modules.php';
            if (is_array($modulesList)) {
                $modules = ArrayHelper::merge($modules, $modulesList);
            }
        }

        $rules = $this->getCache()->get(self::KERNEL_CACHE_MODULES_RULES_KEY);
        $loadRules = $rules === false;

        foreach ($modules as $id => $module) {
            $this->registerModule($id, $module, $loadEvents, $loadRules);
        }

        if ($loadRules === false) {
            $this->getUrlManager()->addRules($rules);
        } else {
            $this->getCache()->set(self::KERNEL_CACHE_MODULES_RULES_KEY, $this->getUrlManager()->rules);
        }

        unset($config['modules']);
    }

    /**
     * @inheritdoc
     */
    protected function registerModule($id, $module, $loadEvents = true, $loadRules = true)
    {
        $class = null;
        switch (gettype($module)) {
            case "string":
                $class = $module;
                break;

            case "array":
                if (isset($module["class"])) {
                    $class = $module["class"];
                }
                break;

            case "object":
                $class = get_class($module);
                break;
        }

        if (is_string($class) && class_exists($class, true)) {
            $this->setModule($id, $module);

            if ($loadRules === true) {
                $modulePath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $id;

                if (file_exists($modulePath . DIRECTORY_SEPARATOR . 'rules.php') && is_array(($moduleRules = require($modulePath . DIRECTORY_SEPARATOR . 'rules.php')))) {
                    $this->getUrlManager()->addRules($moduleRules, false);
                }
            }

            if ($loadEvents === true) {
                $this->registerEvents($class);
            }
        }
    }

    /**
     * @param $class
     */
    protected function registerEvents($class)
    {
        if (class_exists($class) && method_exists($class, 'attachEvents')) {
            $methodChecker = new \ReflectionMethod($class, 'attachEvents');
            if ($methodChecker->isStatic()) {
                $this->_handlers[] = $class::attachEvents();
            } else {
                throw new InvalidCallException('Method attachEvents must be a static.');
            }
        }
    }

    /**
     * Регистрирует обработчики событий
     * @param array $handlers
     */
    protected function registerHandlers(array $handlers)
    {
        foreach ($handlers as $groupEvents) {
            foreach ($groupEvents as $event => $callbacks) {
                if (!is_array($callbacks)) {
                    $callbacks = [$callbacks];
                }
                foreach ($callbacks as $callback) {
                    $this->on($event, $callback);
                }
            }
        }
    }

    /**
     * Returns the user component.
     * @return Config the user component.
     */
    public function getConfig()
    {
        return $this->get('config');
    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'view' => ['class' => 'common\web\View'],
            'request' => ['class' => 'common\web\Request'],
            'urlManager' => ['class' => 'common\web\UrlManager'],
            'config' => ['class' => 'common\base\Config'],
            'user' => ['class' => 'common\web\User'],
        ]);
    }

    /**
     * @return boolean
     */
    public function isInstalled()
    {
        if ($this->_isInstalled === null) {
            $this->_isInstalled = file_exists(Yii::getAlias('@common/config/kernel.php'));

            if ($this->_isInstalled === true && !is_array(($kernel = require(Yii::getAlias('@common/config/kernel.php'))))) {
                $this->_isInstalled = false;
            }
        }

        return $this->_isInstalled;
    }
}