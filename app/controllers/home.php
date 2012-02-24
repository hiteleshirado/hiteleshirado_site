<?php

use Symfony\Component\HttpFoundation\Request;

// HOME.
$app->get('/{page}', function ( $page, Request $request ) use ($app) {
	if ( $show = $request->get( 'show' ) )
	{
		// Old URL format.

		$filename = preg_replace( '/\.png$/i', '', $show );
		return $app->redirect( $app['url_generator']->generate( 'screen', array(
			'filename' => $filename,
		) ), 301 );
	}

	$screen_depository = new $app['class_news_screendepository']( $app );

    return $app['twig']->render('index.twig', array(
        'screens' => $screen_depository->getPage( $page ),
        'toplist' => $screen_depository->getToplist(),
    ));
})
->assert('page', '\d+')
->value('page', '1')
->bind('home');

$app->get('/-ra', function () use ( $app ) {
	return $app->redirect( $app['url_generator']->generate( 'home', array() ), 301 );
});