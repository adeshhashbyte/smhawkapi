<?php

class Networks extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $platform;

    /**
     *
     * @var string
     */
    public $api_key;

    /**
     *
     * @var string
     */
    public $secret_key;

    /**
     *
     * @var string
     */
    public $scope;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'platform' => 'platform', 
            'api_key' => 'api_key', 
            'secret_key' => 'secret_key', 
            'scope' => 'scope'
        );
    }

}
