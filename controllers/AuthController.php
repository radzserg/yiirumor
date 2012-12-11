<?php

class AuthController extends Rm\components\Controller
{

    public function actions()
    {
        $authPlugins = require_once Yii::getPathOfAlias('trumor.config') . '/authPlugins.php';
        $actions = array();
        foreach ($authPlugins as $plugin) {
            if (!isset($plugin['actions'])) {
                continue;
            }
            $actions += $plugin['actions'];
        }

        return $actions;
    }

    public function actionApplyAuth()
    {
        $user = Rm\components\AuthUser::getAuthorizedUser();
        return $this->_returnJson($user->getDetails());
    }

}
