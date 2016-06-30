<?php

namespace common\base;

use yii;
use yii\base\Component;
use common\helpers\ArrayHelper;
use common\helpers\FunctionHelper;

class Config extends Component
{
    /**
     * @var
     */
    public $configFile = [
        '@common/config/params.php',
        '@app/config/params.php'
    ];
    /**
     * @var
     */
    private $_config;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->loadConfig();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $default = null;
        $args = func_get_args();

        if (func_num_args() === 1 && is_array($args[0])) {
            $args = $args[0];
        }

        foreach ($args as $index => $key) {
            if ($index === 0 && array_key_exists($key, $this->_config)) {
                $default = $this->_config[$key];
            } elseif ($index > 0 && is_array($default) && array_key_exists($key, $default)) {
                $default = $default[$key];
            }
        }

        return $default;
    }

    /**
     * @return bool
     */
    public function has() : bool
    {
        $args = func_get_args();

        return !FunctionHelper::isEmpty($this->get($args));
    }

    /**
     * @return mixed
     */
    protected function loadConfig()
    {
        if (null === $this->_config) {
            $this->_config = [];
            if (is_array($this->configFile)) {
                foreach ($this->configFile as $sourceFile) {
                    $sourceFile = Yii::getAlias($sourceFile);
                    if (file_exists($sourceFile)) {
                        $config = require($sourceFile);
                        if (is_array($config)) {
                            $this->_config = ArrayHelper::merge($this->_config, $config);
                        }
                    }
                }
            } elseif (is_string($this->configFile)) {
                $sourceFile = Yii::getAlias($this->configFile);
                $config = require($sourceFile);
                if (is_array($config)) {
                    $this->_config = $config;
                }
            }
        }
    }
}