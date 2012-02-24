<?php

namespace News;

class ScreenList extends \ArrayIterator
{
	protected $depository;
	protected $page;
	protected $dir;
	protected $count;

	const PAGE_SIZE = 10;

	public function __construct( $depository, $page )
	{
		$this->depository	= $depository;
		$this->page			= $page;

		$this->dir			= $this->depository->getDir();

		parent::__construct( $this->loadScreens() );
	}

	public function getPage()
	{
		return $this->page;
	}

	public function getPages()
	{
		if ( !isset( $this->count ) )
		{
			$this->count = count( self::getAllFilesCached( $this->dir ) );
		}

		return ceil( $this->count / self::PAGE_SIZE );
	}

	protected function loadScreens()
	{
		$screens	= array();
		$page_files = $this->getPageFiles();

		foreach( $page_files as $page_file )
		{
			$screens[] = $this->depository->getOne( $page_file );
		}

		return $screens;
	}

	protected function getPageFiles()
	{
		$all_files = self::getAllFilesCached( $this->dir );

		return array_slice(
				$all_files,
				self::PAGE_SIZE * ( $this->page -1 ),
				self::PAGE_SIZE
		);
	}

	static public function flushCache()
	{
		$cache = self::getCache();
		$cache->delete( self::getCacheKey() );
	}

	static protected function getAllFilesCached( $dir )
	{
		$cache = self::getCache();
		$cache_key = self::getCacheKey();

		if ( false === ( $file_list = $cache->get( $cache_key ) ) )
		{
			$file_list = self::getAllFiles( $dir );
			$cache->set( $cache_key, $file_list, 0, 14400 );
		}

		return $file_list;
	}

	static protected function getAllFiles( $dir )
	{
		$files = array();
		if ( is_dir( $dir ) && $handle = opendir( $dir ) )
		{
			while ( false !== ( $file = readdir( $handle ) ) )
			{
				if ( 'png' !== substr( $file, -3 ) ) continue;
				// We don't get about the extension anymore.
				$files[] = preg_replace( '/\.png$/i', '', $file );
			}

			closedir( $handle );
			rsort( $files );
		}

		return $files;
	}

	static protected function getCache()
	{
		$cache = new \Memcache;
		$cache->addServer( 'localhost', 11211 );

		return $cache;
	}

	static protected function getCacheKey()
	{
		static $cache_key;

		if ( !isset( $cache_key ) )
		{
			$cache_key = substr( md5( __DIR__ ), 0, 3 ) . 'hiteleshirado.com_filelist';
		}

		return $cache_key;
	}
}