<?php

use Symfony\Component\HttpFoundation\Request;

// Creating new screen.
$app->post('/torles', function ( Request $request ) use ($app) {

	$assign = array();

	$filename = $request->get('filename');

	if ( !empty( $filename ) )
	{
		$screen_depository = new $app['class_news_screendepository']( $app );
		$screen_depository->delete( $filename );
	}

	$app['session']->set( 'delete_success', true );

	return $app->redirect( $app['url_generator']->generate( 'home', array() ) );

})->bind('delete');