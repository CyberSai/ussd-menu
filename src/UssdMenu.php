<?php

namespace Cybersai\UssdMenu;

class UssdMenu
{
    const NUMBERING_ALPHABETIC_LOWER = 'alphabetic_lower';
    const NUMBERING_ALPHABETIC_UPPER = 'alphabetic_lower';
    const NUMBERING_EMPTY = 'empty';
    const NUMBERING_NUMERIC = 'numeric';

    const ITEMS_SEPARATOR_NO_LINE_BREAK = "";
    const ITEMS_SEPARATOR_LINE_BREAK = "\n";
    const ITEMS_SEPARATOR_DOUBLE_LINE_BREAK = "\n\n";

    const NUMBERING_SEPARATOR_NO_SPACE = "";
    const NUMBERING_SEPARATOR_SPACE = " ";
    const NUMBERING_SEPARATOR_DOUBLE_SPACE = "  ";
    const NUMBERING_SEPARATOR_DOT = ".";
    const NUMBERING_SEPARATOR_DOT_PLUS_SPACE = ". ";
    const NUMBERING_SEPARATOR_DOT_PLUS_DOUBLE_SPACE = ".  ";
    const NUMBERING_SEPARATOR_BRACKET = ")";
    const NUMBERING_SEPARATOR_BRACKET_PLUS_SPACE = ") ";
    const NUMBERING_SEPARATOR_BRACKET_PLUS_DOUBLE_SPACE = ")  ";

    protected $menu;

    private function __construct($menu = '')
    {
        $this->menu = $menu;
    }

    private function numberingFor($index, $numbering)
    {
        if ($numbering == self::NUMBERING_ALPHABETIC_LOWER) {
            return range('a','z')[$index];
        } else if ($numbering == self::NUMBERING_ALPHABETIC_UPPER) {
            return range('A','Z')[$index];
        } else if ($numbering == self::NUMBERING_NUMERIC) {
            return $index + 1;
        } else {
            return '';
        }
    }

    private function isLastPage($page, $numberPerPage, $items)
    {
        return $page * $numberPerPage >= count($items);
    }


    private function pageStartIndex($page, $numberPerPage)
    {
        return $page * $numberPerPage - $numberPerPage;
    }

    private function pageLimit($page, $numberPerPage, $items)
    {
        return $this->isLastPage($page, $numberPerPage, $items) ?
            count($items) - $this->pageStartIndex($page, $numberPerPage) : $numberPerPage;
    }

    private function listParser($items, $page, $numberPerPage, $numberingSeparator, $itemsSeparator, $numbering)
    {
        $startIndex = $this->pageStartIndex($page, $numberPerPage);
        $limit = $this->pageLimit($page, $numberPerPage, $items);
        for ($i = 0;$i < $limit;$i++)
        {
            $this->menu .= "{$this->numberingFor($i + $startIndex, $numbering)}{$numberingSeparator}{$items[$i + $startIndex]}";
            if ($i != $limit - 1) {
                $this->menu .= $itemsSeparator;
            }
        }
    }

    public static function instance()
    {
        return new self();
    }

    public static function from($text)
    {
        return new self($text);
    }

    public function lineBreak($number = 1)
    {
        $this->menu .= str_repeat("\n", $number);
        return $this;
    }

    public function line($text)
    {
        $this->menu .= "$text\n";
        return $this;
    }

    public function text($text)
    {
        $this->menu .= $text;
        return $this;
    }

    public function listing($items, $numberingSeparator = self::NUMBERING_SEPARATOR_DOT,
                            $itemsSeparator = self::ITEMS_SEPARATOR_LINE_BREAK,
                            $numbering = self::NUMBERING_NUMERIC)
    {
        $this->listParser($items, $page = 1, count($items), $numberingSeparator, $itemsSeparator,
            $numbering);
        return $this;
    }

    public function paginateListing($items, $page = 1, $numberPerPage = 5,
                                    $numberingSeparator = self::NUMBERING_SEPARATOR_DOT,
                                    $itemsSeparator = self::ITEMS_SEPARATOR_LINE_BREAK,
                                    $numbering = self::NUMBERING_NUMERIC)
    {
        $this->listParser($items, $page, $numberPerPage, $numberingSeparator, $itemsSeparator,
            $numbering);
        return $this;
    }

    public function toString()
    {
        return $this->menu;
    }

    public function __toString()
    {
        return $this->menu;
    }
}
