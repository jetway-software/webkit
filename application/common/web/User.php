<?php

namespace common\web;

use yii;

class User extends \yii\web\User
{
    /**
     * @throws yii\base\InvalidConfigException
     */
    public function init()
    {
        if ($this->identityClass === null) {
            if (Yii::$app->hasModule('users') && Yii::$app->getModule('users')->identityClass !== null) {
                $this->identityClass = Yii::$app->getModule('users')->identityClass;
            } else {
                $this->identityClass = 'common\model\Identity';
            }
        }

        parent::init();
    }
}