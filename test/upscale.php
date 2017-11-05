<?php

require_once('../src/PHPImage.php');

$image = new PHPImage('./img/benji.jpg');
$image->setFont('./font/arial.ttf');
$originalWidth = $image->getWidth();
$originalHeight = $image->getHeight();
$image->resize(ceil($originalWidth * 1.5), ceil($originalHeight * 1.5), /*crop*/ true, /* upscale*/ true);
$image->setTextColor(array(255, 255, 255));
$image->setStrokeWidth(1);
$image->textBox("Old dimensions: " . $originalWidth . "x" . $originalHeight . "\nNew dimensions: " . $image->getWidth() . "x" . $image->getHeight(), array(
	'width' => $originalWidth,
	'height' => $originalHeight,
	'fontSize' => 16,
	'x' => 10,
	'y' => 10
));
$image->show();
