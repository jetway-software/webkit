<?php

namespace backend\modules\kernel\controllers;

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

        $this->cache = Yii::$app->getCache();

        $this->requirementsChecker = new RequirementChecker();
    }

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
                            return !Yii::$app->isInstalled();
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
     * @inheritdoc
     */
    public function actionIndex()
    {
        $step = Yii::$app->request->get('step', false);

        if ($step === 'next') {
            return $this->next();
        } elseif ($step === 'prev') {
            return $this->prev();
        } else {
            switch ($this->step()) {
                default:
                    return $this->requirements();
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function step()
    {
        return $this->cache->get('step');
    }

    /**
     * @return yii\web\Response
     */
    protected function next()
    {
        return $this->redirect('/');
    }

    /**
     * @return yii\web\Response
     */
    protected function prev()
    {
        return $this->redirect('/');
    }

    /**
     * @inheritdoc
     */
    protected function requirements()
    {
        return $this->requirementsChecker->checkYii()->check($this->getRequirements())->render(true);
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