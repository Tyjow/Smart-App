<?php
ini_set('date.timezone', 'Europe/Paris');
setlocale(LC_ALL, 'fr_FR.UTF-8');

$loader = require __DIR__.'/vendor/autoload.php';
$loader->add('App', __DIR__ . '/src/');

$app = new Silex\Application();

$app['debug'] = true;

\Hisune\EchartsPHP\Config::$minify = false;

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array (
        'driver'    => 'pdo_mysql',
        'host'      => 'percona57',
        'dbname'    => 'smartapp',
        'user'      => 'root',
        'password'  => 'root',
        'charset'   => 'utf8mb4',
    ),
));

$app['modelMarche'] = function() use ($app) {
    return new App\Marche\Model($app['db']);
};
$app['modelActivite'] = function() use ($app) {
    return new App\Activite\Model($app['db']);
};
$app['modelMeteo'] = function() use ($app) {
    return new App\Meteo\Model($app['db']);
};


$app->mount('/activite', new App\Activite\Controller());

$app->get('/entrees', function() use ($app) {
    include __DIR__ . '/src/stat_you.html';
    return true;
});

$app->get('/', function () use ($app) {
    include 'index.html';
    return true;
});



/**
 * Marché
 */
// $app->get('/marche/getAll/json', function () use ($app) {
//     $result = $app['modelMarche']->getMarches();
//     return json_encode($result);
// });

/**
 * Activité
 */

/*
$app->get('/activite/annee/getAll', function () use ($app) {
    $result = $app['modelActivite']->get(1);
    return json_encode($result);
});

$app->get('/activite/annee/getAll', function () use ($app) {
    $sql    = 'SELECT DISTINCT YEAR(`jour_date`) AS `annee` FROM `activite_horaire`';
    $result = $app['db']->fetchAll($sql);
    return json_encode($result);
});

$app->get('/activite/mois/getAll', function () use ($app) {
    $sql    = 'SELECT DISTINCT MONTH(`jour_date`) AS `mois` FROM `activite_horaire`';
    $result = $app['db']->fetchAll($sql);
    return json_encode($result);
});

$app->get('/activite/semaine/getAll', function () use ($app) {
    $sql    = 'SELECT DISTINCT YEAR(`jour_date`) AS `annee`, WEEK(`jour_date`) AS semaine FROM `activite_horaire`';
    $result = $app['db']->fetchAll($sql);
    return json_encode($result);
});


$app->get('/activite/ca/{etat}/{periode}', function ($etat, $periode) use ($app) {
})
->value('periode', null)
->value('etat', 'positif');
*/



$app->run();