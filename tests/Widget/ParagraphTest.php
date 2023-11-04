<?php

namespace PhpTui\Tui\Tests\Widget;

use PhpTui\Tui\Model\Area;
use PhpTui\Tui\Model\Buffer;
use PhpTui\Tui\Model\Widget\HorizontalAlignment;
use PhpTui\Tui\Model\Widget\Line;
use PhpTui\Tui\Model\Widget\Text;
use PhpTui\Tui\Widget\Paragraph;
use Generator;
use PHPUnit\Framework\TestCase;

class ParagraphTest extends TestCase
{

    public function testFromString(): void
    {
        $paragraph = Paragraph::fromString('Hello');
        self::assertEquals(Paragraph::fromText(Text::fromString('Hello')), $paragraph);
    }
    /**
     * @dataProvider provideParagraph
     */
    public function testParagraph(
        Area $area,
        Paragraph $paragraph,
        string $expected
    ): void {
        $buffer = Buffer::empty($area);
        $paragraph->render($buffer->area(), $buffer);
        self::assertEquals($expected, $buffer->toString());

    }
    /**
     * @return Generator<string,array{Area,Paragraph,string}>
     */
    public static function provideParagraph(): Generator
    {
        yield 'simple' => [
            Area::fromDimensions(8, 1),
            Paragraph::fromText(Text::fromString('Gday')),
            'Gday    ',
        ];
        yield 'wrap' => [
            Area::fromDimensions(8, 3),
            Paragraph::fromText(Text::fromString('Gday mate lets put another shrimp on the barby')),
            implode("\n", [
                'Gday mat',
                'e lets p',
                'ut anoth',
            ]),
        ];
        yield 'align right' => [
            Area::fromDimensions(8, 1),
            Paragraph::fromText(
                Text::fromLine(
                    Line::fromString('Gday')->alignment(HorizontalAlignment::Right)
                )
            ),
            '    Gday',
        ];
        yield 'align left and right' => [
            Area::fromDimensions(10, 1),
            Paragraph::fromLines(
                Line::fromString('1/1')->alignment(HorizontalAlignment::Left),
                Line::fromString('About')->alignment(HorizontalAlignment::Right),
            ),
            '1/1       ',
            '     About',
        ];
    }
}
