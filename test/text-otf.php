<?php

require_once('../src/PHPImage.php');

$bg = './img/benji.jpg';
$image = new PHPImage();
$image->setDimensionsFromImage($bg);
$image->draw($bg);
$image->setFont('./font/Rubik-Regular.otf');
$image->setTextColor(array(255, 255, 255));
$image->setStrokeWidth(1);
$image->setStrokeColor(array(0, 0, 0));
$image->rectangle(40, 40, 120, 120, array(0, 0, 0), 0.5);
$image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', array(
	'width' => 100,
	'height' => 100,
	'fontSize' => 16,
	'x' => 50,
	'y' => 50
));
$image->rectangle(40, 190, 120, 120, array(0, 0, 0), 0.5);
$image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie tortor quam, at congue nibh imperdiet dapibus.', array(
	'width' => 100,
	'height' => 100,
	'fontSize' => 16,
	'x' => 50,
	'y' => 200
));
$image->rectangle(40, 340, 120, 120, array(0, 0, 0), 0.5);
$image->textBox('Vestibulum venenatis risus scelerisque enim faucibus, ac pretium massa condimentum. Curabitur faucibus mi at convallis viverra. Integer nec finibus ligula, id hendrerit felis.', array(
	'width' => 100,
	'height' => 100,
	'fontSize' => 16,
	'x' => 50,
	'y' => 350
));
$image->rectangle(190, 40, 320, 200, array(255, 255, 255), 0.5);
$image->setTextColor(array(0, 0, 0));
$image->setStrokeWidth(0);
$image->textBox("Auto wrap with auto font scale based on width and height of bounding box:\n\n\$image->textBox('MULTILINE TEXT', array(\n    'width' => 100,\n    'height' => 100,\n    'fontSize' => 16, // Desired starting size\n    'x' => 50,\n    'y' => 50\n));", array(
	'width' => 300,
	'height' => 180,
	'fontSize' => 10,
	'x' => 200,
	'y' => 50
));
$image->show();
