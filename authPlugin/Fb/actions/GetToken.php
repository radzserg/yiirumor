<?php

namespace Rm\authPlugin\Fb\actions;

class GetToken extends \CAction
{

    public function run()
    {
        $sdk = \Rm\authPlugin\Fb\Plugin::getSdk();
        $accessToken = $sdk->getAccessToken();
        //$accessToken = $fb->getAccessToken();
        /**
        $this->getController()->render('trumor.authPlugin.Vk.views.getCode', array(

        ));
        */
    }
}