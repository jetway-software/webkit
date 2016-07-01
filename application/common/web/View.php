<?php

namespace common\web;

use common\helpers\FileHelper;
use yii\base\InvalidParamException;

class View extends \yii\web\View
{
    /**
     * @var \common\web\Theme
     */
    public $theme = 'common\web\Theme';

    private $_loadBootstrap;

    public function init()
    {
        parent::init();

        if ($this->_loadBootstrap === null && file_exists($this->theme->getBasePath() . DIRECTORY_SEPARATOR . 'bootstrap.php')) {
            $this->_loadBootstrap = true;
            call_user_func(function () {
                include_once($this->theme->getBasePath() . DIRECTORY_SEPARATOR . 'bootstrap.php');
            });
        }
    }

    /**
     * @param string $viewFile
     * @param array $params
     * @param null $context
     * @return string the rendering result
     * @throws InvalidParamException if the view file does not exist
     */
    public function renderFile($viewFile, $params = [], $context = null)
    {
        $viewFile = FileHelper::normalizePath($viewFile);

        return parent::renderFile($viewFile, $params, $context);
    }
}