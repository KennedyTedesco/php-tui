<?php

declare(strict_types=1);

namespace PhpTui\Tui\Tests\Unit\Extension\Core\Shape;

use Generator;
use PhpTui\Tui\Extension\Core\Shape\Map;
use PhpTui\Tui\Extension\Core\Shape\MapResolution;
use PhpTui\Tui\Extension\Core\Widget\Canvas;
use PhpTui\Tui\Model\Area;
use PhpTui\Tui\Model\AxisBounds;
use PhpTui\Tui\Model\Buffer;
use PhpTui\Tui\Model\Canvas\CanvasContext;
use PhpTui\Tui\Model\Marker;

class MapTest extends ShapeTestCase
{
    /**
     * @param array<int,string> $expected
     * @dataProvider provideMap
     */
    public function testMap(MapResolution $resolution, Marker $marker, array $expected): void
    {
        $canvas = Canvas::default()
            ->marker($marker)
            ->xBounds(AxisBounds::new(-180, 180))
            ->yBounds(AxisBounds::new(-90, 90))
            ->paint(function (CanvasContext $context) use ($resolution): void {
                $context->draw(Map::default()->resolution($resolution));
            });
        $area = Area::fromDimensions(80, 40);
        $buffer = Buffer::empty($area);
        $this->render($buffer, $canvas);
        self::assertEquals($expected, $buffer->toLines());
    }
    /**
     * @return Generator<string,array{MapResolution,Marker,array<int,string>}>
     */
    public static function provideMap(): Generator
    {
        yield 'low' => [
            MapResolution::Low,
            Marker::Dot,
            [
            '                                                                                ',
            '                   ••••••• •• •• •• •                                           ',
            '            ••••••••••••••       •••      ••••  •••  ••    ••••                 ',
            '            ••••••••••••••••     ••                ••• ••••••• •• •• •••        ',
            '• • •• •••••• •••••••••••• ••   •••  •    •••••  •••••••••          ••  • • • • ',
            '•••••       ••••  •••••••• •• ••  •••    •••• ••••    •• •                    • ',
            '   ••••••••  ••••••• •••••  •••       ••••••••                        • •••••   ',
            '  •• ••   ••    •••••••  ••          ••• ••••                        ••    •    ',
            '•••       •••    •••••• ••••         ••••                             •• •   •• ',
            '            •      •••••••••          ••  •   ••• • •• ••            ••         ',
            '            • •     ••••             •• ••••••••• •••   •         • • ••        ',
            '            •         •               ••••• ••••  ••             ••••••         ',
            '             •      ••               •   • •• •                  •••••          ',
            '              ••  •• •              •         ••  ••              •             ',
            '    ••        •••   •••            •           •  •••••    •   •••              ',
            '     •           •••• •••                       •   •  •    •  • ••             ',
            '                  •••• •           •            •• •     •  ••   ••             ',
            '                     ••• ••         •           • •     ••   ••• •••            ',
            '                      •    •        • •• •              •   •   •  •            ',
            '                   •  •     •            •    • •            ••• •  •           ',
            '                     •        •           •   •              •• •   • •         ',
            '                               •                •              ••   ••• •       ',
            ' •                    •       •           •     • •                • •          ',
            '                        •                 •    • ••               •  • •   •  • ',
            '                              •                •                •       •       ',
            '                       •    •                 •  •              •        •      ',
            '                       •   ••              • •                  • • ••       •  ',
            '                       •  •                •                         ••••    •• ',
            '                       • •                                             ••   ••• ',
            '                       ••                                                   •   ',
            '                       •• •                                                     ',
            '                       ••                                                       ',
            '                                                                                ',
            '                        •••                        •      •••• • • •• •         ',
            '                       ••••           •••••• •••••• ••••••             • •••    ',
            '         •• •••••• ••••• ••      • ••• •                                   ••   ',
            '•  •••••             ••  •• ••••••                                         • •• ',
            '•    •                 •   •  •                                             • • ',
            '       •                                                                        ',
            '                                                                                ',
            ]
        ];
        yield 'high' => [
            MapResolution::High,
            Marker::Braille,
            [
            '                                                                                ',
            '                  ⢀⣠⠤⠤⠤⠔⢤⣤⡄⠤⡠⣄⠢⠂⢢⠰⣠⡄⣀⡀                      ⣀                   ',
            '            ⢀⣀⡤⣦⠲⢶⣿⣮⣿⡉⣰⢶⢏⡂        ⢀⣟⠁     ⢺⣻⢿⠏   ⠈⠉⠁ ⢀⣀    ⠈⠓⢳⣢⣂⡀               ',
            '            ⡞⣳⣿⣻⡧⣷⣿⣿⢿⢿⣧⡀⠉⠉⠙⢆      ⣰⠇               ⣠⠞⠃⢉⣄⣀⣠⠴⠊⠉⠁ ⠐⠾⠤⢤⠤⡄⠐⣻⠜⢓⠂      ',
            '⢍ ⢀⡴⠊⠙⠓⠒⠒⠤⠖⠺⠿⠽⣷⣬⢬⣾⣷⢻⣷⢲⢲⣍⠱⡀ ⠹⡗   ⢀⢐⠟        ⡔⠒⠉⠲⠤⢀⢄⡀⢩⣣⠦⢷⢼⡏⠈          ⠉⠉⠉ ⠈⠈⠉⠖⠤⠆⠒⠭',
            '⠶⢽⡲⣽⡆             ⠈⣠⣽⣯⡼⢯⣘⡯⠃⠘⡆ ⢰⠒⠁ ⢾⣚⠟    ⢀⠆ ⣔⠆ ⢷⠾⠋⠁    ⠙⠁                     ⠠⡤',
            '  ⠠⢧⣄⣀⡶⠦⠤⡀        ⢰⡁ ⠉⡻⠙⣎⡥  ⠘⠲⠇       ⢀⡀⠨⣁⡄⣸⢫⡤⠄                        ⣀⢠⣤⠊⣼⠅⠖⠋⠁',
            '   ⣠⠾⠛⠁  ⠈⣱        ⠋⠦⢤⡼ ⠈⠈⠦⡀         ⢀⣿⣇ ⢹⣷⣂⡞⠃                       ⢀⣂⡀  ⠏⣜    ',
            '          ⠙⣷⡄        ⠘⠆ ⢀⣀⡠⣗         ⠘⣻⣽⡟⠉⠈                           ⢹⡇  ⠟⠁    ',
            '           ⠈⡟           ⢎⣻⡿⠾⠇         ⠘⠇  ⣀⡀  ⣤⣤⡆ ⡠⡦                 ⢀⠎⡏        ',
            '            ⡇          ⣀⠏⠋           ⢸⠒⢃⡖⢻⢟⣷⣄⣰⣡⠥⣱ ⢏⣧              ⣀ ⡴⠚⢰⠟        ',
            '            ⢳         ⢸⠃             ⠸⣄⣼⣠⢼⡴⡟⢿⢿⣀⣄  ⠸⡹             ⠘⡯⢿⡇⡠⢼⠁        ',
            '             ⢳⣀      ⢀⠞⠁             ⢠⠋⠁ ⠐⠧⡄⣬⣉⣈⡽                  ⢧⠘⢽⠟⠉         ',
            '              ⣿⣄  ⡴⠚⠛⣿⣀             ⢠⠖     ⠈⠁ ⠹⣧  ⢾⣄⡀             ⡼ ⠈           ',
            '    ⣀         ⠘⣿⡄ ⡇  ⣘⣻             ⡏          ⢻⡄ ⠘⠿⢿⠒⠲⡀   ⢀⡀   ⢀⡰⣗             ',
            '    ⠉⠷          ⢫⡀⢧⡼⡟⠉⣛⣳⣦⡀         ⠈⡇          ⠸⣱  ⢀⡼  ⢺  ⡸⠉⢇  ⣾⡏ ⣁             ',
            '                 ⠉⠒⢆⡓⡆             ⠠⡃           ⢳⣇⡠⠏   ⠐⡄⡞  ⠘⣇⡀⢱  ⣾⡀            ',
            '                    ⢹⣇⣀⣾⡷⠤⡆         ⢣            ⠯⢺⠇    ⢣⣅   ⣽⢱⡔ ⢠⢿⣗            ',
            '                     ⠙⢱   ⠘⠦⡄       ⠈⢦⡠⣠⢶⣀        ⡜     ⠈⠿  ⢠⣽⢆ ⢀⣼⡜⠿            ',
            '                     ⢀⡞     ⢱⡀           ⢸       ⡔⠁          ⢻⢿⢰⠏⢸⣤⣴⣆           ',
            '                     ⢘⠆      ⠙⠢⢄         ⠸⡀     ⡸⠁           ⠈⣞⡎⠥⡟⣿⠠⠿⣷⠒⢤⢀⣆      ',
            '                     ⠘⠆        ⢈⠂         ⢳     ⡇             ⠈⠳⠶⣤⣭⣠ ⠋⢧⡬⣟⠉⠷⡄    ',
            '                      ⢨        ⡜          ⢸     ⠸ ⣠               ⠁⢁⣰⢶ ⡇⠉⠁ ⠛    ',
            '⠆                     ⠈⢱⡀      ⡆          ⡇    ⢀⡜⡴⢹               ⢰⠏⠁⠘⢶⠹⡀   ⠸ ⢠⡶',
            '                        ⠅     ⣸           ⢸    ⢫ ⡞⡊             ⢠⠔⠋     ⢳⡀ ⠐⣦   ',
            '                        ⡅    ⡏            ⠈⡆  ⢠⠎ ⠳⠃             ⢸        ⢳      ',
            '                       ⠨    ⡸⠁             ⢱  ⡸                 ⠈⡇ ⢀⣀⡀   ⢸      ',
            '                       ⠸  ⠐⡶⠁              ⠘⠖⠚                   ⠣⠒⠋ ⠱⣇ ⢀⠇   ⠰⡄ ',
            '                       ⠽ ⣰⡖⠁                                          ⠘⢚⡊    ⢀⣿⠇',
            '                       ⡯⢀⡟                                             ⠘⠏   ⢠⢾⠃ ',
            '                       ⠇⢨⠆                            ⢠⡄                    ⠈⠁  ',
            '                       ⢧⣷⡀⠚                                                     ',
            '                        ⠉⠁                                                      ',
            '                          ⢀⡀                                                    ',
            '                        ⢠⡾⠋                      ⣀⡠⠖⢦⣀⣀  ⣀⠤⠦⢤⠤⠶⠤⠖⠦⠤⠤⠤⠴⠤⢤⣄       ',
            '                ⢀⣤⣀ ⡀  ⣼⣻⠙⡆         ⢀⡤⠤⠤⠴⠒⠖⠒⠒⠒⠚⠉⠋⠁    ⢰⡳⠊⠁              ⠈⠉⠉⠒⠤⣤  ',
            '    ⢀⣀⣀⡴⠖⠒⠒⠚⠛⠛⠛⠒⠚⠳⠉⠉⠉⠉⢉⣉⡥⠔⠃     ⢀⣠⠤⠴⠃                                      ⢠⠞⠁  ',
            '   ⠘⠛⣓⣒⠆              ⠸⠥⣀⣤⡦⠠⣞⣭⣇⣘⠿⠆                                         ⣖⠛   ',
            '⠶⠔⠲⠤⠠⠜⢗⠤⠄                 ⠘⠉  ⠁                                            ⠈⠉⠒⠔⠤',
            '                                                                                ',
            ]
        ];
    }
}