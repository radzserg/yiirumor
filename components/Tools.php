<?php

namespace Rm\components;

class Tools
{

    static public function underscoreToCamel($name)
    {
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        return str_replace(' ', '', $name);
    }

}