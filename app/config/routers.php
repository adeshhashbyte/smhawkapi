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
$router->add('/payment-proccess', array(
        'controller' => 'smspackage',
        'action' => 'postPaymentProccess'
));
$router->add('/payment-success', array(
        'controller' => 'smspackage',
        'action' => 'getPaymentSuccess'
));
$router->add('/payment-success-api', array(
        'controller' => 'smspackage',
        'action' => 'getPaymentSuccessApi'
));
$router->add('/payment-failer', array(
        'controller' => 'smspackage',
        'action' => 'getPaymentFailer'
));
$router->add('/shedule-sms', array(
        'controller' => 'sms',
        'action' => 'sheduleSMS'
));
return $router;