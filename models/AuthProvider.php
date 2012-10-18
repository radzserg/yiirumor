<?php

namespace Rm\models;

/**
 * This is the model class for table "rm_auth_provider".
 *
 * The followings are the available columns in table 'rm_auth_provider':
 * @property integer $id
 * @property string $code
 * @property string $comment
 * @property string $create_time
 * @property integer $active
 * @property integer $trash
 *
 * The followings are the available model relations:
 * @property RmUser[] $rmUsers
 */
class AuthProvider extends \CActiveRecord
{
    const VK = 'vk';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuthProvider the static model class
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
		return 'rm_auth_provider';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, comment, create_time', 'required'),
			array('active, trash', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>10),
			array('comment', 'length', 'max'=>500),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, code, comment, create_time, active, trash', 'safe', 'on'=>'search'),
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
			'rmUsers' => array(self::HAS_MANY, 'RmUser', 'auth_provider_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => 'Code',
			'comment' => 'Comment',
			'create_time' => 'Create Time',
			'active' => 'Active',
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
		$criteria->compare('code',$this->code,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('trash',$this->trash);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Return auth provider code by id
     *
     * @param type $code
     * @return type
     * @throws \CException
     */
    public static function getIdByCode($code)
    {
        $row = AuthProvider::model()->find('code = :code', array(':code' => $code));
        if (!$row) {
            throw new \CException("Undefined auth provider code {$code}");
        }
        return $row->id;
    }
}