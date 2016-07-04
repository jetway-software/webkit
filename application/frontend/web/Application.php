<?php

namespace frontend\web;

class Application extends \common\web\Application
{
    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['id'])) {
            $config['id'] = 'frontend';
        }

        if (!isset($config['basePath'])) {
            $config['basePath'] = dirname(__DIR__);
        }

        parent::__construct($config);
    }
}