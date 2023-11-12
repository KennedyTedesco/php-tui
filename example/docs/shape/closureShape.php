<?php

use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Model\Marker;
use PhpTui\Tui\Model\Widget\Line;
use PhpTui\Tui\Widget\Canvas;
use PhpTui\Tui\Model\Canvas\Painter;
use PhpTui\Tui\Shape\ClosureShape;

require 'vendor/autoload.php';

$display = DisplayBuilder::default()->build();
$display->drawWidget(
    Canvas::fromIntBounds(-1, 21, -1, 21)
        ->marker(Marker::Dot)
        ->draw(
            new ClosureShape(
                function (Painter $painter): void {
                    $painter->context->print(
                        0,
                        0,
                        Line::fromString('Hello World')
                    );
                }
            )
        )
);
