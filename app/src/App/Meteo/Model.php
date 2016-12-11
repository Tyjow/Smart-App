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


    public function getByPeriode($periodeDebut, $periodeFin, $groupBy)
    {
        $query = $this->_oDb->createQueryBuilder();
        $query->from($this->_table);

        if($groupBy === 'mois') {
            $query->select('MONTH(`date`) AS mois, AVG(temperature_min) AS temperature_min, AVG(temperature_max) as temperature_max, AVG(`pluie`) AS `pluie`');
            $query->groupBy('mois');
            $query->orderBy('mois');
        }
        elseif($groupBy === 'semaine') {
            $query->select('WEEK(`date`) AS semaine, AVG(temperature_min) AS temperature_min, AVG(temperature_max) as temperature_max, AVG(`pluie`) AS `pluie`');
            $query->groupBy('semaine');
            $query->orderBy('semaine');
        }
        if($groupBy === 'jour') {
            $query->select('`date`, `temperature_min`, `temperature_max`, `pluie`');
            $query->orderBy('`date`');
        }

        $query->where('date BETWEEN :periodeDebut AND :periodeFin');
        $query->setParameter(':periodeDebut', $periodeDebut);
        $query->setParameter(':periodeFin', $periodeFin);




        return $query->execute()->fetchAll();
    }


}