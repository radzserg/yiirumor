<?php

namespace Rm\authPlugin;

class Vk extends AbstractClass
{

    const BASE_URL = "https://oauth.vk.com/";
    const API_URL = 'https://api.vk.com/';

    static private $_accessToken;
    static private $_accessTokenExpires;


    /**
     * Return url to authorize user
     * @return type
     */
    static public function getAuthUrl()
    {
        $data = array(
            'client_id' => \Yii::app()->params['vk']['app_id'],
            'scope' => '',
            'redirect_uri' => \Yii::app()->createAbsoluteUrl('/trumor/auth/vkgetcode'),
            'response_type' => 'code'
        );

        return self::BASE_URL . 'authorize?' . http_build_query($data);
    }

    /**
     * Rename to authorize
     */
    static public function authorize($code)
    {
        $url = self::BASE_URL . "access_token?" . http_build_query(array(
            'client_id' => \Yii::app()->params['vk']['app_id'],
            'client_secret' => \Yii::app()->params['vk']['app_shared_secret'],
            'code' => $code,
            'redirect_uri' => \Yii::app()->createAbsoluteUrl('/trumor/auth/vkgetcode'),
        ));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = \CJSON::decode($result);

        if (!empty($data['error'])) {
            throw new \CException("Can't get vk access token " . $data['error']
                . ' ' . $data['error_description']
            );
        }

        self::$_accessToken = $data['access_token'];
        self::$_accessTokenExpires = time() + $data['expires_in'];

        \Rm\models\AuthProviderVk::authorizeUser($data);

//        $vkUser = \Rm\models\AuthProviderVk::model()->find('vk_id = :vkId', array(':vkId' => $data['user_id']));
//        if (!$vkUser) {
//
//        }

        /**
         * {"access_token":"d003e09e8092d312d012c4ef17d0224c5bdd012d012c4ef806c02918b9e3ad12f0f0b4a","expires_in":86399,"user_id":1123441}
         * print_r($result);
         * then see
         */

    }

    public function getUserDetails($vkId, $fields = null)
    {
        $uids = array($vkId);
        if (!$fields) {
            $fields = array(
                 'uid', 'first_name', 'last_name', 'nickname', 'screen_name',
                'sex', 'bdate', 'city', 'country', 'timezone', 'photo',
                'photo_medium', 'photo_big', 'has_mobile', 'rate', 'contacts',
                'education', 'online', 'counters'
            );
        }
        $data = self::_apiCall('users.get', array(
            'uids' => implode(',', $uids),
            'fields' => $fields
        ));

        return isset($data['response'][0]) ? ($data['response'][0]) : array();
    }


    static private function _apiCall($methodName, $data)
    {
        if (!self::$_accessToken || self::$_accessTokenExpires < time()) {
            throw new \CException("VK access token is missing or expired");
        }
        $data['access_token'] = self::$_accessToken;

        $url = self::API_URL . "method/{$methodName}?" . http_build_query($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        return \CJSON::decode($result);
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
