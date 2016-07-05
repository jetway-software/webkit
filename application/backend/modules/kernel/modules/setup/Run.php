<?php

namespace backend\modules\kernel\modules\setup;

use yii;
use common\base\Module;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class Run extends Module
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->kernel()->installed();
                        }
                    ],
                ],
                'denyCallback' => function () {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return 'setup';
    }

    /**
     * @return string
     */
    public function getName()
    {
        // TODO: Implement getName() method.
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
        // TODO: Implement getDescription() method.
    }
}