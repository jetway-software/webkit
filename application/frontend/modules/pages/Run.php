<?php

namespace frontend\modules\pages;

use common\base\Module;

class Run extends Module
{
    /**
     * @return mixed
     */
    public function getId()
    {
        return 'pages';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pages';
    }

    /**
     * @return string
     */
    public function getVersion()
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