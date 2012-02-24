<?php

use Symfony\Component\HttpFoundation\Request;

class DisplayableError extends Exception {}

// Creating new screen.
$app->post('/uj', function ( Request $request ) use ($app) {

	$auth_facebook = new $app['class_auth_facebook']( $app );
	if ( !$auth_facebook->isLoggedIn() )
	{
		$app->abort( 403, "No upload allowed any more." );
	}

	$user_id	= $auth_facebook->getUserId();
	$assign		= array();

	try
	{
		$place	= $request->get('place');
		$title	= $request->get('title');
		$link	= $request->get('link');

		if (
			empty( $place ) ||
			empty( $title )
		)
		{
			throw new DisplayableError( 'Add meg a helyet is meg a címet is!' );
		}

		if ( empty( $link ) )
		{
			$link = null;
		}
		elseif ( !preg_match( '/^https?:\/\//i', $link ) )
		{
			$link = 'http://' . $link;
		}

		$file = $request->files->get('background');

		if ( empty( $file ) )
		{
			throw new DisplayableError( 'A háttérképet elfejetetted feltölteni!' );
		}

		if ( !$file->isValid() )
		{
			throw new DisplayableError( 'Biztos kiválasztottad a háttérképet?' );
		}

		$file = $file->move( sys_get_temp_dir() . '/upl_img/', uniqid() );
	}
	catch ( DisplayableError $e )
	{
		$app['session']->set( 'flash', $e->getMessage() );
		return $app->redirect( $app['url_generator']->generate( 'home' ) );
	}

	$app['session']->set( 'success', true );

	$screen_depository = new $app['class_news_screendepository']( $app );
	$screen = $screen_depository->create( $title, $place, $file, $user_id, $link );

	return $app->redirect( $app['url_generator']->generate( 'screen', array(
		'filename' => $screen->getFilename(),
	) ) );

})->bind('create');

$app->get('/uj', function ( Request $request ) use ($app) {

	return $app['twig']->render('create.twig');

})->bind('create_form');