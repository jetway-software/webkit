<?php

namespace common\base;

abstract class Module extends \yii\base\Module
{
    /**
     * Set true if module is active, default false
     * @var bool
     */
    public $active = false;

    /**
     * @return mixed
     */
    abstract public function getId();

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return string
     */
    abstract public function getVersion();

    /**
     * @return string
     */
    abstract public function getDescription();

    /**
     * @return array
     */
    public static function getDependency() : array
    {
        return [];
    }
}