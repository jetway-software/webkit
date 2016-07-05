<?php

namespace common\web;

use yii;
use common\base\Kernel;
use common\base\Config;
use yii\base\InvalidConfigException;

abstract class Application extends \yii\web\Application
{
    /**
     * @var
     */
    private $_kernel;

    /**
     * @param array $config
     * @throws InvalidConfigException
     */
    public function preInit(&$config)
    {
        $this->initKernel($config);
    }

    /**
     * @param array $config
     */
    protected function initKernel(array &$config)
    {
        $this->_kernel = new Kernel($this, $config);
    }

    /**
     * Returns the kernel component.
     * @return Kernel the kernel component.
     */
    public function kernel()
    {
        return $this->_kernel;
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
}