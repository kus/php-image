<?php

require_once('../src/PHPImage.php');

$bg = './img/benji.jpg';
$overlay = './img/paw.png';

// Avatar instance
$avatar = new PHPImage();
$avatar->setDimensionsFromImage($overlay);
$avatar->draw($overlay);
$avatar->resize(100, 100, false, false);

// Main image
$image = new PHPImage();
$image->setDimensionsFromImage($bg);
$image->draw($bg);
$image->setFont('./font/arial.ttf');
$image->setTextColor(array(255, 255, 255));
$image->setStrokeWidth(1);
$image->setStrokeColor(array(0, 0, 0));
$image->text('<- Resized image from other PHP Image instance', array('fontSize' => 12, 'x' => 170, 'y' => 500));

// Draw Avatar on main image
$image->rectangle(50, 450, 100, 100, array(255, 255, 255), 0.25);
$image->draw($avatar, 50, 450);

$image->show();
