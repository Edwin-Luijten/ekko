<?php

namespace EdwinLuijten\Ekko\Broadcast\Test;

use Monolog\Handler\StreamHandler;

class Config
{
    public static function get()
    {
        return [
            'default'     => 'logger',
            'broadcasters' => [
                'pusher' => [
                    'driver' => 'pusher',
                    'key'    => '',
                    'secret' => '',
                    'app_id' => '',
                ],
                'redis'  => [
                    'driver' => 'redis',
                    'parameters' => 'tcp://localhost',
                    'options' => '',
                ],
                'logger' => [
                    'driver'   => 'log',
                    'name'     => 'broadcasts',
                    'handlers' => [
                        'stream' => [
                            'class'     => StreamHandler::class,
                            'arguments' => [
                                __DIR__ . '/broadcasts.log'
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }
}