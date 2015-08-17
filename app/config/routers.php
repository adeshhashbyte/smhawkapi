<?php
$router = new Phalcon\Mvc\Router();
$router->add('/confirm/{code}/{email}', array(
        'controller' => 'auth',
        'action' => 'confirmEmail'
));
return $router;