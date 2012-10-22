<?php

namespace Rm\components;

class AuthUser
{

    public static function authorize($userId, $details)
    {
        \Yii::app()->session['rm'] = array(
            'user' => array(
                'id' => $userId,
            ) + $details,
        );
    }

    public static function getAuthorizedUser()
    {
        if (!isset(\Yii::app()->session['rm']['user'])) {
            return null;
        }
        $user = new AuthUser();
        return $user;
    }

    public function __get($name)
    {
        $session = \Yii::app()->session['rm'];
        return isset($session['user'][$name]) ? $session['user'][$name] : null;
    }


    public function getDetails()
    {
        $session = \Yii::app()->session['rm'];
        return isset($session['user']) ? $session['user'] : null;
    }


}