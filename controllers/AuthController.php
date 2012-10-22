<?php

class AuthController extends Rm\components\Controller
{

    public function actions()
    {
        // load from some config
        return array(
            'vkgetcode'=>'Rm\authPlugin\Vk\actions\GetCode',
        );
    }

    public function actionApplyAuth()
    {
        $user = Rm\components\AuthUser::getAuthorizedUser();
        return $this->_returnJson($user->getDetails());
    }

}
