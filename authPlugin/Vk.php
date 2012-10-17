<?php

namespace Rm\authPlugin;

class Vk extends AbstractClass
{

    const BASE_URL = "https://oauth.vk.com/";

    /**
     * Return url to authorize user
     * @return type
     */
    static public function getAuthUrl()
    {
        $data = array(
            'client_id' => \Yii::app()->params['vk']['app_id'],
            'scope' => '',
            'redirect_uri' => \Yii::app()->createAbsoluteUrl('rumor/auth/vkgetcode'),
            'response_type' => 'code'
        );

        return self::BASE_URL . 'authorize?' . http_build_query($data);
    }

    static public function getToken($code)
    {
        $data = array(
            'client_id' => \Yii::app()->params['vk']['app_id'],
            'client_secret' => \Yii::app()->params['vk']['app_shared_secret'],
            'code' => $code,
            'redirect_uri' => \Yii::app()->createAbsoluteUrl('rumor/auth/vkgetcode'),
        );

        $url = self::BASE_URL . 'access_token?' . http_build_query($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);

        /**
         * {"access_token":"d003e09e8092d312d012c4ef17d0224c5bdd012d012c4ef806c02918b9e3ad12f0f0b4a","expires_in":86399,"user_id":1123441}
         * print_r($result);
         * then see 
         */

    }


    /**
     * Authorize user
     * @param type $data

    public function authorize($data)
    {
        $member = false;
        $validKeys = array('expire', 'mid', 'secret', 'sid', 'sig');
        foreach ($validKeys as $key) {
            if (!isset($data[$key])) {
                return false;
            }
        }
        ksort($data);

        $sign = '';
        foreach ($data as $key => $value) {
            if ($key != 'sig') {
                $sign .= ($key . '=' . $value);
            }
        }
        $sign .= \Yii::app()->params['vk']['app_shared_secret'];
        $sign = md5($sign);
        if ($data['sig'] == $sign && $data['expire'] > time()) {
            // @todo return user id
            return array(
                'id' => intval($data['mid']),
                'secret' => $data['secret'],
                'sid' => $data['sid']
            );
        }

        return false;
    }
     */

    static public function initVkApi()
    {
        $clientScript = \Yii::app()->getComponent('clientScript');
        /* @var $clientScript \CClientScript */

        $clientScript->registerScriptFile('http://vkontakte.ru/js/api/openapi.js');

        $vkAppId = \Yii::app()->params['vk']['app_id'];
        $js = <<<JS
VK.init({
    apiId: "{$vkAppId}"
});
JS;
        $clientScript->registerScript('initVk', $js, \CClientScript::POS_BEGIN);
    }

}
