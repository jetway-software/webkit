<?php

namespace backend\modules\kernel\modules\setup\controllers;

use yii;
use common\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\modules\kernel\requirements\RequirementChecker;

class SetupController extends Controller
{
    /**
     * @var \yii\caching\Cache
     */
    private $cache;
    /**
     * @var \backend\modules\kernel\requirements\RequirementChecker
     */
    protected $requirementsChecker;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->requirementsChecker = new RequirementChecker();
    }


    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        $step = Yii::$app->request->get('step', false);
    }
    
    /**
     * @inheritdoc
     */
    protected function requirements()
    {
        $require = $this->requirementsChecker->checkYii()->check($this->getRequirements());

        if (isset($require->result['summary']['errors']) && (int)$require->result['summary']['errors'] === 0) {
            $this->cache->set('setup', ['step' => 'requirements', 'allow' => true]);
        }

        return $require->render(true);
    }

    /**
     * @return array
     */
    protected function getRequirements() : array
    {
        $requirementsChecker = $this->requirementsChecker;

        return require(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'requirements' . DIRECTORY_SEPARATOR . 'requirements.php');
    }
}