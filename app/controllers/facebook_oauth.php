<?php

use Symfony\Component\HttpFoundation\Request;

// HOME.
$app->get('/facebook-oauth', function ( Request $request ) use ($app) {
	$auth_facebook = new $app['class_auth_facebook']( $app );

	$code = $request->get( 'code' );
	if ( empty( $code ) )
	{
		$app->abort( 400, 'Missing code' );
	}

	$auth_facebook->loadAccessToken( $code );

	return $app->redirect( $app['url_generator']->generate( 'create_form' ) );
})
->bind('facebook_oauth');

// HOME.
$app->get('/facebook-login', function ( Request $request ) use ($app) {
	$auth_facebook = new $app['class_auth_facebook']( $app );

	$access_token = $auth_facebook->getAccessToken();

	if ( empty( $access_token ) )
	{
		return $auth_facebook->redirectToDialog();
	}

	return $app->redirect( $app['url_generator']->generate( 'create_form' ) );
})
->bind('facebook_login_dialog');


// HOME.
$app->get('/facebook-logout', function ( Request $request ) use ($app) {
	$auth_facebook = new $app['class_auth_facebook']( $app );
	$auth_facebook->logout();

	return $app->redirect( $app['url_generator']->generate( 'home' ) );
})
->bind('facebook_logout');

