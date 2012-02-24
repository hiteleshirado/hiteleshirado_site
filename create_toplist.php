<?php

$dir = __DIR__ . '/images/';
$files = array();

if ( is_dir( $dir ) && $handle = opendir( $dir ) )
{
	while ( false !== ( $file = readdir( $handle ) ) )
	{
		if ( 'png' !== substr( $file, -3 ) ) continue;
		$files[] = preg_replace( '/\.png$/i', '', $file );
	}

	closedir( $handle );
}

$i = 1;
$all = count( $files );
$like_counts = array();

foreach ( $files as $file )
{
	$likes = get_likes( $file );
	$like_counts[$file] = $likes;
	echo "($i/$all) $file: $likes likes\n";

	$i++;
}

arsort( $like_counts );
$toplist = array_slice( $like_counts, 0, 10, true );
$file = $dir . '/toplist.txt';

file_put_contents( $file, implode( "\n", array_keys( $toplist ) ) );
chmod( $file, 0777 );

var_dump( $toplist );

function get_likes( $file )
{
	$query = "SELECT total_count FROM link_stat WHERE url IN ( \"http://hiteleshirado.com/kepernyo/$file\", \"http://hiteleshirado.com/?show=$file.png\" )";
	$url = 'https://graph.facebook.com/fql';

	$attempts = 3;

	while ( $attempts-- )
	{
		// Facebook has a 600 q / 600s limit...
		sleep( 1 );

		$response = curl_get( $url, array( 'q'=> $query ), array( CURLOPT_FORBID_REUSE => true ) );
		$data = json_decode( $response, true );

		if ( !isset( $data ) || !isset( $data['data'] ) )
		{
			continue;
		}

		$likes = 0;
		foreach ( $data['data'] as $row )
		{
			$likes += $row['total_count'];
		}

		return $likes;
	}

	throw new Exception( "Unable to parse response from Facebook: $response" );
}

function curl_get( $url, array $get = NULL, array $options = array() )
{
    $defaults = array(
        CURLOPT_URL => $url. ( strpos( $url, '?' ) === FALSE ? '?' : '' ) .
			http_build_query( $get ),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 12
    );

    $ch = curl_init();
    curl_setopt_array( $ch, ( $options + $defaults ) );
    if( !$result = curl_exec( $ch ) )
    {
        trigger_error( curl_error( $ch ) );
    }
    curl_close( $ch );
    return $result;
}