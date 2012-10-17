<?php

namespace Rm\components;

class Controller extends \CController
{

    public $layout = 'trumor';

    protected function _returnJson($data)
    {
        $this->layout = false;
        header('Content-type: application/json');
        echo \CJavaScript::jsonEncode($data);
        \Yii::app()->end();
    }

    protected function _getRequiredParam($name)
    {
        $value = \Yii::app()->getRequest()->getParam($name);
        if (!$value) {
            throw new \CHttpException(404, "Wrong request");
        }
        return $value;
    }

}