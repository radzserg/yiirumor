<?php

namespace Rm\models;

/**
 * This is the model class for table "rm_user".
 *
 * The followings are the available columns in table 'rm_user':
 * @property integer $id
 * @property integer $auth_provider_id
 * @property string $username
 * @property string $info
 * @property string $create_time
 * @property integer $trash
 *
 * The followings are the available model relations:
 * @property RmComment[] $rmComments
 * @property RmAuthProvider $authProvider
 */
class User extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rm_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('auth_provider_id, username, foreign_id', 'required'),
			array('auth_provider_id, trash', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>255),
			array('photo', 'safe'),

             array('create_time', 'default', 'value' => new \CDbExpression('NOW()'), 'setOnEmpty'=>false,'on'=>'insert'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, auth_provider_id, username, info, create_time, trash', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'comments' => array(self::HAS_MANY, 'RmComment', 'user_id'),
			'authProvider' => array(self::BELONGS_TO, 'RmAuthProvider', 'auth_provider_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'auth_provider_id' => 'Auth Provider',
			'username' => 'Username',
			'info' => 'Info',
			'create_time' => 'Create Time',
			'trash' => 'Trash',
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

		$criteria=new \CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('auth_provider_id',$this->auth_provider_id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('info',$this->info,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('trash',$this->trash);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


    public function authorizeUser($vkAuthData, \Rm\authPlugin\AbstractClass $authProvider)
    {
        $user = \Rm\models\User::model()
            ->find('foreign_id = :vkId AND auth_provider_id = :authProviderId',
                    array(':vkId' => $vkAuthData['user_id'], ':authProviderId' => static::getAuthProviderId())
            );
        if (!$user) {
            // need to get base user info
            $details = $authProvider->getUserDetails($vkAuthData['user_id']);

            $user = new \Rm\models\User();
            $user->setAttributes(array(
                'foreign_id' => $details['user_id'],
                'auth_provider_id' => $authProvider->getAuthProviderId(),
                'username' => "{$details['username']}",
                'photo' => $details['photo'],
            ));
            $user->save();

            $commonDetails = self::filterDetails($details);
            \Rm\components\AuthUser::authorize($user->id, $commonDetails);
        } else {
            $user->token_expires = time() + $vkAuthData['expires_in'];
            $user->save();

            $commonDetails = self::filterDetails(unserialize($user->user_data));
            \Rm\components\AuthUser::authorize($user->user->id, $commonDetails);
        }
    }

}