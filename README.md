# PHP Image

## What does it do?

Wrapper for PHP's GD Library for easy image manipulation to resize, crop and draw images on top of each other preserving transparency, writing text with stroke and transparency and drawing shapes.

## Installation

Place the PHP file on your server and include it in your script.

# Usage

## Example

```ruby
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
	$image->show();
```

![Overlay and text](https://raw.github.com/kus/php-image/master/test/examples/overlay.jpg "Overlay and text")

## Chainable

```ruby
	(new PHPImage('./img/benji.jpg'))->resize(200, 200, 'C', true)->show();
```

## Batch resize with optional crop and upscale

```ruby
	$image = new PHPImage('./img/benji.jpg');
	$image->batchResize('examples/thumb_%dx%d.jpg', array(
		array(400, 400, true, true),
		array(200, 400, true, true),
		array(400, 200, true, true),
		array(100, 100, true, true),
	));
	$image->resize(100, 100, true, true)->show();
```

![400x400](https://raw.github.com/kus/php-image/master/test/examples/thumb_400x400.jpg "400x400")
![400x200](https://raw.github.com/kus/php-image/master/test/examples/thumb_400x200.jpg "400x200")
![200x400](https://raw.github.com/kus/php-image/master/test/examples/thumb_200x400.jpg "200x400")
![100x100](https://raw.github.com/kus/php-image/master/test/examples/thumb_100x100.jpg "100x100")

## Text box with auto wrap

```ruby
	$image = new PHPImage(400, 400);
	$image->rectangle(0, 0, 100, 200, array(0, 0, 0), 0.5);
	$image->setFont('./font/arial.ttf');
	$image->setTextColor(array(255, 255, 255));
	$image->setStrokeWidth(1);
	$image->setStrokeColor(array(0, 0, 0));
	$image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 100, 12, 0, 0);
	$image->show();
```

## Text box auto fit text (variable font size)

```ruby
	$image = new PHPImage(400, 400);
    $image->setFont('./font/arial.ttf');
    $image->setTextColor(array(255, 255, 255));
    $image->text('This is a big sentence', array(
		'fontSize' => 60,
		'x' => 0,
		'y' => 0,
		'width' => 400,
		'height' => 200,
		'alignHorizontal' => 'center',
		'alignVertical' => 'center',
		'debug' => true
	));
	$image->text('BIG', array(
		'fontSize' => 120,
		'x' => 0,
		'y' => 200,
		'width' => 400,
		'height' => 200,
		'alignHorizontal' => 'center',
		'alignVertical' => 'center',
		'debug' => true
	));
    $image->show();
```

## Copyright

Copyright (c) 2013 Blake Kus [blakek.us](http://blakek.us)

This plugin is dual licenced under MIT and GPL Version 2 licences. 

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.