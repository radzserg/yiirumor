<?php

namespace Rm\authPlugin;

class Factory
{

    /**
     *
     * @param type $authProviderCode
     * @return \Rm\authPlugin\AbstractClass
     * @throws \CException
     */
    static public function factory($authProviderCode)
    {
        $className = "Rm\\authPlugin\\" . \Rm\components\Tools::underscoreToCamel($authProviderCode);
        if (!class_exists($className)) {
            throw new \CException("Can't find auth plugin {$authProviderCode}");
        }

        return new $className;
    }

}
