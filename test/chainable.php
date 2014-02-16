<?php

require_once('../src/PHPImage.php');

(new PHPImage('./img/benji.jpg'))->resize(200, 200, 'C', true)->show();