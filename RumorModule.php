<?php

class RumorModule extends CWebModule
{

    public function init()
    {
        Yii::setPathOfAlias('Rm', realpath(dirname(__FILE__)));
        $this->_registerClientScript();
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        }
        else
            return false;
    }

    private function _registerClientScript()
    {
        /* @var $cs CClientScript */
        $cs = Yii::app()->clientScript;

        $cs->addPackage('bootstrap', array(
            'basePath' => 'rumor.assets.bootstrap',
            'js' => array('/js/bootstrap.min.js'),
            'css' => array('/css/bootstrap.min.css'),
            'depends' => array('jquery'),
        ));

        $cs->addPackage('underscore', array(
            'basePath' => 'rumor.assets.js.lib',
            'js' => array('underscore.js'),
            'depends' => array('jquery')
        ));
        $cs->addPackage('backbone', array(
            'basePath' => 'rumor.assets.js.lib',
            'js' => array('backbone.js'),
            'depends' => array('underscore')
        ));


        $cs->addPackage('rumor', array(
            'basePath' => 'rumor.assets',
            'css' => array('/css/rumor.css'),
            'js' => array(),
            'depends' => array('underscore')
        ));

        $cs->registerPackage('bootstrap');
        $cs->registerPackage('backbone');
        $cs->registerPackage('rumor');
    }

}
