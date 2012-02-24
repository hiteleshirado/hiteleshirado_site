<?php

// We will need to access session.
$app->register(new Silex\Provider\SessionServiceProvider(), array(
	'session.storage.options' => array(
		'lifetime' => 2592000, // 1 month.
	),
));

// Symfony bridges - needed for url generator to be accessible from twig.
$app->register(new Silex\Provider\SymfonyBridgesServiceProvider(), array(
    'symfony_bridges.class_path'  => __DIR__ . '/../vendor/',
));

// Url generator.
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// We use Twig for templating.
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__ . '/../views',
    'twig.class_path' => __DIR__ . '/../vendor/twig/lib',
    'twig.options'    => array(
        'debug' => $app['debug'],
    ),
));
