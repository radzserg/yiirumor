<?php

namespace Rm\models;

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
class AuthProviderVk extends \CActiveRecord
{

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

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'vk_id' => 'Vk',
            'access_token' => 'Access Token',
            'token_expires' => 'Token Expires',
            'user_data' => 'User Data',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('vk_id', $this->vk_id, true);
        $criteria->compare('access_token', $this->access_token, true);
        $criteria->compare('token_expires', $this->token_expires, true);
        $criteria->compare('user_data', $this->user_data, true);

        return new \CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    public function authorizeUser($vkAuthData)
    {
        $vkUser = AuthProviderVk::model()->find('vk_id = :vkId', array(':vkId' => $vkAuthData['user_id']));
        if (!$vkUser) {
            $transaction = AuthProviderVk::model()->getDbConnection()->beginTransaction();
            try {
                // need to get base user info
                $details = \Rm\authPlugin\Vk::getUserDetails($vkAuthData['user_id']);

                $user = new User();
                $user->setAttributes(array(
                    'auth_provider_id' => AuthProvider::getIdByCode(AuthProvider::VK),
                    'username' => "{$details['first_name']} {$details['last_name']}",
                ));
                $user->save();

                $vkUser = new AuthProviderVk();
                $vkUser->setAttributes(array(
                    'user_id' => $user->id,
                    'vk_id' => $vkAuthData['user_id'],
                    'token_expires' =>  time() + $vkAuthData['expires_in'],
                    'access_token' => $vkAuthData['access_token']
                ));
                $vkUser->save();

                $transaction->commit();

                \Rm\components\AuthUser::authorize($user->id, $user->username);
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        } else {
            $vkUser->token_expires = time() + $vkAuthData['expires_in'];
            $vkUser->save();

            \Rm\components\AuthUser::authorize($vkUser->user->id, $vkUser->user->username);
        }
    }

}