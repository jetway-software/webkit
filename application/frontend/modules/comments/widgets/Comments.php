<?php

namespace frontend\modules\comments\widgets;

use yii;
use common\helpers\Url;
use common\base\Widget;
use yii\base\InvalidConfigException;

class Comments extends Widget
{
    /**
     * @var
     */
    public $to;
    /**
     * @var
     */
    public $forPage;
    /**
     * @var
     */
    public $perPage = 15;
    /**
     * @var null|integer maximum comments level, level starts from 1, null - unlimited level;
     */
    public $maxLevel = 5;
    /**
     * @var bool
     */
    public $useCache = true;
    /**
     * @var
     */
    public $viewFile = 'comments';

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     * @throws InvalidConfigException
     */
    public function run()
    {
        if (empty($this->to)) {
            if (Yii::$app->controller->route) {
                $this->to = Yii::$app->controller->route;
            } else {
                throw new InvalidConfigException(__('comments', 'The "to" property must be set.'));
            }
        }

        if (empty($this->forPage) && $this->forPage !== false) {
            $this->forPage = Url::current();
        }

        return $this->render($this->viewFile, [
            'comments' => $this->getComments(),
            'totalCount' => $this->getTotalCount()
        ]);
    }

    /**
     * @return array
     */
    protected function getComments() : array
    {
        return [];
    }

    /**
     * @return int
     */
    protected function getTotalCount() : int
    {
        return 0;
    }
}