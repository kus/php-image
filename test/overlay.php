<?php

require_once('../src/PHPImage.php');

$bg = './img/benji.jpg';
$overlay = './img/paw.png';
$image = new PHPImage();
$image->setDimensionsFromImage($bg);
$image->draw($bg);
$image->draw($overlay, '50%', '75%');
$image->rectangle(40, 40, 120, 80, array(0, 0, 0), 0.5);
$image->setFont('./font/arial.ttf');
$image->setTextColor(array(255, 255, 255));
$image->setStrokeWidth(1);
$image->setStrokeColor(array(0, 0, 0));
$image->text('Hello World!', array('fontSize' => 12, 'x' => 50, 'y' => 50));
$image->text('This is a big sentence with width 200px', array(
	'fontSize' => 60,
	'x' => 300,
	'y' => 0,
	'width' => 200,
	'height' => 50,
	'alignHorizontal' => 'center',
	'alignVertical' => 'center',
	'debug' => true
));
$image->text('This is a big sentence', array(
	'fontSize' => 60,
	'x' => 300,
	'y' => 200,
	'width' => 200,
	'height' => 50,
	'alignHorizontal' => 'center',
	'alignVertical' => 'center',
	'debug' => true
));
$image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', array('width' => 100, 'fontSize' => 8, 'x' => 50, 'y' => 70));
$image->rectangle(40, 140, 170, 160, array(0, 0, 0), 0.5);
$image->textBox('Auto wrap and scale font size to multiline text box width and height bounds. Vestibulum venenatis risus scelerisque enim faucibus, ac pretium massa condimentum. Curabitur faucibus mi at convallis viverra. Integer nec finibus ligula, id hendrerit felis.', array(
	'width' => 150,
	'height' => 140,
	'fontSize' => 16,
	'x' => 50,
	'y' => 150
));
$image->show();