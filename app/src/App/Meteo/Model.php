<?php
namespace App\Meteo;

class Model
{

    /**
     * @var mixed
     */
    protected $_oDb;

    /**
     * @var string
     */
    protected $_table = 'evenement_meteo';

    /**
     * @param $oDb
     */
    public function __construct($oDb)
    {
        $this->_oDb = $oDb;
    }



}