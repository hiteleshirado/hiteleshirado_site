<?php

require_once __DIR__ . '/../vendor/silex/silex.phar';

$app = new Silex\Application();

// Debug mode is on depending on the setenv of the server.
$app['debug'] = (bool) @$_SERVER['DEVEL'];

require( __DIR__ . '/../app/autoloaders.php' );
require( __DIR__ . '/../app/extensions.php' );
require( __DIR__ . '/../app/services.php' );
require( __DIR__ . '/../app/controllers.php' );

/**
 * Flash messages.
 */
$app->before( function() use ( $app ) {
	$flash_message_keys = array(
		'flash',
		'success',
		'delete_success',
	);

	foreach ( $flash_message_keys as $flash_message_key )
	{
		$flash = $app['session']->get( $flash_message_key );

		if ( !empty( $flash ) )
		{
			$app['session']->set( $flash_message_key, null );
			$app['twig']->addGlobal( $flash_message_key, $flash );
		}
	}

	if ( $app['debug'] )
	{
		// Serving from local while developing.
		$app['twig']->addGlobal( 'cdn_url', '' );
	}
	else
	{
		$app['twig']->addGlobal( 'cdn_url', 'http://d2fmdnatsa6jaq.cloudfront.net' );
	}

	$app['twig']->addGlobal( 'auth_facebook', new $app['class_auth_facebook']( $app ) );
});

$app->run();