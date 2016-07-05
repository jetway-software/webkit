<?php

namespace backend\modules\kernel;

use yii;
use common\base\Module;
use common\web\Application;
use yii\base\BootstrapInterface;

class Run extends Module implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
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
}