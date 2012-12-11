<?php

namespace Rm\authPlugin\Vk;

class Plugin extends \Rm\authPlugin\AbstractClass
{

    static private $_accessToken;
    static private $_accessTokenExpires;

    const BASE_URL = "https://oauth.vk.com/";
    const API_URL = 'https://api.vk.com/';
    const CODE = 'vk';

    public static function setJsHandler()
    {
        $data = array(
            'client_id' => \Yii::app()->params['vk']['app_id'],
            'scope' => '',
            'redirect_uri' => \Yii::app()->createAbsoluteUrl('/trumor/auth/vkgetcode'),
            'response_type' => 'code'
        );

        $url = self::BASE_URL . 'authorize?' . http_build_query($data);

        $js = <<<JS
$('#auth_block .vk').click(function() {
    var authUrl = "{$url}"
    window.open(authUrl, 'Authorize via VK', 'width=500,height=200,toolbar=0,menubar=0,location=0,resizable=0,scrollbars=0,left=300,top=200')
})
JS;

        $cs = \Yii::app()->getComponent('clientScript');
        /* @var $cs \CClientScript */
        $cs->registerScript('vk_handler', $js, \CClientScript::POS_READY);
    }

    public function authorizeUser($vkAuthData)
    {
        $vkUser = \Rm\models\User::model()
            ->find('foreign_id = :vkId AND auth_provider_id = :authProviderId',
                    array(':vkId' => $vkAuthData['user_id'], ':authProviderId' => static::getAuthProviderId())
            );
        if (!$vkUser) {
            $transaction = \Rm\models\User::model()->getDbConnection()->beginTransaction();
            try {
                // need to get base user info
                $details = self::getUserDetails($vkAuthData['user_id']);

                $user = new \Rm\models\User();
                $user->setAttributes(array(
                    'foreign_id' => $details['uid'],
                    'auth_provider_id' => static::getAuthProviderId(),
                    'username' => "{$details['first_name']} {$details['last_name']}",
                    'photo' => $details['photo'],
                ));
                $user->save();
                $transaction->commit();

                $commonDetails = self::filterDetails($details);
                \Rm\components\AuthUser::authorize($user->id, $commonDetails);
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        } else {
            //$vkUser->token_expires = time() + $vkAuthData['expires_in'];
            //$vkUser->save();
            \Rm\components\AuthUser::authorize($vkUser->id, $vkUser->getAttributes());
        }
    }

    /**
     * Rename to authorize
     */
    static public function authorizeByCode($code)
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

        self::authorizeUser($data);
    }

    /**
     * Filter user details and return common array for all auth plugins
     * @param type $details
     */
    public static function filterDetails($details)
    {
        return array(
            'username' => "{$details['first_name']} {$details['last_name']}",
            'photo' => $details['photo'],
        );
    }

    public function getUserDetails($vkId, $fields = null)
    {
        $uids = array($vkId);
        if (!$fields) {
            $fields = array('uid', 'first_name', 'last_name', 'photo');
            /**
            $fields = array(
                 'uid', 'first_name', 'last_name', 'nickname', 'screen_name',
                'sex', 'bdate', 'city', 'country', 'timezone', 'photo',
                'photo_medium', 'photo_big', 'has_mobile', 'rate', 'contacts',
                'education', 'online', 'counters'
            );*/
        }
        $data = self::_apiCall('users.get', array(
            'uids' => implode(',', $uids),
            'fields' => implode(',', $fields)
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
        $result = \CJSON::decode($result);

        if (isset($result['error'])) {
            throw new CException("An error happened during vk request {$result['error_msg']}");
        }
        return $result;
    }

}
