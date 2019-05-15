<?php

require('../vendor/autoload.php');

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

<<<<<<< HEAD
$app->get('/alert', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('alert.twig');
=======
$app->get('/monitor', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('monitor.twig');
});

$app->get('/almacenar/{consumo}/{temperatura}/{apertura}', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return "ok";
>>>>>>> 466ef0529b6496bcb6964fc976dc7b80cd5d57ca
});

$app->run();
