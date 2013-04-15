<?php

/**
 * Wrapper for PHP's GD Library for easy image manipulation to draw images
 * on top of each other preserving transparency, writing text with stroke
 * and transparency and drawing shapes.
 *
 * Example:
 * $overlay = '/path/to/images/overlay.png';
 * $image = new PHPImage();
 * $image->setDimensionsFromImage($overlay);
 * $image->draw('/path/to/images/image.jpg');
 * $image->draw($overlay);
 * $image->rectangle(40, 40, 100, 60, array(0, 0, 0), 0.5);
 * $image->setFont('/path/to/fonts/Arial.ttf');
 * $image->setTextColor(array(255, 255, 255));
 * $image->setStrokeWidth(1);
 * $image->setStrokeColor(array(0, 0, 0));
 * $image->text('Hello World!', 12, 50, 50);
 * $image->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 8, 50, 70);
 * $image->show();
 *
 * It is also chainable:
 * $image = new PHPImage();
 * $image->rectangle(40, 40, 100, 60, array(0, 0, 0), 0.5)->setFont('/path/to/fonts/Arial.ttf')
 * ->setTextColor(array(255, 255, 255))->setStrokeWidth(1)->setStrokeColor(array(0, 0, 0))->text('Hello World!', 12, 50, 50)
 * ->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 8, 50, 70)->show();
 *
 * @version
 * @author Blake Kus <blakekus@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @copyright 2013 Blake Kus
 */

class PHPImage {
	/**
	* Canvas resource
	*
	* @var resource
	*/
	private $img;

	/**
	* Global font file
	*
	* @var String
	*/
	private $fontFile;

	/**
	* Global font colour
	*
	* @var array
	*/
	private $textColor = array(255, 255, 255);

	/**
	* Global text opacity
	*
	* @var float
	*/
	private $textOpacity = 1;

	/**
	* Global text angle
	*
	* @var integer
	*/
	private $textAngle = 0;

	/**
	* Global stroke width
	*
	* @var integer
	*/
	private $strokeWidth = 0;

	/**
	* Global stroke colour
	*
	* @var array
	*/
	private $strokeColor = array(0, 0, 0);

	/**
	* Canvas width
	*
	* @var integer
	*/
	private $width;

	/**
	* Canvas height
	*
	* @var integer
	*/
	private $height;

	/**
	* Initialise the image with dimensions, or pass no dimensions and
	* use setDimensionsFromImage to set dimensions from another image.
	*
	* @param integer $width (optional)
	* @param integer $height (optional)
	* @return PHPImage
	*/
	public function __construct($width=null, $height=null){
		if($width !== null && $height !== null){
			$this->initialiseCanvas($width, $height);
		}
	}

	/**
	* Intialise the canvas
	*
	* @param integer $width
	* @param integer $height
	*/
	private function initialiseCanvas($width, $height){
		$this->width = $width;
		$this->height = $height;
		$this->img = imagecreatetruecolor($this->width, $this->height);
		// Set the flag to save full alpha channel information
		imagesavealpha($this->img, true);
		// Turn off transparency blending (temporarily)
		imagealphablending($this->img, false);
		// Completely fill the background with transparent color
		imagefilledrectangle($this->img, 0, 0, $this->width, $this->height, imagecolorallocatealpha($this->img, 0, 0, 0, 127));
		// Restore transparency blending
		imagealphablending($this->img, true);
	}

	/**
	* Set image dimensions from an image source
	*
	* @param String $file
	* @return PHPImage
	*/
    public function setDimensionsFromImage($file){
		list($width, $height, $type) = getimagesize($file);
		$this->initialiseCanvas($width, $height);
		return $this;
	}

	/**
     * Shows the resulting image, always PNG
     */
    public function show(){
		header('Expires: Wed, 1 Jan 1997 00:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-type: image/png');
		imagepng($this->img);
		imagedestroy($this->img);
    }

    /**
     * Save the image
     *
     * @param String $path
     * @param boolean $show
     */
    public function save($path, $show=false){
    	imagepng($this->img, $path);
    	if($show){
			$this->show();
			return;
		}
		imagedestroy($this->img);
	}

	/**
	 * Save the image and show it
	 *
	 * @param string $path
	 */
	public function showAndSave($path){
		$this->save($path, true);
	}

    /**
     * Create a new image resource from image file, supports: jpeg, png, gif
     *
     * @param string $file
     * @return Image Resource
     */
    private function createImage($file){
    	list($width, $height, $type) = getimagesize($file);
		switch ($type) {
			case IMAGETYPE_GIF:
				return imagecreatefromgif($file);
			break;
			case IMAGETYPE_JPEG:
				return imagecreatefromjpeg($file);
			break;
			case IMAGETYPE_PNG:
				return imagecreatefrompng($file);
			break;
			default:
				die('File not supported!');
		}
    }

    /**
    * Draw a line
    *
    * @param integer $x1
    * @param integer $y1
    * @param integer $x2
    * @param integer $y2
    * @param array $colour
    * @param float $opacity
    * @param boolean $dashed
    * @return PHPImage
    */
    public function line($x1=0, $y1=0, $x2=100, $y2=100, $colour=array(0, 0, 0), $opacity=1, $dashed=false){
    	if($dashed === true){
    		imagedashedline($this->img, $x1, $y1, $x2, $y2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imageline($this->img, $x1, $y1, $x2, $y2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}
		return $this;
	}

	/**
	* Draw a rectangle
	*
	* @param integer $x
	* @param integer $y
	* @param integer $width
	* @param integer $height
	* @param array $colour
	* @param float $opacity
	* @param boolean $outline
	* @see http://www.php.net/manual/en/function.imagefilledrectangle.php
	* @return PHPImage
	*/
    public function rectangle($x=0, $y=0, $width=100, $height=50, $colour=array(0, 0, 0), $opacity=1, $outline=false){
    	if($outline === true){
    		imagerectangle($this->img, $x, $y, $x + $width, $y + $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imagefilledrectangle($this->img, $x, $y, $x + $width, $y + $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}
		return $this;
	}

	/**
	* Draw a square
	*
	* @param integer $x
	* @param integer $y
	* @param integer $width
	* @param array $colour
	* @param float $opacity
	* @param boolean $outline
	* @see http://www.php.net/manual/en/function.imagefilledrectangle.php
	* @return PHPImage
	*/
	public function square($x=0, $y=0, $width=100, $colour=array(0, 0, 0), $opacity=1, $outline=false){
		return $this->rectangle($x, $y, $width, $width, $colour, $opacity, $outline);
	}

	/**
	* Draw an ellipse
	*
	* @param integer $x
	* @param integer $y
	* @param integer $width
	* @param integer $height
	* @param array $colour
	* @param float $opacity
	* @param boolean $outline
	* @see http://www.php.net/manual/en/function.imagefilledellipse.php
	* @return PHPImage
	*/
	public function ellipse($x=0, $y=0, $width=100, $height=50, $colour=array(0, 0, 0), $opacity=1, $outline=false){
		if($outline === true){
			imageellipse($this->img, $x, $y, $width, $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imagefilledellipse($this->img, $x, $y, $width, $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}
		return $this;
	}

	/**
	* Draw a circle
	*
	* @param integer $x
	* @param integer $y
	* @param integer $width
	* @param array $colour
	* @param float $opacity
	* @param boolean $outline
	* @see http://www.php.net/manual/en/function.imagefilledellipse.php
	* @return PHPImage
	*/
	public function circle($x=0, $y=0, $width=100, $colour=array(0, 0, 0), $opacity=1, $outline=false){
		return $this->ellipse($x, $y, $width, $width, $colour, $opacity, $outline);
	}

	/**
	* Draw a polygon
	*
	* @param array $points
	* @param array $colour
	* @param float $opacity
	* @param boolean $outline
	* @see http://www.php.net/manual/en/function.imagefilledpolygon.php
	* @return PHPImage
	*/
	public function polygon($points=array(), $colour=array(0, 0, 0), $opacity=1, $outline=false){
		if(count($points) > 0){
			if($outline === true){
				imagepolygon($this->img, $points, count($points) / 2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
			}else{
				imagefilledpolygon($this->img, $points, count($points) / 2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
			}
		}
		return $this;
	}

	/**
	* Draw an arc
	*
	* @param integer $x
	* @param integer $y
	* @param integer $width
	* @param integer $height
	* @param integer $start
	* @param integer $end
	* @param array $colour
	* @param float $opacity
	* @param boolean $outline
	* @see http://www.php.net/manual/en/function.imagefilledarc.php
	* @return PHPImage
	*/
	public function arc($x=0, $y=0, $width=100, $height=50, $start=0, $end=180, $colour=array(0, 0, 0), $opacity=1, $outline=false){
		if($outline === true){
    		imagearc($this->img, $x, $y, $width, $height, $start, $end, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imagefilledarc($this->img, $x, $y, $width, $height, $start, $end, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127), IMG_ARC_PIE);
		}
		return $this;
	}

	/**
	* Draw an image from file
	*
	* Accepts x/y properties from CSS background-position (left, center, right, top, bottom, percentage and pixels)
	*
	* @param String $file
	* @param String|integer $x
	* @param String|integer $y
	* @see http://www.php.net/manual/en/function.imagecopyresampled.php
	* @see http://www.w3schools.com/cssref/pr_background-position.asp
	* @return PHPImage
	*/
    public function draw($file, $x='50%', $y='50%'){
		$image = $this->createImage($file);
		$width = imagesx($image);
		$height = imagesy($image);
		// Defaults if invalid values passed
		if(strpos($x, '%') === false && !is_numeric($x) && !in_array($x, array('left', 'center', 'right'))){
			$x = '50%';
		}
		if(strpos($y, '%') === false && !is_numeric($y) && !in_array($y, array('top', 'center', 'bottom'))){
			$y = '50%';
		}
		// If word passed, convert it to percentage
		switch($x){
			case 'left':
				$x = '0%';
			break;
			case 'center':
				$x = '50%';
			break;
			case 'right':
				$x = '100%';
			break;
		}
		switch($y){
			case 'top':
				$y = '0%';
			break;
			case 'center':
				$y = '50%';
			break;
			case 'bottom':
				$y = '100%';
			break;
		}
		// Work out offset
		if(strpos($x, '%') > -1){
			$x = str_replace('%', '', $x);
			$x = ceil(($this->width - $width) * ($x / 100));
		}
		if(strpos($y, '%') > -1){
			$y = str_replace('%', '', $y);
			$y = ceil(($this->height - $height) * ($y / 100));
		}
		// Draw image
		imagecopyresampled(
			$this->img,
			$image,
			$x,
			$y,
			0,
			0,
			$width,
			$height,
			$width,
			$height
		);
		imagedestroy($image);
		return $this;
	}

	/**
	* Draw text
	*
	* @param String $text
	* @param integer $fontSize
	* @param integer $x
	* @param integer $y
	* @param integer $angle
	* @param integer $strokeWidth
	* @param float $opacity
	* @param array $fontColor
	* @param array $strokeColor
	* @param String $fontFile
	* @see http://www.php.net/manual/en/function.imagettftext.php
	* @return PHPImage
	*/
    public function text($text, $fontSize=12, $x=0, $y=0, $angle=null, $strokeWidth=null, $opacity=null, $fontColor=null, $strokeColor=null, $fontFile=null){
    	if($fontFile === null){
			$fontFile = $this->fontFile;
		}
		if($fontColor === null){
			$fontColor = $this->textColor;
		}
		if($angle === null){
			$angle = $this->textAngle;
		}
		if($strokeWidth === null){
			$strokeWidth = $this->strokeWidth;
		}
		if($opacity === null){
			$opacity = $this->textOpacity;
		}
		if($strokeColor === null){
			$strokeColor = $this->strokeColor;
		}
		// Get Y offset as it 0 Y is the lower-left corner of the character
		$testbox = imagettfbbox($fontSize, $angle, $fontFile, 'W');
		$offset = abs($testbox[7]);
		// Draw stroke
		if($strokeWidth > 0){
			$strokeColor = imagecolorallocatealpha($this->img, $strokeColor[0], $strokeColor[1], $strokeColor[2], (1 - $opacity) * 127);
			for($sx = ($x-abs($strokeWidth)); $sx <= ($x+abs($strokeWidth)); $sx++){
				for($sy = ($y-abs($strokeWidth)); $sy <= ($y+abs($strokeWidth)); $sy++){
					imagettftext($this->img, $fontSize, $angle, $sx, $sy + $offset, $strokeColor, $fontFile, $text);
				}
			}
		}
		// Draw text
		imagettftext($this->img, $fontSize, $angle, $x, $y + $offset, imagecolorallocatealpha($this->img, $fontColor[0], $fontColor[1], $fontColor[2], (1 - $opacity) * 127), $fontFile, $text);
		return $this;
	}

	/**
	* Draw multi-line text box and auto wrap text
	*
	* @param String $text
	* @param integer $width
	* @param integer $fontSize
	* @param integer $x
	* @param integer $y
	* @param integer $angle
	* @param integer $strokeWidth
	* @param float $opacity
	* @param array $fontColor
	* @param array $strokeColor
	* @param String $fontFile
	* @return PHPImage
	*/
	public function textBox($text, $width=100, $fontSize=12, $x=0, $y=0, $angle=null, $strokeWidth=null, $opacity=null, $fontColor=null, $strokeColor=null, $fontFile=null){
		return $this->text($this->wrap($text, $width, $fontSize, $angle, $fontFile), $fontSize, $x, $y, $angle, $strokeWidth, $opacity, $fontColor, $strokeColor, $fontFile);
	}

	/**
	* Helper to wrap text
	*
	* @param String $text
	* @param integer $width
	* @param integer $fontSize
	* @param integer $angle
	* @param String $fontFile
	* @return String
	*/
	private function wrap($text, $width=100, $fontSize=12, $angle=0, $fontFile=null){
		if($fontFile === null){
			$fontFile = $this->fontFile;
		}
		$ret = "";
		$arr = explode(' ', $text);
		foreach ($arr as $word){
			$teststring = $ret . ' ' . $word;
			$testbox = imagettfbbox($fontSize, $angle, $fontFile, $teststring);
			if ($testbox[2] > $width){
				$ret .= ($ret == "" ? "" : "\n") . $word;
			} else {
				$ret .= ($ret == "" ? "" : ' ') . $word;
			}
		}
		return $ret;
	}

	/**
	* Set's global text colour using RGB
	*
	* @param array $colour
	* @return PHPImage
	*/
    public function setTextColor($colour=array(255, 255, 255)){
    	$this->textColor = $colour;
    	return $this;
    }

    /**
    * Set's global text angle
    *
    * @param integer $angle
    * @return PHPImage
    */
    public function setTextAngle($angle=0){
    	$this->textAngle = $angle;
    	return $this;
    }

    /**
    * Set's global text stroke
    *
    * @param integer $strokeWidth
    * @return PHPImage
    */
    public function setStrokeWidth($strokeWidth=0){
    	$this->strokeWidth = $strokeWidth;
    	return $this;
    }

    /**
    * Set's global text opacity
    *
    * @param float $opacity
    * @return PHPImage
    */
    public function setTextOpacity($opacity=1){
    	$this->textOpacity = $opacity;
    	return $this;
    }

    /**
    * Set's global stroke colour
    *
    * @param array $colour
    * @return PHPImage
    */
    public function setStrokeColor($colour=array(0, 0, 0)){
    	$this->strokeColor = $colour;
    	return $this;
    }

	/**
	* Set's global font file for text from .ttf font file (TrueType)
	*
	* @param string $fontFile
	* @return PHPImage
	*/
    public function setFont($fontFile){
    	$this->fontFile = $fontFile;
    	return $this;
    }
}