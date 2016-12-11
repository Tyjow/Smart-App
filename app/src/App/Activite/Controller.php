<?php
namespace App\Activite;

use Silex\Application;
use \DateTime as DateTime;
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
            '/marches/{periode}/{group_by}'
            , 'App\Activite\Controller::marches'
        )
            ->value('periode', null)
            ->value('group_by', 'mois');

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
     * @param $periode
     * @param Request $request
     * @param Application $app
     * @return mixed
     */
    public function marches($periode, Request $request, Application $app)
    {
        $result = array(
            'error'    => 0,
            'messages' => array(),
        );

        // Vérification période demandée
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

        $result['messages'][] = sprintf("Requête période du %s au %s"
            , $periodQuery['debut']
            , $periodQuery['fin']
        );

        // Requête
        $ventes = $app['modelActivite']->getVentes(
            $periodQuery['debut'],
            $periodQuery['fin']
        );

        $legends = array_unique(array_column($ventes, 'marche_label'));

        $xAxis = array(
            array(
                'type' => 'mois',
                'data' => array_values(array_unique(array_column($ventes, 'mois'))),
            ),
        );

        $yAxis = array(
            array('type' => 'value'),
        );

        $series = array();
        foreach ($ventes as $key => $vente) {
            if (!isset($series[$vente['marche_label']])) {
                $series[$vente['marche_label']] = array(
                    'type' => 'line',
                    'name' => $vente['marche_label'],
                    'data' => [],
                );
            }

            $series[$vente['marche_label']]['data'][] =
                (int) $vente['quantites_vendues'];

        }

        $result['result'] = array(
            'legends' => $legends,
            'xAxis'   => $xAxis,
            'yAxis'   => $yAxis,
            'series'  => $series,
        );

        include (__DIR__ . '/views/marches.php');

        return;
    }

}
