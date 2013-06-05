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
 * $image->text('Hello World!', array('fontSize' => 12, 'x' => 50, 'y' => 50));
 * $image->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', array('fontSize' => 8, 'x' => 50, 'y' => 70));
 * $image->show();
 *
 * It is also chainable:
 * $image = new PHPImage();
 * $image->rectangle(40, 40, 100, 60, array(0, 0, 0), 0.5)->setFont('/path/to/fonts/Arial.ttf')
 * ->setTextColor(array(255, 255, 255))->setStrokeWidth(1)->setStrokeColor(array(0, 0, 0))->text('Hello World!', array('fontSize' => 12, 'x' => 50, 'y' => 50))
 * ->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', array('fontSize' => 8, 'x' => 50, 'y' => 70))->show();
 *
 * @version 0.2
 * @author Blake Kus <blakekus@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @copyright 2013 Blake Kus
 *
 * CHANGELOG:
 * version 0.2 2013-05-23
 * Add support for remote images
 * Add error handling when reading/writing files
 * Add ability to draw text box and auto fit text and align text
 *
 * version 0.1 2013-04-15
 * Initial release
 */

class PHPImage {
	/**
	* Canvas resource
	*
	* @var resource
	*/
	private $img;

	/**
	* PNG Compression level: from 0 (no compression) to 9.
	*
	* @var integer
	*/
	private $quality = 3;

	/**
	* Global font file
	*
	* @var String
	*/
	private $fontFile;

	/**
	* Global font size
	*
	* @var integer
	*/
	private $fontSize = 12;

	/**
	* Global text vertical alignment
	*
	* @var String
	*/
	private $alignVertical = 'top';

	/**
	* Global text horizontal alignment
	*
	* @var String
	*/
	private $alignHorizontal = 'left';

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
	* Default folder mode to be used if folder structure needs to be created
	*
	* @var String
	*/
	private $folderMode = 0755;

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
    	if($info = $this->getImageInfo($file, false)){
			$this->initialiseCanvas($info->width, $info->height);
			return $this;
		} else {
			$this->handleError($file . ' is not readable!');
		}
	}

	/**
	 * Check if an image (remote or local) is a valid image and return type, width, height and image resource
	 *
	 * @param string $file
	 * @param boolean $returnResource
	 * @return stdClass
	 */
	private function getImageInfo($file, $returnResource=true){
		if (preg_match('#^https?://#i', $file)) {
			$headers = get_headers($file, 1);
			if (is_array($headers['Content-Type'])) {
				// Some servers return an array of content types, Facebook does this
				$contenttype = $headers['Content-Type'][0];
			} else {
				$contenttype = $headers['Content-Type'];
			}
			if (preg_match('#^image/(jpe?g|png|gif)$#i', $contenttype)) {
				switch(true){
					case stripos($contenttype, 'jpeg') !== false:
					case stripos($contenttype, 'jpg') !== false:
						$img = imagecreatefromjpeg($file);
						$type = IMAGETYPE_JPEG;
					break;
					case stripos($contenttype, 'png') !== false:
						$img = imagecreatefrompng($file);
						$type = IMAGETYPE_PNG;
					break;
					case stripos($contenttype, 'gif') !== false:
						$img = imagecreatefromgif($file);
						$type = IMAGETYPE_GIF;
					break;
					default:
						return false;
					break;
				}
				$width = imagesx($img);
				$height = imagesy($img);
				if (!$returnResource) {
					imagedestroy($img);
				}
			} else {
				return false;
			}
		} elseif (is_readable($file)) {
			list($width, $height, $type) = getimagesize($file);
			switch($type){
				case IMAGETYPE_GIF:
					if ($returnResource) {
						$img = imagecreatefromgif($file);
					}
				break;
				case IMAGETYPE_JPEG:
					if ($returnResource) {
						$img = imagecreatefromjpeg($file);
					}
				break;
				case IMAGETYPE_PNG:
					if ($returnResource) {
						$img = imagecreatefrompng($file);
					}
				break;
				default:
					return false;
				break;
			}
		} else {
			return false;
		}
		$info = new stdClass();
		$info->type = $type;
		$info->width = $width;
		$info->height = $height;
		if ($returnResource) {
			$info->resource = $img;
		}
		return $info;
	}

	/**
	 * Handle errors
	 *
	 * @param String $error
	 */
	private function handleError($error){
		die($error);
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
		imagepng($this->img, null, $this->quality);
		imagedestroy($this->img);
		die();
    }

    /**
     * Save the image
     *
     * @param String $path
     * @param boolean $show
     */
    public function save($path, $show=false){
    	if (!is_writable(dirname($path))) {
    		if (!mkdir(dirname($path), $this->folderMode, true)) {
			    $this->handleError(dirname($path) . ' is not writable and failed to create directory structure!');
			}
		}
    	if (is_writable(dirname($path))) {
    		imagepng($this->img, $path, $this->quality);
		} else {
			$this->handleError(dirname($path) . ' is not writable!');
		}
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
    	if($info = $this->getImageInfo($file)){
			$image = $info->resource;
			$width = $info->width;
			$height = $info->height;
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
		} else {
			$this->handleError($file . ' is not a valid image!');
		}
	}

	/**
	 * Draw text
	 *
	 * ### Options
	 *
	 * - integer $fontSize
	 * - integer $x
	 * - integer $y
	 * - integer $angle
	 * - integer $strokeWidth
	 * - float $opacity
	 * - array $fontColor
	 * - array $strokeColor
	 * - String $fontFile
	 *
	 * @param String $text
	 * @param array $options
	 * @see http://www.php.net/manual/en/function.imagettftext.php
	 * @return PHPImage
	 */
	public function text($text, $options=array()){
		$defaults = array(
			'fontSize' => $this->fontSize,
			'fontColor' => $this->textColor,
			'opacity' => $this->textOpacity,
			'x' => 0,
			'y' => 0,
			'width' => null,
			'height' => null,
			'alignHorizontal' => $this->alignHorizontal,
			'alignVertical' => $this->alignVertical,
			'angle' => $this->textAngle,
			'strokeWidth' => $this->strokeWidth,
			'strokeColor' => $this->strokeColor,
			'fontFile' => $this->fontFile,
			'autoFit' => true,
			'debug' => false
		);
		extract(array_merge($defaults, $options), EXTR_OVERWRITE);
		if(is_int($width) && $autoFit){
			$fontSize = $this->fitToWidth($fontSize, $angle, $fontFile, $text, $width);
		}
		// Get Y offset as it 0 Y is the lower-left corner of the character
		$testbox = imagettfbbox($fontSize, $angle, $fontFile, $text);
		$offsety = abs($testbox[7]);
		$offsetx = 0;
		$actualWidth = abs($testbox[6] - $testbox[4]);
		$actualHeight = abs($testbox[1] - $testbox[7]);
		// If text box align text
		if(is_int($width) || is_int($height)){
			if(!is_int($width)){
				$width = $actualWidth;
			}
			if(!is_int($height)){
				$height = $actualHeight;
			}
			if($debug){
				$this->rectangle($x, $y, $width, $height, array(0, 255, 255), 0.5);
			}
			switch($alignHorizontal){
				case 'center':
					$offsetx += (($width - $actualWidth) / 2);
				break;
				case 'right':
					$offsetx += ($width - $actualWidth);
				break;
			}
			switch($alignVertical){
				case 'center':
					$offsety += (($height - $actualHeight) / 2);
				break;
				case 'bottom':
					$offsety += ($height - $actualHeight);
				break;
			}
		}
		// Draw stroke
		if($strokeWidth > 0){
			$strokeColor = imagecolorallocatealpha($this->img, $strokeColor[0], $strokeColor[1], $strokeColor[2], (1 - $opacity) * 127);
			for($sx = ($x-abs($strokeWidth)); $sx <= ($x+abs($strokeWidth)); $sx++){
				for($sy = ($y-abs($strokeWidth)); $sy <= ($y+abs($strokeWidth)); $sy++){
					imagettftext($this->img, $fontSize, $angle, $sx + $offsetx, $sy + $offsety, $strokeColor, $fontFile, $text);
				}
			}
		}
		// Draw text
		imagettftext($this->img, $fontSize, $angle, $x + $offsetx, $y + $offsety, imagecolorallocatealpha($this->img, $fontColor[0], $fontColor[1], $fontColor[2], (1 - $opacity) * 127), $fontFile, $text);
		return $this;
	}

	/**
	* Reduce font size to fit to width
	*
	* @param integer $fontSize
	* @param integer $angle
	* @param String $fontFile
	* @param String $text
	* @param integer $width
	*/
	private function fitToWidth($fontSize, $angle, $fontFile, $text, $width){
		while($fontSize > 0){
			$testbox = imagettfbbox($fontSize, $angle, $fontFile, $text);
			$actualWidth = abs($testbox[6] - $testbox[4]);
			if($actualWidth <= $width){
				return $fontSize;
			}else{
				$fontSize--;
			}
		}
		return $fontSize;
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
	* Set's global folder mode if folder structure needs to be created
	*
	* @param String $mode
	* @return PHPImage
	*/
	public function setFolderMode($mode=0755){
		$this->folderMode = $mode;
    	return $this;
	}

	/**
	* Set's global text size
	*
	* @param integer $size
	* @return PHPImage
	*/
    public function setFontSize($size=12){
    	$this->fontSize = $size;
    	return $this;
    }

    /**
	* Set's global text vertical alignment
	*
	* @param String $align
	* @return PHPImage
	*/
    public function setAlignVertical($align='top'){
    	$this->alignVertical = $align;
    	return $this;
    }

    /**
	* Set's global text horizontal alignment
	*
	* @param String $align
	* @return PHPImage
	*/
    public function setAlignHorizontal($align='left'){
    	$this->alignHorizontal = $align;
    	return $this;
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
	
	/**
	* Set's global quality for PNG output
	*
	* @param string $quality
	* @return PHPImage
	*/
    public function setQuality($quality){
    	$this->quality = $quality;
    	return $this;
    }
}