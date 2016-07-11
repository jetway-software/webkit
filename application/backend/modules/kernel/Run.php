<?php

namespace backend\modules\kernel;

use yii;
use common\base\Module;
use common\web\Application;
use yii\base\BootstrapInterface;

class Run extends Module
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setModules([
            'setup' => [
                'class' => 'backend\modules\kernel\modules\setup\Run'
            ]
        ]);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return 'kernel';
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return __('kernel', 'Kernel System');
    }


    public function getVersion() : string
    {
        return '0.0.1';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @return array
     */
    public function registerUrlRules()
    {
        return [
            '/' => 'kernel/default/index'
        ];
    }
}