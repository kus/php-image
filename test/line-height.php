<?php

require_once('../src/PHPImage.php');

$bg = './img/benji.jpg';
$image = new PHPImage();
$image->setDimensionsFromImage($bg);
$image->draw($bg);
$image->setFont('./font/arial.ttf');
$image->setTextColor(array(255, 255, 255));
$image->rectangle(40, 40, 120, 120, array(0, 0, 0), 0.5);
$image->textBox('Ваша фобия на сегодня астрофобия', array(
    'width' => 400,
    'fontSize' => 48,
    'x' => 50,
    'y' => 50
));

$image->snapshot('./examples/line-height.jpg');
