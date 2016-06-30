<?php

namespace frontend\modules\comments;

use common\base\Module;

class Run extends Module
{
    /**
     * @return mixed
     */
    public function getId()
    {
        return 'comments';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return __('comments', 'Comments');
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
        return __('comments', 'Comments');
    }
}