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
        $authProviderCode = $this->_getRequiredParam('authProviderCode');
        $authData = $this->_getRequiredParam('authData');

        $authPlugin = Rm\authPlugin\Factory::factory($authProviderCode);
        $authPlugin->authorize($authData);

        return $this->_returnJson(array());
    }

}
