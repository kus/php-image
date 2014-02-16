<?php

require_once('../src/PHPImage.php');

$image = new PHPImage('./img/benji.jpg');
$image->batchResize('examples/thumb_%dx%d.jpg', array(
	array(400, 400, true, true),
	array(200, 400, true, true),
	array(400, 200, true, true),
	array(100, 100, true, true),
));
$image->resize(100, 100, true, true)->show();