<?php

namespace common\web;

use yii;

class UrlManager extends \yii\web\UrlManager
{
    /**
     * @var bool
     */
    public $enablePrettyUrl = true;
    /**
     * @var bool
     */
    public $enableStrictParsing = true;
    /**
     * @var bool
     */
    public $showScriptName = false;

    /**
     * @param \yii\web\Request $request
     * @return $this|array|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function parseRequest($request)
    {
        $requestUri = $request->getUrl();

        if ($this->showScriptName === false && strpos($requestUri, pathinfo($request->getScriptFile(), PATHINFO_BASENAME)) && ($pos = strpos($requestUri, $request->getScriptUrl())) === 0) {
            $n = strlen($request->getScriptUrl());
            $redirectUrl = substr($request->getUrl(), $n);
            $redirectUrl = rtrim(substr($request->getScriptUrl(), 0, -$n), '/') . '/' . ($redirectUrl === '/' ? '' : ltrim($redirectUrl, '/'));

            Yii::$app->getResponse()->redirect($redirectUrl === false ? '/' : $redirectUrl, 301)->send();
            Yii::$app->end();
        }

        return parent::parseRequest($request);
    }
}