<?php

namespace PhpTui\Tui\Tests\Model;

use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Model\AnsiColor;
use PhpTui\Tui\Model\Backend\DummyBackend;
use PhpTui\Tui\Model\Buffer;
use PhpTui\Tui\Model\Marker;
use PhpTui\Tui\Model\Position;
use PHPUnit\Framework\TestCase;
use PhpTui\Tui\Widget\Canvas;
use PhpTui\Tui\Shape\Points;

class DisplayTest extends TestCase
{
    public function testAutoresize(): void
    {
        $backend = DummyBackend::fromDimensions(4, 4);
        $terminal = DisplayBuilder::default($backend)->build();
        $backend->setDimensions(2, 2);

        // intentionally go out of bounds
        $terminal->draw(function (Buffer $buffer): void {
            for ($y = 0; $y < 4; $y++) {
                for ($x = 0; $x < 4; $x++) {
                    $buffer->putString(new Position($x, $y), 'h');
                }
            }
        });
        self::assertEquals(<<<'EOT'
            hh  
            hh  
                
                
            EOT, $backend->toString());
    }

    public function testDraw(): void
    {
        $backend = DummyBackend::fromDimensions(4, 4);
        $terminal = DisplayBuilder::default($backend)->build();
        $terminal->draw(function (Buffer $buffer): void {
            $x = 0;
            for ($y = 0; $y <= 4; $y++) {
                $buffer->putString(new Position($x++, $y), 'x');
            }
        });
        self::assertEquals(
            <<<'EOT'
                x   
                 x  
                  x 
                   x
                EOT,
            $backend->flushed()
        );
    }

    public function testRender(): void
    {
        $backend = DummyBackend::fromDimensions(4, 4);
        $terminal = DisplayBuilder::default($backend)->build();
        $terminal->drawWidget(Canvas::fromIntBounds(0, 3, 0, 3)->marker(Marker::Dot)->draw(Points::new([
            [3, 3], [2, 2], [1, 1], [0, 0]
        ], AnsiColor::Green)));

        self::assertEquals(
            <<<'EOT'
                   •
                  • 
                 •  
                •   
                EOT,
            $backend->flushed()
        );
    }

    public function testFlushes(): void
    {
        $backend = DummyBackend::fromDimensions(10, 4);
        $terminal = DisplayBuilder::default($backend)->build();
        $terminal->buffer()->putString(new Position(2, 1), 'X');
        $terminal->buffer()->putString(new Position(0, 0), 'X');
        $terminal->flush();
        self::assertEquals(
            implode("\n", [
                'X         ',
                '  X       ',
                '          ',
                '          ',
            ]),
            $backend->toString()
        );
    }
}
