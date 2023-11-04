<?php

namespace PhpTui\Tui\Tests\Example;

use Generator;
use PHPUnit\Framework\TestCase;
use PhpTui\Term\AnsiParser;
use PhpTui\Term\Painter\HtmlStylePainter;
use RuntimeException;

class DocsTest extends TestCase
{
    const WIDTH = 20;
    const HEIGHT = 50;

    /**
     * @dataProvider provideExamples
     */
    public function testExamples(string $path): void
    {
        $spec = [
            1 => ['pipe', 'w'],
        ];
        $process = proc_open(
            command: [
                PHP_BINARY,
                $path,
            ],
            descriptor_spec: $spec,
            pipes: $pipes,
            cwd: __DIR__ . '/../../',
            env_vars: [
                'LINES' => self::WIDTH,
                'COLUMNS' => self::HEIGHT,
            ],
        );
        if (!is_resource($process)) {
            throw new RuntimeException(sprintf(
                'Could not spawn process'
            ));
        }
        $output = (string)stream_get_contents($pipes[1]);
        $exitCode = proc_close($process);
        self::assertEquals(0, $exitCode);

        $actions = AnsiParser::parseString($output, throw: false);

        $painter = HtmlStylePainter::default(self::HEIGHT, self::WIDTH);
        $painter->paint($actions);
        $output = $painter->toString();

        $snapshot = substr($path, 0, -3) . 'snapshot';
        if (!file_exists($snapshot) || getenv('SNAPSHOT_APPROVE')) {
            file_put_contents($snapshot, $output);
            return;
        }

        $existing = file_get_contents($snapshot);
        if (false === $existing) {
            throw new RuntimeException('Could not read file');
        }

        self::assertEquals($this->sanitize($output), $this->sanitize($existing));
        self::assertEquals($output, $existing);

    }

    /**
     * @return Generator<array{string}>
     */
    public static function provideExamples(): Generator
    {
        foreach ((array)glob(__DIR__ . '/../../example/docs/*/*.php') as $example) {
            if (false === $example) {
                continue;
            }
            yield dirname($example) . ' ' . basename($example) => [
                $example,
            ];
        }
    }

    private function sanitize(string $html): string
    {
        return trim(str_replace(
            '&nbsp;',
            ' ',
            strip_tags(
                str_replace('<div style="clear: both;"></div>', "\n", $html)
            )
        ));
    }
}
