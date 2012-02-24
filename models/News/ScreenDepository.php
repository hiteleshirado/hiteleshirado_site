<?php

namespace News;

class ScreenDepository
{
    protected $app;
	protected $user_screens = array();

    public function __construct( $app )
    {
        $this->app = $app;

		$this->loadScreensFromSession();
    }

	public function getDir()
	{
		return $this->app['screens_dir'];
	}

	public function getPage( $page )
	{
		return new ScreenList( $this, $page );
	}

	public function getOne( $file )
	{
		// Security is important.
		$file = basename( $file );
		$screen = Screen::fromExisting( $this->app['screens_dir'], $file );

		if ( isset( $screen ) && isset( $this->user_screens[$screen->getFilename()] ) )
		{
			$screen->setAdministrable( true );
		}

		return $screen;
	}

	public function getToplist()
	{
		$toplist = array();
		$file = $this->app['screens_dir'] . '/toplist.txt';

		if ( is_readable( $file ) )
		{
			$handle = fopen( $file, 'r' );
			while( !feof( $handle ) )
			{
				$line = trim( fgets( $handle ), "\n" );
				$screen = $this->getOne( $line );

				if ( isset( $screen ) )
				{
					$toplist[] = $screen;
				}
			}
		}

		return $toplist;
	}

	public function create( $title, $place, $background_image, $user_id, $link )
	{
		$screen = new Screen( $title, $place, $this->app['screens_dir'], $user_id, $link );
		$screen->generate( $background_image );

		// Remembering that it's our own.
		$filename = $screen->getFilename();
		$this->user_screens[$filename] = true;
		$this->saveScreensToSession();

		ScreenList::flushCache();

		return $screen;
	}

	public function delete( $file )
	{
		$screen = $this->getOne( $file );
		if ( !isset( $screen ) )
		{
			return;
		}
		if ( !$screen->isAdministrable() )
		{
			$app->abort( 403, "Cant edit screen $file" );
			return;
		}

		if ( !$screen->delete() )
		{
			throw new Exception( "Unable to delete screen $file" );
		}

		ScreenList::flushCache();
	}

	protected function loadScreensFromSession()
	{
		$screens = $this->app['session']->get( 'screens' );

		if ( is_array( $screens ) )
		{
			$this->user_screens = $screens;
		}
	}

	protected function saveScreensToSession()
	{
		$screens = $this->app['session']->set( 'screens', $this->user_screens );
	}
}