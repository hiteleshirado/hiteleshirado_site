<?php

// Landing paeg for one screen.
$app->get('/kepernyo/{filename}', function ( $filename ) use ($app) {
	$screen_depository = new $app['class_news_screendepository']( $app );
	$screen = $screen_depository->getOne( $filename );

	if ( !isset( $screen ) )
	{
		$app->abort( 404, "Screen $filename does not exist." );
	}

    return $app['twig']->render('screen.twig', array(
        'screen' => $screen,
    ));
})->bind('screen');