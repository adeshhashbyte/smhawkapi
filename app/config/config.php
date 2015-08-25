<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        // 'password'    => 'root',
        'password'    => 'db_h@s#3rs',
        // 'dbname'      => 'smhawk_live',
        'dbname'      => 'smhawk',
        ),
    'application' => array(
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'modelsDir'      => __DIR__ . '/../../app/models/',
        'viewsDir'       => __DIR__ . '/../../app/views/',
        'pluginsDir'     => __DIR__ . '/../../app/plugins/',
        'libraryDir'     => __DIR__ . '/../../app/library/',
        'cacheDir'       => __DIR__ . '/../../app/cache/',
        'classeDir'      => __DIR__ . '/../../app/classes/',
        'baseUri'        => '/phalconapi/',
        'apiUri'        => 'http://m.smhawk.in/#'
        ),
    'mail' => array(
        'fromName'  =>  'SMHawk Support',
        'fromEmail' =>  'info@smhawk.com',
        'server'    =>  'smtp.gmail.com',
        'port'      =>  '465',
        'security'  =>  'ssl',
        'username'  =>  'info@smhawk.com',
        'password'  =>  'p@ssw0rd'
        ),
    ));
