<?php

namespace App\Marche;

class Model
{

    protected $_oDb;

    protected $_table = 'marche';

    public function __construct($oDb)
    {
        $this->_oDb = $oDb;
    }

    public function getMarches()
    {
        $sql    = "SELECT * FROM `{$this->_table}`";

        return  $this->_oDb->fetchAll($sql);
    }

}