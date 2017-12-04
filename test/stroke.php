<?php

require_once('../src/PHPImage.php');

$image = new PHPImage(400, 400);
$image->rectangle(0, 0, 100, 200, array(0, 0, 0), 0.5);
$image->setFont('./font/arial.ttf');
$image->setTextColor(array(255, 255, 255));

// $image->setStrokeWidth(1);
// $image->setStrokeColor(array(255, 0, 0));
// $image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie tortor quam, at congue nibh imperdiet dapibus.', array(
// 	'width' => 150,
// 	'height' => 140,
// 	'fontSize' => 16, // Desired starting font size
// 	'x' => 50,
// 	'y' => 150
// ));

$image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie tortor quam, at congue nibh imperdiet dapibus.', array(
	'width' => 150,
	'height' => 140,
	'fontSize' => 16, // Desired starting font size
	'x' => 50,
	'y' => 150,
	'strokeWidth' => 1,
	'strokeColor' => array(255, 0, 0)
));

$image->show();
