<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
return [
    'webhemi_user' => [
        1 => [
            'id_user' => 1,
            'username' => 'c.kent',
            'email' => 'clark.kent@daily-planet.com',
            'password' => null,
            'hash' => null,
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'time_register' =>  '2016-03-24 16:25:12',
        ],
        2 => [
            'id_user' => 2,
            'username' => 'p.parker',
            'email' => 'peter.parker@daily-bugle.com',
            'password' => null,
            'hash' => null,
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'time_register' =>  '2016-03-24 16:25:12',
        ],
    ],
    'webhemi_user_meta' => [
        1 => [
            'id_user_meta' => 1,
            'fk_user' => 1,
            'meta_key' => 'alter ego',
            'meta_data' => 'Superman',
        ],
        2 => [
            'id_user_meta' => 2,
            'fk_user' => 1,
            'meta_key' => 'super power',
            'meta_data' => 'God-like',
        ],
        3 => [
            'id_user_meta' => 3,
            'fk_user' => 2,
            'meta_key' => 'alter ego',
            'meta_data' => 'Spider-man',
        ],
        4 => [
            'id_user_meta' => 4,
            'fk_user' => 2,
            'meta_key' => 'super power',
            'meta_data' => 'Power of a human-sized spider, wall climbing, spider sense',
        ],
    ]
];
