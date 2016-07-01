<?php

namespace frontend\modules\pages\controllers;

use common\web\Controller;

class ViewController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}