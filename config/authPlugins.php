<?php

return array(
    'vk' => array(
        //'class' => 'Vk',
        'actions' => array(
            'vkgetcode'=>'Rm\authPlugin\Vk\actions\GetCode'
        ),
    ),
    'fb' => array(
        'actions' => array(
            'fbgettoken' => 'Rm\authPlugin\Fb\actions\GetToken',
        ),
    ),
);