<?php

// Serving image.
$app->get('/kepernyo-kep/{filename}', function ( $filename ) use ($app) {
	$screen_depository = new $app['class_news_screendepository']( $app );
    $screen = $screen_depository->getOne( $filename );

	if ( !isset( $screen ) )
	{
		$app->abort( 404, "Screen $filename does not exist." );
	}

	// 30 days.
	header( 'Expires: '. gmdate( 'D, d M Y H:i:s', time() + 2592000 ). 'GMT' );
	header( "Content-Type: image/png" );

	readfile( $screen->getImagePath() );

})->bind('image');

// Regi atiranyit...
$app->get('/images/{filename}.png', function ( $filename ) use ( $app ) {
	return $app->redirect( $app['url_generator']->generate( 'image', array( 'filename' => $filename ) ), 301 );
});
