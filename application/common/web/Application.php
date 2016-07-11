<?php

namespace common\web;

use yii;
use common\base\Kernel;
use common\base\Config;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\web\NotFoundHttpException;

abstract class Application extends \yii\web\Application
{
    /**
     * @var
     */
    private $_kernel;

    /**
     * @param array $config
     * @throws InvalidConfigException
     */
    public function preInit(&$config)
    {
        $this->initKernel($config);
    }

    /**
     * @param array $config
     */
    protected function initKernel(array &$config)
    {
        $this->_kernel = new Kernel($this, $config);
    }

    /**
     * Returns the kernel component.
     * @return Kernel the kernel component.
     */
    public function kernel()
    {
        return $this->_kernel;
    }

    /**
     * Handles the specified request.
     * @param Request $request the request to be handled
     * @return Response the resulting response
     * @throws NotFoundHttpException if the requested route is invalid
     */
    public function handleRequest($request)
    {
        if (empty($this->catchAll)) {
            try {
                list ($route, $params) = $request->resolve();
            } catch (\Exception $e) {
                if (!Yii::$app->kernel()->installed() && $this->hasModule('setup')) {
                    return $this->getResponse()->redirect(['/kernel/setup/default/index']);
                } else {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }
            }
        } else {
            $route = $this->catchAll[0];
            $params = $this->catchAll;
            unset($params[0]);
        }

        if (!Yii::$app->kernel()->installed() && $this->hasModule('setup') && strpos($route, 'kernel/setup') === false) {
            return $this->getResponse()->redirect(['/kernel/setup/default/index']);
        }

        try {
            Yii::trace("Route requested: '$route'", __METHOD__);
            $this->requestedRoute = $route;
            $result = $this->runAction($route, $params);
            if ($result instanceof Response) {
                return $result;
            } else {
                $response = $this->getResponse();
                if ($result !== null) {
                    $response->data = $result;
                }

                return $response;
            }
        } catch (InvalidRouteException $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'), $e->getCode(), $e);
        }
    }

    /**
     * Returns the user component.
     * @return Config the user component.
     */
    public function getConfig()
    {
        return $this->get('config');
    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'view' => ['class' => 'common\web\View'],
            'request' => ['class' => 'common\web\Request'],
            'config' => ['class' => 'common\base\Config'],
            'user' => ['class' => 'common\web\User'],
            'urlManager' => ['class' => 'common\web\UrlManager'],
            'cache' => ['class' => 'yii\caching\FileCache'],
        ]);
    }
}