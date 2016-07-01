<?php

namespace backend\modules\kernel\requirements;

require_once(dirname(APP_PATH) . '/vendor/yiisoft/yii2/requirements/YiiRequirementChecker.php');

class RequirementChecker extends \YiiRequirementChecker
{
    /**
     * @inheritdoc
     */
    function render($_return_ = false)
    {
        if (!isset($this->result)) {
            $this->usageError('Nothing to render!');
        }

        $baseViewFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'views';

        if (!empty($_SERVER['argv'])) {
            $viewFileName = $baseViewFilePath . DIRECTORY_SEPARATOR . 'console' . DIRECTORY_SEPARATOR . 'index.php';
        } else {
            $viewFileName = $baseViewFilePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'index.php';
        }

        if ($_return_ === true) {
            return $this->renderViewFile($viewFileName, $this->result, $_return_);
        } else {
            $this->renderViewFile($viewFileName, $this->result);
        }
    }
}
