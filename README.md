# PHP Image

## What does it do?

Wrapper for PHP's GD Library for easy image manipulation to draw local or remote images on top of each other preserving transparency, writing text with stroke and transparency and drawing shapes.

## Installation

Place the PHP file on your server and include it in your script.

# Usage

## Example

```ruby
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
	$image->text('Hello World!', array('fontSize' => 12, 'x' => 50, 'y' => 50));
	$image->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', array('fontSize' => 8, 'x' => 50, 'y' => 70));
	$image->show();
```

## Chainable

```ruby
	$image = new PHPImage();
	$image->rectangle(40, 40, 100, 60, array(0, 0, 0), 0.5)->setFont('/path/to/fonts/Arial.ttf')->setTextColor(array(255, 255, 255))->setStrokeWidth(1)->setStrokeColor(array(0, 0, 0))->text('Hello World!', array('fontSize' => 12, 'x' => 50, 'y' => 50))->text('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', array('fontSize' => 8, 'x' => 50, 'y' => 70))->show();
```

## Text box with auto wrap

```ruby
	$image = new PHPImage(400, 400);
	$image->rectangle(0, 0, 100, 200, array(0, 0, 0), 0.5);
	$image->setFont('/path/to/fonts/Arial.ttf');
	$image->setTextColor(array(255, 255, 255));
	$image->setStrokeWidth(1);
	$image->setStrokeColor(array(0, 0, 0));
	$image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 100, 12, 0, 0);
	$image->show();
```

## Text box auto fit text (variable font size)

```ruby
	$image = new PHPImage(400, 400);
    $image->setFont('/path/to/fonts/Arial.ttf');
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