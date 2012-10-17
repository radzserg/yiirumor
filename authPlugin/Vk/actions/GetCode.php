<?php

namespace Rm\authPlugin\Vk\actions;

/**
 * First step we get code from vk
 * @see http://vk.com/developers.php?oid=-1&p=%D0%90%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F_%D1%81%D0%B0%D0%B9%D1%82%D0%BE%D0%B2
 */
class GetCode extends \CAction
{

    public function run()
    {
        $request = \Yii::app()->getRequest();
        if ($request->getParam('error')) {
            throw new \CException("Can't authorize user through vk "
                . $request->getParam('error') . ' '
                . $request->getParam('error_description')
            );
        }

        $code = \Yii::app()->getRequest()->getParam('code');

        $vkAuthPlugin = new \Rm\authPlugin\Vk();
        $token = $vkAuthPlugin->getToken($code);
    }
}