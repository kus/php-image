<?php

require_once('../src/PHPImage.php');

$image = new PHPImage('./img/benji.jpg');
$image->setFont('./font/arial.ttf');
$one = clone $image;
$two = clone $image;
$one->resize(200, 100, true, true)->text('one', array('fontColor' => array(0, 255, 0)))->save('./examples/one.jpg');
$two->resize(100, 200, true, true)->text('two', array('fontColor' => array(255, 0, 0)))->save('./examples/two.jpg');
$three = clone $two;
// We should see Two and Three written
$three->resize(80, 160, true, true)->text('three', array('fontColor' => array(0, 0, 255)))->save('./examples/three.jpg');

// Example using raw GD command
$rotated = imagerotate($image->getResource(), 90, 0);
// Rotate returns a new image resource, so set the new one to the active one
$image->setResource($rotated);

$image->show();