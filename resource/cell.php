<?php

class Cell {

    public $x;
    public $y;
    public $status;

    public function __construct($x = 0, $y = 0, $status = 0) {
        $this->x = $x;
        $this->y = $y;
        $this->status = $status;
    }

    public function setCell($cell) {
        if (!isset($cell->x) || !isset($cell->y) || !isset($cell->status)) {
            return false;
        }
        $this->x = $cell->x;
        $this->y = $cell->y;
        $this->status = $cell->status;
        return true;
    }

    public function isEqualsPosition($cell) {
        if ($this->x != $cell->x || $this->y != $cell->y) {
            return false;
        }

        return true;
    }

    public function checkMove($dir, $sizemap) {
        
        switch ($dir) {
            case 'T':
            case 't':
                if ($this->x > 0) {

                    return TRUE;
                }
                break;
            case 'R':
            case 'r':
                if ($this->y < $sizemap - 1) {

                    return TRUE;
                }
                break;
            case 'B':
            case 'b':
                if ($this->x < $sizemap - 1) {

                    return TRUE;
                }
                break;
            case 'L':
            case 'l':
                if ($this->y > 0) {

                    return TRUE;
                }
                break;
        }

        return FALSE;
    }

}
