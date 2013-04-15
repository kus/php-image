PHP Image
====

Wrapper for PHP's GD Library for easy image manipulation to draw images on top of each other preserving transparency, writing text with stroke and transparency and drawing shapes.

Example
----

$overlay = '/path/to/images/overlay.png';
$image = new PHPImage();
$image->setDimensionsFromImage($overlay);
$image->draw('/path/to/images/image.jpg');
$image->draw($overlay);
$image->rectangle(40, 40, 100, 60, array(0, 0, 0), 0.5);
$image->setFont('/path/to/fonts/Arial.ttf');
$image->setTextColor(array(255, 255, 255));
$image->setStrokeWidth(1);
$image->setStrokeColor(array(0, 0, 0));
$image->text('Hello World!', 12, 50, 50);
$image->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 8, 50, 70);
$image->show();
  
Chainable
----

$image = new PHPImage();
$image->rectangle(40, 40, 100, 60, array(0, 0, 0), 0.5)->setFont('/path/to/fonts/Arial.ttf')->setTextColor(array(255, 255, 255))->setStrokeWidth(1)->setStrokeColor(array(0, 0, 0))->text('Hello World!', 12, 50, 50)->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 8, 50, 70)->show();