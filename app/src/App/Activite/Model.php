<?php

namespace App\Activite;

class Model
{

    /**
     * @var mixed
     */
    protected $_oDb;

    /**
     * @var string
     */
    protected $_table = 'activite_horaire';

    /**
     * @param $oDb
     */
    public function __construct($oDb)
    {
        $this->_oDb = $oDb;
    }

    /**
     * @param int $id
     * @return
     */
    public function get(int $id)
    {
        $sql = "SELECT * FROM `{$this->_table}`";

        if ($id > 0) {
            $sql .= ' WHERE id=' . $id;
        }

        return $this->_oDb->fetch($sql);
    }

    /**
     * @param $etat
     * @param $periodeDebut
     * @param null $periodeFin
     */
    public function getCa($etat, $periodeDebut = null, $periodeFin = null)
    {
        $query = $this->_oDb->createQueryBuilder();
        $query->select('*');
        $query->from($this->_table);
        $query->where("poid_ca {$etat} 0");

        if ($periodeDebut && $periodeFin) {
            $query->andWhere('jour_date BETWEEN :periodeDebut AND :periodeFin');
            $query->setParameter(':periodeDebut', $periodeDebut);
            $query->setParameter(':periodeFin', $periodeFin);

        }

        $query->orderBy('jour_date', 'ASC');

        return $query->execute()->fetchAll();
    }


    public function getVentes($periodeDebut = null, $periodeFin = null, $groupBy = null )
    {

        $query = $this->_oDb->createQueryBuilder();
        $query->from($this->_table);

        if($groupBy === null || $groupBy === 'mois') {
            $query->select('DATE_FORMAT(jour_date,"%Y-%m") AS mois, `marche_label`, sum(quantites_vendues) AS quantites_vendues');
            $query->groupBy('`mois`, `marche_label`');
            $query->orderBy('`mois`');
        }
        elseif($groupBy === 'semaine') {
            $query->select('DATE_FORMAT(jour_date, "%Y-%v") AS semaine, `marche_label`, sum(quantites_vendues) AS quantites_vendues');
            $query->groupBy('`semaine`, `marche_label`');
            $query->orderBy('`semaine`');
        }
        elseif($groupBy === 'jour') {
            $query->select('`jour_date` AS jour, `marche_label`, sum(quantites_vendues) AS quantites_vendues');
            $query->groupBy('`jour`, `marche_label`');
            $query->orderBy('`jour`');
        }

        $query->where('jour_date >= :periodeDebut AND jour_date <= :periodeFin');
        $query->setParameter(':periodeDebut', $periodeDebut);
        $query->setParameter(':periodeFin', $periodeFin);

        return $query->execute()->fetchAll();
    }

}
