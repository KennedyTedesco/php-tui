<?php

namespace DTL\PhpTui\Model;

final class Cell
{
    public function __construct(
        public string $char,
        public Color $fg,
        public Color $bg,
        public Color $underline,
        public Modifiers $modifier
    ) {
    }

    public static function empty(): self
    {
        return new self(' ', AnsiColor::Reset, AnsiColor::Reset, AnsiColor::Reset, Modifiers::none());
    }

    public static function fromChar(string $char): self
    {
        return new self($char, AnsiColor::Reset, AnsiColor::Reset, AnsiColor::Reset, Modifiers::none());
    }

    public function setChar(string $char): self
    {
        $this->char = $char;
        return $this;
    }

    public function setStyle(Style $style): self
    {
        if ($style->fg) {
            $this->fg = $style->fg;
        }
        if ($style->bg) {
            $this->bg = $style->bg;
        }
        if ($style->underline) {
            $this->underline = $style->underline;
        }
        foreach ($style->addModifiers as $modifier) {
            $this->modifier->add($modifier);
        }
        foreach ($style->subModifiers as $modifier) {
            $this->modifier->sub($modifier);
        }
        return $this;
    }

    public function reset(): void
    {
        $this->setChar(' ');
        $this->setStyle(Style::default());
    }
}
