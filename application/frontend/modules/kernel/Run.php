<?php

namespace frontend\modules\kernel;

use yii;
use common\base\Module;
use yii\base\Application;
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
    final function getId() : string
    {
        return 'kernel';
    }

    /**
     * @return string
     */
    final function getVersion() : string
    {
        return '0.0.1';
    }

    /**
     * @return string
     */
    final function getName() : string
    {
        return __('kernel', 'Kernel System');
    }

    /**
     * @return string
     */
    final function getDescription() : string
    {
        return __('kernel', 'Kernel System');
    }
}