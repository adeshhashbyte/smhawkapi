<?php
$router = new Phalcon\Mvc\Router();
$router->add('/confirm/{code}/{email}', array(
        'controller' => 'auth',
        'action' => 'confirmEmail'
));
$router->add('/bonus', array(
        'controller' => 'smspackage',
        'action' => 'bonusPlans'
));
$router->add('/security', array(
        'controller' => 'user',
        'action' => 'userSecurity'
));
$router->add('/order-history', array(
        'controller' => 'history',
        'action' => 'userTransactionHistory'
));
return $router;