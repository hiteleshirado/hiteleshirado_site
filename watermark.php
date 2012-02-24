<?php

$dir = __DIR__ . '/images/';

if ( is_dir( $dir ) && $handle = opendir( $dir ) )
{
	while ( false !== ( $file = readdir( $handle ) ) )
	{
		if ( 'png' !== substr( $file, -3 ) ) continue;
		wm( $dir . $file );
		echo "Done: ",  $dir . $file, "\n";
	}

	closedir( $handle );
}

function wm( $file )
{
	$back_img	= imagecreatefromstring( file_get_contents( $file ) );

	imagealphablending( $back_img,false );
	imagesavealpha( $back_img, true );

	$layer_img = imagecreatefrompng( __DIR__ . '/watermark.png' );
	imagealphablending( $layer_img, false );
	imagesavealpha( $layer_img, true );

	imagelayereffect( $back_img, IMG_EFFECT_ALPHABLEND );

	imagecopy(
					$back_img,
					$layer_img,
					0,
					0,
					0,
					0,
					560,
					300
			);

	imagepng( $back_img, $file );
	imagedestroy( $back_img );
	imagedestroy( $layer_img );
}