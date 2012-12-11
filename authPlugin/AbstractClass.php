<?php

namespace Rm\authPlugin;

abstract class AbstractClass
{

    static protected $_authProviderId;

    public static function getCode()
    {
        return static::CODE;
    }

    abstract public static function setJsHandler();


    public static function getAuthProviderId()
    {
        // don't do static method
        $authProvider = \Rm\models\AuthProvider::model()
                ->find('code = :code', array(':code' => static::CODE));
        return $authProvider->id;
    }
}

