<?php
namespace App\Activite;

use \DatePeriod;
use \DateInterval;
use Silex\Application;
use \DateTime as DateTime;
use Hisune\EchartsPHP\ECharts;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class Controller implements ControllerProviderInterface
{

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $factory = $app['controllers_factory'];

        // Routes are defined here
        $factory->get('/', 'App\Activite\Controller::home');
        //$factory->get('/annee/getAll', 'App\Activite\Controller::annee');
        $factory->get('/ca/{etat}/{periode}', 'App\Activite\Controller::ca');

        $factory->get(
            '/marches/{periode}/{group_by}/{axe2}'
            , 'App\Activite\Controller::marches'
        )
            ->value('periode', null)
            ->value('group_by', 'mois')
            ->value('axe2', null);

        return $factory;
    }

    public function home()
    {
        return 'Controller Activités';
    }

    /**
     * @param Request $request
     * @param Application $app
     */
    public function ca($etat, $periode, Request $request, Application $app)
    {
        $result = array(
            'error'    => 0,
            'messages' => array(),
        );

        $etatAllow = array(
            'positif'     => '>',
            'negatif'     => '<',
            'null'        => '=',
            'nullnegatif' => '<=',
        );

        if (!isset($etatAllow[$etat])) {
            $result['error']      = 1;
            $result['messages'][] = 'Le paramètre {etat} /activite/ca/{etat}/ peut-être : '
            . implode(' ou ', $etatAllow);
        }

        $periodQuery = array();
        if ($periode === null) {
            $periodQuery['debut'] = date('Ymd');
            $periodQuery['fin']   = date('Ymd', strtotime("-30 days"));
        } elseif (mb_strpos($periode, '_')) {
            $periode = explode('_', $periode);

            $periodQuery['debut'] = $periode[0];
            $periodQuery['fin']   = $periode[1];
        } else {
            $periodQuery['debut'] = $periodQuery['fin'] = $periode;
        }

        // Vérification du format des dates
        if (false === ($oDateDebut = DateTime::createFromFormat('Ymd', $periodQuery['debut']))) {
            $result['error']      = 1;
            $result['messages'][] = "Mauvais format de date pour la période : Ymd requis.";
        }

        if ($periodQuery['debut'] != $periodQuery['fin']
            && false === ($oDateFin = DateTime::createFromFormat('Ymd', $periodQuery['fin']))) {
            $result['error']      = 1;
            $result['messages'][] = "Mauvais format de date pour la période : Ymd_Ymd requis.";
        }

        if ($result['error'] === 1) {
            return $app->json($result);
        }

        $periodQuery['debut'] = $oDateDebut->format('Y-m-d');
        $periodQuery['fin']   = $oDateFin->format('Y-m-d');

        $result['messages'] = sprintf("Période de recherche du %s au %s"
            , $oDateDebut->format('Y-m-d')
            , $oDateFin->format('Y-m-d')
        );

        $result['result'] = $app['modelActivite']->getCa($etatAllow[$etat], $periodQuery['debut'], $periodQuery['fin']);

        return $app->json($result);
    }

    /**
     * @param string
     * @param array $periodQuery['debut' => '', 'find' => '']
     * @return array
     */
    private function _calculDataXAxis(string $groupBy, array $periodQuery)
    {
        if ($groupBy === 'jour') {
            $period = new DatePeriod(
                new DateTime($periodQuery['debut']),
                new DateInterval('P1D'),
                new DateTime($periodQuery['fin'])
            );

            foreach ($period as $jour) {
                $dataXAxis[$jour->format('Y-m-d')] = $jour->format("D d\nM Y");
            }
            $dataXAxis[$period->getEndDate()->format('Y-m-d')] = $period->getEndDate()->format("D d\nM Y");

        } elseif ($groupBy === 'mois') {
            $period = new DatePeriod(
                new DateTime($periodQuery['debut']),
                new DateInterval('P1M'),
                new DateTime($periodQuery['fin'])
            );

            foreach ($period as $jour) {
                $dataXAxis[$jour->format('Y-m')] = $jour->format("M Y");
            }
            $dataXAxis[$period->getEndDate()->format('Y-m')] = $period->getEndDate()->format("M Y");
        }
         elseif ($groupBy === 'semaine') {
            $period = new DatePeriod(
                new DateTime($periodQuery['debut']),
                new DateInterval('P7D'),
                new DateTime($periodQuery['fin'])
            );

            foreach ($period as $jour) {
                $dataXAxis[$jour->format('Y-W')] = $jour->format("\sW Y");
            }
            $dataXAxis[$period->getEndDate()->format('Y-W')] = $period->getEndDate()->format("\sW Y");
        }

        return $dataXAxis;
    }

    private function _calculPeriodQuery(string $periode)
    {
        $periodQuery = array();
        if ($periode === null) {

            $periodQuery['debut'] = date('Y-m-d');
            $periodQuery['fin']   = date('Y-m-d', strtotime("-30 days"));

        } elseif (mb_strpos($periode, '_')) {
            $periode = explode('_', $periode);

            $periodQuery['debut'] = DateTime::createFromFormat('Ymd', $periode[0])->format('Y-m-d');
            $periodQuery['fin']   = DateTime::createFromFormat('Ymd', $periode[1])->format('Y-m-d');

        } else {

            $periodQuery['debut'] = $periodQuery['fin'] = DateTime::createFromFormat('Ymd', $periode)->format('Y-m-d');
        }

        return $periodQuery;
    }

    /**
     * @param $periode
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function marches($periode, $group_by, $axe2, Request $request, Application $app)
    {
        $result = array(
            'error'    => 0,
            'messages' => array(),
        );

        // Vérification période demandée
        $periodQuery = $this->_calculPeriodQuery($periode);

        $groupBy = 'semaine';
        if (in_array($group_by, array('jour', 'mois', 'semaine'))) {
            $groupBy = $group_by;
        }

        // Requête
        $ventes = $app['modelActivite']->getVentes(
            $periodQuery['debut'],
            $periodQuery['fin'],
            $groupBy
        );

        $meteo = $app['modelMeteo']->getByPeriode(
            $periodQuery['debut'],
            $periodQuery['fin'],
            $groupBy
        );

        $dataXAxis = $this->_calculDataXAxis($groupBy, $periodQuery);

        // Configuration du rendu
        $chart                 = new ECharts();
        $chart->legend->data   = array_unique(array_column($ventes, 'marche_label'));
        $chart->legend->data[] = 'T°';
        $chart->legend->data[] = 'Précipitations';

        $chart->xAxis[] = array(
            'type' => 'category',
            'data' => array_values($dataXAxis),
        );

        $chart->yAxis[] = array(
            'type' => 'value',
            'name' => 'ventes',
        );

        if($axe2 === 'temperaturemax') {
            $chart->yAxis[] = array(
                'type'      => 'value',
                'name'      => 'temperature',
                'axisLabel' => array(
                    'formatter' => '{value} °C',
                ),
            );
            // Température Max
            $tempData = array_column($meteo, 'temperature_max');
            foreach ($tempData as $key => $temp) {
                $tempData[$key] = round($temp, 0);
            }
            $chart->series[] = array(
                'type'       => 'bar',
                'name'       => 'temperature',
                'yAxisIndex' => 1,
                'itemStyle'  => array(
                    'normal' => array(
                        'color' => 'rgba(193,35,43,0.3)',
                        'label' => array('show' => true),
                    ),
                ),
                'data'       => $tempData,
            );
        }
        elseif($axe2 === 'precipitations') {
            $chart->yAxis[] = array(
                'type'      => 'value',
                'name'      => 'precipitations',
                'axisLabel' => array(
                    'formatter' => '{value} mm',
                ),
            );
            // Précipitations
            $tempData = array_column($meteo, 'pluie');
            foreach ($tempData as $key => $temp) {
                $tempData[$key] = $temp*10;
            }

            $chart->series[] = array(
                'type'       => 'bar',
                'name'       => 'precipitations',
                'yAxisIndex' => 0,
                'itemStyle'  => array(
                    'normal' => array(
                        'color' => 'rgba(18, 113, 158, 0.3)',
                        'label' => array('show' => false),
                    ),
                ),
                'data'       => $tempData,
            );
        }

        $series = array();
        foreach ($ventes as $key => $vente) {
            if (!isset($series[$vente['marche_label']])) {
                $series[$vente['marche_label']] = array(
                    'type' => 'line',
                    'name' => $vente['marche_label'],
                    'data' => array_fill_keys(array_keys($dataXAxis), null),
                );
            }

            $series[$vente['marche_label']]['data'][$vente[$groupBy]] =
            (int) $vente['quantites_vendues'];
        }

        foreach ($series as $key => $serie) {
            $serie['data']   = array_values($serie['data']);
            $chart->series[] = $serie;
        }


        $chart->title->text = sprintf("Evolution des Marché");

        $chart->title->subtitle = "Par {$groupBy}";

        $chart->tooltip->trigger = 'axis';
        $chart->tooltip->show    = true;
        $chart->toolbox          = array(
            'show'   => true,
            'orient' => 'vertical',
            'x'      => 'right',
            'y'      => 'center',
        );

        include __DIR__ . '/views/marches.php';

        return '';
    }


}
