<?php

namespace frontend\modules\seo\controllers;

use yii;
use common\web\Response;
use common\web\Controller;

class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actionRobots()
    {
        $response = Yii::$app->response;
        $response->headers->set('Content-Type', 'text/plain');
        $response->format = Response::FORMAT_RAW;
        $response->data = '';

        Yii::$app->end();
    }
}