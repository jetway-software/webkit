<?php

namespace common\web;

use yii;
use yii\base\Component;
use common\helpers\FileHelper;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;

class Theme extends Component
{
    /**
     * Include views dir in path
     * @var bool
     */
    public $viewsDir = false;

    /**
     * @var
     */
    private $_baseUrl;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->getBasePath() === null) {
            $basePath = Yii::$app->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
        }

        $template = Yii::$app->getConfig()->get('template');

        if (empty($template)) {
            $template = 'kernel';
        }

        if (isset($basePath)) {
            $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $template;
        } else {
            $basePath = rtrim($this->getBasePath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $template;
        }

        $this->setBasePath($basePath);
    }

    /**
     * @return string the base URL (without ending slash) for this theme. All resources of this theme are considered
     * to be under this base URL.
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * @param string $url the base URL or path alias for this theme. All resources of this theme are considered
     * to be under this base URL.
     */
    public function setBaseUrl($url)
    {
        $this->_baseUrl = rtrim(Yii::getAlias($url), '/');
    }

    private $_basePath;

    /**
     * @return string the root path of this theme. All resources of this theme are located under this directory.
     * @see pathMap
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * @param string $path the root path or path alias of this theme. All resources of this theme are located
     * under this directory.
     * @see pathMap
     */
    public function setBasePath($path)
    {
        $this->_basePath = Yii::getAlias($path);
    }

    /**
     * @param string $path
     * @return string
     * @throws InvalidConfigException
     */
    public function applyTo($path)
    {
        $from = FileHelper::normalizePath(Yii::getAlias(Yii::$app->getBasePath())) . DIRECTORY_SEPARATOR;

        if (strpos($path, $from) === 0) {
            $to = FileHelper::normalizePath(Yii::getAlias($this->getBasePath())) . DIRECTORY_SEPARATOR;

            if ($this->viewsDir === false) {
                $file = FileHelper::normalizePath($to . substr(preg_replace('/\/views\//', DIRECTORY_SEPARATOR, $path, 1), strlen($from)));
            } else {
                $file = $to . substr($path, strlen($from));
            }

            if (is_file($file)) {
                return $file;
            } else {
                if (!is_file($path)) {
                    throw new InvalidParamException("The view file does not exist: $file");
                }
            }
        }

        return $path;
    }

    /**
     * Converts a relative URL into an absolute URL using [[baseUrl]].
     * @param string $url the relative URL to be converted.
     * @return string the absolute URL
     * @throws InvalidConfigException if [[baseUrl]] is not set
     */
    public function getUrl($url)
    {
        if (($baseUrl = $this->getBaseUrl()) !== null) {
            return $baseUrl . '/' . ltrim($url, '/');
        } else {
            throw new InvalidConfigException('The "baseUrl" property must be set.');
        }
    }

    /**
     * Converts a relative file path into an absolute one using [[basePath]].
     * @param string $path the relative file path to be converted.
     * @return string the absolute file path
     * @throws InvalidConfigException if [[baseUrl]] is not set
     */
    public function getPath($path)
    {
        if (($basePath = $this->getBasePath()) !== null) {
            return $basePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        } else {
            throw new InvalidConfigException('The "basePath" property must be set.');
        }
    }
}