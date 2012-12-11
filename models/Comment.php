<?php

namespace Rm\models;

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property string $create_time
 * @property integer $trash
 */
class Comment extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
		return 'rm_comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, comment', 'required'),
			array('user_id, trash', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>500),

             array('create_time','default', 'value'=>new \CDbExpression('NOW()'), 'setOnEmpty'=>false,'on'=>'insert'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, comment, create_time, trash', 'safe', 'on'=>'search'),
		);
	}

    public function scopes()
    {
        return array(
            'published'=>array(
                //'condition'=>'status=1',
            ),
		);
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'user' => array(self::BELONGS_TO, '\Rm\models\User', 'user_id'),
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
			'comment' => 'Comment',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('trash',$this->trash);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}