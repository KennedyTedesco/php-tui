<?php

declare(strict_types=1);

namespace PhpTui\Term\Painter;

use PhpTui\Term\Action;
use PhpTui\Term\Action\MoveCursor;
use PhpTui\Term\Action\PrintString;
use PhpTui\Term\Action\Reset;
use PhpTui\Term\Action\SetRgbBackgroundColor;
use PhpTui\Term\Action\SetRgbForegroundColor;
use PhpTui\Term\Painter;
use RuntimeException;

class HtmlCanvasPainter implements Painter
{
    /**
     * @var list<string>
     */
    private array $chars = [];

    /**
     * @var list<array{bg:?SetRgbBackgroundColor,fg:?SetRgbForegroundColor}>
     */
    private array $attributes = [];

    private int $cursorX = 0;

    private int $cursorY = 0;

    /**
     * @var int<1,max>
     */
    private int $width;

    private ?SetRgbBackgroundColor $bgColor = null;

    private ?SetRgbForegroundColor $fgColor = null;

    private SetRgbBackgroundColor $defaultBgColor;

    private SetRgbForegroundColor $defaultFgColor;

    private function __construct(
        int $width,
        int $height,
    ) {
        if ($width < 1 || $height < 1) {
            throw new RuntimeException(sprintf(
                'Width or height cannot be less than 1, got width: %d, height: %d',
                $width,
                $height
            ));
        }
        $this->chars = array_fill(0, $width * $height, ' ');
        $this->attributes = array_fill(0, $width * $height, [
            'fg' => null,
            'bg' => null,
        ]);
        $this->width = $width;
        $this->defaultBgColor = new SetRgbBackgroundColor(10, 10, 10);
        $this->defaultFgColor = new SetRgbForegroundColor(255, 255, 255);
    }

    public static function default(int $width, int $height): self
    {
        return new self(
            $width,
            $height,
        );
    }

    public function paint(array $actions): void
    {
        foreach ($actions as $action) {
            if ($action instanceof PrintString) {
                $this->printString($action);

                continue;
            }
            if ($action instanceof MoveCursor) {
                $this->cursorX = $action->col - 1;
                $this->cursorY = $action->line - 1;

                continue;
            }
            if ($action instanceof SetRgbBackgroundColor) {
                $this->bgColor = $action;

                continue;
            }
            if ($action instanceof SetRgbForegroundColor) {
                $this->fgColor = $action;

                continue;
            }
            if ($action instanceof Reset) {
                $this->fgColor = null;
                $this->bgColor = null;

                continue;
            }

            throw new RuntimeException(sprintf(
                'Do not know how to handle action: %s',
                $action::class
            ));

        }

    }

    public function toString(): string
    {
        $x = 0;
        $y = 0;
        $charChunks = array_chunk($this->chars, $this->width);
        $attrChunks = array_chunk($this->attributes, $this->width);
        $canvasId = sprintf('term_%s', uniqid());
        $scale = 8;

        $width = $this->width * $scale;
        $height = ceil(count($this->chars) / $this->width) * $scale;
        $html = [
            sprintf(
                '<canvas id="%s" width=%d height=%d></canvas>',
                $canvasId,
                $width,
                $height
            )
        ];
        $html[] = '<script>';
        $html[] = sprintf('const canvas = document.getElementById("%s");', $canvasId);
        $html[] = 'const ctx = canvas.getContext("2d");';
        $html[] = 'ctx.font = "12px monospace";';
        $html[] = sprintf('ctx.fillStyle = "%s";', $this->toHtmlRgb($this->defaultBgColor));
        $html[] = sprintf(
            'ctx.fillRect(0,0,%d,%d);',
            $width,
            $height,
        );

        foreach ($this->chars as $offset => $char) {
            if ($char === ' ') {
                continue;
            }
            $attr = $this->attributes[$offset];
            $x = $offset % $this->width;
            $y = floor($offset / $this->width);
            $html[] = sprintf('ctx.fillStyle = "%s";', $this->toHtmlRgb($attr['bg'] ?? $this->defaultBgColor));
            $html[] = sprintf(
                'ctx.fillRect(%d,%d,%d,%d);',
                $x * $scale,
                $y * $scale,
                $scale,
                $scale
            );
            $html[] = sprintf('ctx.fillStyle = "%s";', $this->toHtmlRgb($attr['fg'] ?? $this->defaultFgColor));

            $html[] = sprintf(
                'ctx.fillText("%s",%d,%d);',
                $char,
                $x * $scale,
                $y * $scale + $scale,
            );
        }
        $html[] = '</script>';

        return implode("\n", $html);
    }

    private function printString(PrintString $action): void
    {
        foreach (mb_str_split($action->string) as $char) {
            $this->paintChar($this->cursorX, $this->cursorY, $char);
            $this->cursorX++;
        }
    }

    private function paintChar(int $x, int $y, string $char): void
    {
        $offset = ($y * $this->width + 1) + $x - 1;
        $this->chars[$offset] = $char;
        if ($this->bgColor) {
            $this->attributes[$offset]['bg'] = $this->bgColor;
        }
        if ($this->fgColor) {
            $this->attributes[$offset]['fg'] = $this->fgColor;
        }
    }

    private function toHtmlRgb(Action $action): string
    {
        if (
            $action instanceof SetRgbBackgroundColor ||
            $action instanceof SetRgbForegroundColor
        ) {
            return sprintf('rgb(%d,%d,%d)', $action->r, $action->g, $action->b);
        }

        throw new RuntimeException(sprintf(
            'Do not know how to convert action %s to color',
            $action::class
        ));
    }
}