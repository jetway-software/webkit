<?php
/**
 * JW console bootstrap file.
 *
 * @link http://www.jetway.su/
 * @copyright Copyright (c) 2016 JetWay Software LLC
 */

/* PHP version validation */
if (!defined('PHP_VERSION_ID') || !(PHP_VERSION_ID >= 70001)) {
    if (PHP_SAPI == 'cli') {
        echo 'This script supports PHP 7.0.1 or newer.';
    } else {
        echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <p>This script supports PHP 7.0.1 or newer.</p>
</div>
HTML;
    }
    exit(1);
}

defined('APP_PATH') or define('APP_PATH', __DIR__);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/functions.php');

$vendorRoot = dirname(__DIR__) . '/vendor';

/* 'composer install' validation */
if (file_exists($vendorRoot . '/autoload.php')) {
    require($vendorRoot . '/autoload.php');
} else {
    throw new \Exception(
        'Vendor autoload is not found. Please run \'composer install\' under application root directory.'
    );
}

require($vendorRoot . '/yiisoft/yii2/BaseYii.php');

class Yii extends \yii\BaseYii
{
    /**
     * @var \console\Application|\backend\web\Application|\frontend\web\Application the application instance
     */
    public static $app;
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require($vendorRoot . '/yiisoft/yii2/classes.php');
Yii::$container = new yii\di\Container();

Yii::setAlias('common', __DIR__ . DIRECTORY_SEPARATOR . 'common');
Yii::setAlias('backend', __DIR__ . DIRECTORY_SEPARATOR . 'backend');
Yii::setAlias('console', __DIR__ . DIRECTORY_SEPARATOR . 'console');
Yii::setAlias('frontend', __DIR__ . DIRECTORY_SEPARATOR . 'frontend');