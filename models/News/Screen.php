<?php

namespace News;

class Screen
{
	protected $place;
	protected $title;
	protected $filename;
	protected $administrable;
	protected $user_id;
	protected $link;

    public function __construct( $title, $place, $dir, $user_id, $link )
	{
		$this->place			= $place;
		$this->title			= $title;
		$this->dir				= $dir;
		$this->user_id			= $user_id;
		$this->link				= $link;
		$this->administrable	= false;
	}

	public function getImagePath()
	{
		if ( !isset( $this->filename ) )
		{
			throw new \LogicException( 'No file generated for this screen yet.' );
		}

		return $this->dir . '/' . $this->filename . '.png';
	}

	public function setFilename( $filename )
	{
		$this->filename = $filename;
	}

	public function getFilename()
	{
		return $this->filename;
	}

	public function getHeader()
	{
		return $this->title . ', ' . $this->place;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getPlace()
	{
		return $this->place;
	}

	public function getLink()
	{
		return $this->link;
	}

	public function setAdministrable( $administrable )
	{
		$this->administrable = $administrable;
	}

	public function isAdministrable()
	{
		return $this->administrable;
	}

	public function generate( $background_file )
	{
		if ( isset( $this->filename ) )
		{
			throw new \LogicException( 'File is already generated!' );
		}

		$this->filename = ( (string) time() ) . "_" . substr( md5( uniqid() ), 0, 3 );

		$this->createImage( $background_file );
		$this->writeText();
	}

	public function delete()
	{
		$image_path = $this->getImagePath();
		$title_path = $image_path . '.txt';

		return unlink( $image_path ) && unlink( $title_path );
	}

	protected function writeText()
	{
		$title_path = $this->getImagePath() . '.txt';
		file_put_contents( $title_path, "{$this->title}\t{$this->place}\t{$this->user_id}\t{$this->link}" );
	}

	protected function createImage( $background_file )
	{
		setlocale( LC_ALL, "hu_HU" );

		$place = mb_strtoupper( mb_substr( $this->place, 0, 50, 'UTF-8' ), 'UTF-8' );
		$title = mb_strtoupper( mb_substr( $this->title, 0, 40, 'UTF-8' ), 'UTF-8' );

		$img_path = $this->getImagePath();
		if ( !is_dir( dirname( $img_path ) ) ) mkdir( dirname( $img_path ), 0777, true );

		$width		= 560;
		$height		= 300;
		$img		= imagecreatetruecolor( $width, $height );

		imagealphablending( $img,false );
		imagesavealpha( $img, true );

		$back_img	= imagecreatefromstring( file_get_contents( $background_file ) );
		$b_width	= imagesx( $back_img );
		$b_height	= imagesy( $back_img );

		if ( $b_width / $b_height > $width / $height )
		{
			$crop_w = $width * ( $b_height / $height );
			$crop_x = ( $b_width - $crop_w ) / 2;
			$crop_h = $b_height;
			$crop_y = 0;
		}
		else
		{
			$crop_w = $b_width;
			$crop_x = 0;
			$crop_h = $height * ( $b_width / $width );
			$crop_y = ( $b_height - $crop_h ) / 2;
		}

		imagecopyresampled( $img, $back_img, 0, 0, $crop_x, $crop_y, $width, $height, $crop_w, $crop_h );
		imagedestroy( $back_img );

		$layer_img = imagecreatefrompng( __DIR__ . '/../../layer.png' );
		imagealphablending( $layer_img, false );
		imagesavealpha( $layer_img, true );

		imagelayereffect( $img, IMG_EFFECT_ALPHABLEND );

		imagecopy(
						$img,
						$layer_img,
						0,
						0,
						0,
						0,
						$width,
						$height
				);

		$font = __DIR__ . '/../../arialbd.ttf';

		// Cim.
		$font_size = 14;
		$font_color = imagecolorallocate( $img, 160, 0, 0 );
		imagettftext( $img, $font_size, 0, 88, 244, $font_color, $font, $title );

		// Helyszin.
		$font_size = 10;
		$font_color = imagecolorallocate( $img, 210, 210, 210 );
		imagettftext( $img, $font_size, 0, 88, 220, $font_color, $font, $place );

		imagepng( $img, $img_path );
		imagedestroy( $img );

		return $img_path;
	}

	public static function fromExisting( $dir, $file )
	{
		list( $title, $place, $user_id, $link ) = self::readTexts( $dir, $file ) + array( null, null, null, null );

		if ( !isset( $title ) )
		{
			return null;
		}

		$screen = new self( $title, $place, $dir, $user_id, $link );
		$screen->setFilename( $file );

		return $screen;
	}

	protected static function readTexts( $dir, $file )
	{
		$title_path = $dir . '/' . $file . '.png.txt';
		if ( is_readable( $title_path ) )
		{
			return explode( "\t", file_get_contents( $title_path ) );
		}
	}
}
