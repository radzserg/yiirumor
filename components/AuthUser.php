<?php

namespace Rm\components;

class AuthUser
{

    public static function authorize($userId, $username)
    {
        \Yii::app()->session['rm'] = array(
            'auth' => array(
                'user_id' => $userId,
                'username' => $username,
            ),
        );
    }

}