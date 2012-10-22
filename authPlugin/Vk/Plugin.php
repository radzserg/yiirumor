<?php

namespace Rm\authPlugin\Vk;

/**
 * This is the model class for table "rm_auth_provider_vk".
 *
 * The followings are the available columns in table 'rm_auth_provider_vk':
 * @property integer $id
 * @property integer $user_id
 * @property string $vk_id
 * @property string $access_token
 * @property string $token_expires
 * @property string $user_data
 *
 * The followings are the available model relations:
 * @property RmUser $user
 */
class Plugin extends \CActiveRecord
{

    const BASE_URL = "https://oauth.vk.com/";
    const API_URL = 'https://api.vk.com/';

    static private $_accessToken;
    static private $_accessTokenExpires;


    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Rm\models\AuthProviderVk the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'rm_auth_provider_vk';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, vk_id', 'required'),
            array('user_id', 'numerical', 'integerOnly' => true),
            array('vk_id', 'length', 'max' => 20),
            array('access_token', 'length', 'max' => 255),
            array('token_expires, user_data', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, vk_id, access_token, token_expires, user_data', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'Rm\models\User', 'user_id'),
        );
    }

    public function authorizeUser($vkAuthData)
    {
        $vkUser = self::model()->find('vk_id = :vkId', array(':vkId' => $vkAuthData['user_id']));
        if (!$vkUser) {
            $transaction = self::model()->getDbConnection()->beginTransaction();
            try {
                // need to get base user info
                $details = self::getUserDetails($vkAuthData['user_id']);

                $user = new User();
                $user->setAttributes(array(
                    'auth_provider_id' => self::getIdByCode(AuthProvider::VK),
                    'username' => "{$details['first_name']} {$details['last_name']}",
                ));
                $user->save();

                $vkUser = new self();
                $vkUser->setAttributes(array(
                    'user_id' => $user->id,
                    'vk_id' => $vkAuthData['user_id'],
                    'token_expires' =>  time() + $vkAuthData['expires_in'],
                    'access_token' => $vkAuthData['access_token'],
                    'user_data' => serialize($details),
                ));
                $vkUser->save();

                $transaction->commit();

                $commonDetails = self::filterDetails($details);
                \Rm\components\AuthUser::authorize($user->id, $commonDetails);
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        } else {
            $vkUser->token_expires = time() + $vkAuthData['expires_in'];
            $vkUser->save();

            $commonDetails = self::filterDetails(unserialize($vkUser->user_data));
            \Rm\components\AuthUser::authorize($vkUser->user->id, $commonDetails);
        }
    }


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
