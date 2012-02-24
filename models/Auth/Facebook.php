<?php

namespace Auth;

class Facebook
{
    protected $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }

	public function isLoggedIn()
	{
		$access_token = $this->app['session']->get( 'facebook_access_token' );
		return isset( $access_token );
	}

	public function getUserName()
	{
		$user_name = $this->app['session']->get( 'facebook_user_name' );

		if ( empty( $user_name ) )
		{
			$this->loadUserData();
		}

		return $this->app['session']->get( 'facebook_user_name' );
	}

	public function getUserId()
	{
		$user_name = $this->app['session']->get( 'facebook_user_id' );

		if ( empty( $user_name ) )
		{
			$this->loadUserData();
		}

		return $this->app['session']->get( 'facebook_user_id' );
	}

	public function getAccessToken()
	{
		$access_token	= $this->app['session']->get( 'facebook_access_token' );
		$token_expires	= $this->app['session']->get( 'facebook_access_token_expires' );

		if ( isset( $access_token ) && isset( $token_expires ) && $token_expires < time() )
		{
			return $access_token;
		}

		$code = $this->app['session']->get( 'facebook_code' );

		if ( isset( $code ) )
		{
			return $this->loadAccessToken( $code );
		}

		return null;
	}

	public function redirectToDialog()
	{
		$url = sprintf(
			$this->app['facebook_oauth_dialog'],
			$this->app['facebook_app_id'],
			urlencode( $this->getRedirectUrl() )
		);

		return $this->app->redirect( $url );
	}

	public function loadAccessToken( $code )
	{
		$url = sprintf(
			$this->app['facebook_oauth_token_url'],
			$this->app['facebook_app_id'],
			urlencode( $this->getRedirectUrl() ),
			$this->app['facebook_app_secret'],
			$code
		);

		$response = $this->request( $url );
		parse_str( $response, $token );

		if ( empty( $token ) )
		{
			throw new Exception( 'Can\'t get auth token, response: ' . $response );
		}

		$this->app['session']->set( 'facebook_code', $code );
		$this->app['session']->set( 'facebook_access_token', $token['access_token'] );
		$this->app['session']->set( 'facebook_access_token_expires', time() + $token['expires'] );

		return $token['access_token'];
	}

	public function logout()
	{
		$this->app['session']->set( 'facebook_code', null );
		$this->app['session']->set( 'facebook_access_token', null );
		$this->app['session']->set( 'facebook_access_token_expires', null );
		$this->app['session']->set( 'facebook_user_name', null );
		$this->app['session']->set( 'facebook_user_id', null );
	}

	protected function loadUserData()
	{
		$access_token = $this->getAccessToken();
		if ( empty( $access_token ) )
		{
			throw new \RuntimeException( 'Can\'t get access token from session.' );
		}

		$url = sprintf(
			$this->app['facebook_api_me'],
			$access_token
		);

		$response = $this->request( $url );

		$data = json_decode( $response, true );

		if ( empty( $data ) || empty( $data['name'] ) || empty( $data['id'] ) )
		{
			throw new Exception( 'Can\'t get user data, response: ' . $response );
		}

		$this->app['session']->set( 'facebook_user_id', $data['id'] );
		$this->app['session']->set( 'facebook_user_name', $data['name'] );
	}

	protected function getRedirectUrl()
	{
		return $this->app['url_generator']->generate( 'facebook_oauth', array(), true );
	}

	protected function request( $url )
	{
		$defaults = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 12
		);

		$ch = curl_init();
		curl_setopt_array( $ch, $defaults );

		if( !$result = curl_exec( $ch ) )
		{
			trigger_error( curl_error( $ch ) );
		}

		curl_close( $ch );

		return $result;
	}
}