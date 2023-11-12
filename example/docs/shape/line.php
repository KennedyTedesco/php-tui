<?php

use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Model\AnsiColor;
use PhpTui\Tui\Model\Marker;
use PhpTui\Tui\Widget\Canvas;
use PhpTui\Tui\Shape\Line;

require 'vendor/autoload.php';

$display = DisplayBuilder::default()->build();
$display->drawWidget(
    Canvas::fromIntBounds(0, 20, 0, 20)
        ->marker(Marker::Dot)
        ->draw(Line::fromScalars(
            0,  // x1
            0,  // y1
            20, // x2
            20, // y2
        )->color(AnsiColor::Green))
);
