<?php

use Silex\Provider\MonologServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Form\Extensions\Doctrine\Bridge\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;

// include the prod configuration
require __DIR__.'/prod.php';

// enable the debug mode
$app['debug'] = true;

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/silex_dev.log',
));

$app->register(new WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../var/cache/profiler',
));

// Doctrine Brigde for form extension
$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
    $manager = new Form\Extensions\Doctrine\Bridge\ManagerRegistry(
        null, array(), array('default'), null, null, '\Doctrine\ORM\Proxy\Proxy'
    );
    $manager->setContainer($app);
    $extensions[] = new Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension($manager);

    return $extensions;
}));

$app['twig']->addExtension(new Twig_Extension_Debug());