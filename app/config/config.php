<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => 'root',
        'dbname'      => 'smhawk_live',
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
        ),
    'mail' =>array(
        'fromName'       =>'SMHawk',
        'fromEmail'      =>'adesh@hashbyte.com',
        'smtp'      =>array(
            'server'    =>'smtp.gmail.com',
            'port'    =>'465',
            'security'    =>'ssl',
            'username'    =>'adesh@hashbyte.com',
            'password'    =>'p@ssw0rD',
            ),
        )
    ));
