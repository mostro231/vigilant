<?php
date_default_timezone_set('America/Bogota');

require('../vendor/autoload.php');
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/monitor', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('monitor.twig');
});

$app->get('/almacenar/{consumo}/{temperatura}', function($consumo, $temperatura) use($app) {
  $app['monolog']->addDebug('logging output.');

  $dbconexion=pg_connect( "host=ec2-54-221-198-156.compute-1.amazonaws.com port=5432 dbname=daclr0pc5h658k user=kzlmednsrgkxpn password=e98f8854ed2ce71f7f34dae87b70a63675e41c8e6ba689aae13927da6a974232");
  $registro=array(
    "fecha"=>date('Y-m-d H:i:s'),
    "temperatura"=>$temperatura,
    "consumo"=>$consumo);
  $resultado=pg_insert ($dbconexion,"monitor",$registro);
  return $resultado;
});

$app->get('/registrarlog/{apertura}', function($apertura) use($app) {
  $app['monolog']->addDebug('logging output.');

  $dbconexion=pg_connect( "host=ec2-54-221-198-156.compute-1.amazonaws.com port=5432 dbname=daclr0pc5h658k user=kzlmednsrgkxpn password=e98f8854ed2ce71f7f34dae87b70a63675e41c8e6ba689aae13927da6a974232");
  $registro=array(
    "fecha"=>date('Y-m-d H:i:s'),
    "estado"=>$apertura);
  $resultado=pg_insert ($dbconexion,"monitor_apertura",$registro);
  return $resultado;
});

$app->get('/getTemperaturaData/{numberOfRecords}', function($numberOfRecords) use($app){
  $app['monolog']->addDebug('logging output.');

  $dbconexion=pg_connect( "host=ec2-54-221-198-156.compute-1.amazonaws.com port=5432 dbname=daclr0pc5h658k user=kzlmednsrgkxpn password=e98f8854ed2ce71f7f34dae87b70a63675e41c8e6ba689aae13927da6a974232");
 // $consult_db = pg_query($dbconexion, 'SELECT "fecha","temperatura" FROM public."monitor" ORDER BY "fecha" DESC LIMIT ' . $numberOfRecords .'');
  
  $consult_db = pg_query($dbconextion, 'select array_to_json(array_agg(row_to_json(t)))
    from (
      select * FROM public."monitor" ORDER BY "fecha" DESC LIMIT ' . $numberOfRecords .'
    ) t');

  return $consult_db;

  $resultArray = array();
  while ($row = pg_fetch_row($consult_db, null, PGSQL_ASSOC)) {
    $resultArray[] = $row;
  }

  $jsonResult = json_encode($resultArray, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);

  $response = new Response();
  $response->setContent($jsonResult);
  $response->setCharset('UTF-8');
  $response->headers->set('Content-Type', 'application/json');

  return $response;
});

$app->run();
