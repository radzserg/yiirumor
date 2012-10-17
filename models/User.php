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
			array('auth_provider_id, username, create_time', 'required'),
			array('auth_provider_id, trash', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>255),
			array('info', 'safe'),
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
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'rmComments' => array(self::HAS_MANY, 'RmComment', 'user_id'),
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
}