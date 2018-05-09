<?php

require 'cell.php';

class Ship {

    public $n;
    public $config = [];

    public function __construct($n) {
        $this->n = $n;
        for ($i = 0; $i < $n; $i++) {
            $this->config[$i] = new Cell();
        }
    }

    public function setConfig($config = []) {
        if (count($config) == $this->n) {
            for ($i = 0; $i < $this->n; $i++) {
                if (!$this->config[$i]->setCell($config[$i])) {
                    return FALSE;
                }
            }
            return TRUE;
        }

        return FALSE;
    }

    public function setShip(Ship $ship) {
        $this->n = $ship->n;

        if (!$this->setConfig($ship->config)) {
            return FALSE;
        }
        return TRUE;
    }

    public function brokenAll() {
        return ($this->getNumBroken() == $this->n);
    }

    public function getNumBroken() {
        $num = 0;
        for ($i = 0; $i < $this->n; $i++) {
            if ($this->config[$i]->status != 0) {
                $num ++;
            }
        }

        return $num;
    }

    public function validConfig() {
        for ($i = 0; $i < $this->n - 1; $i++) {
            for ($j = $i + 1; $j < $this->n; $j++) {
                if ($this->config[$i]->isEqualsPosition($this->config[$j])) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    public function move($dir, $sizemap) {
        for ($i = 0; $i < $this->n; $i++) {
            if ($this->config[$i]->checkMove($dir, $sizemap) == FALSE) {

                return FALSE;
            }
        }

        $config = [];
        for ($i = 0; $i < $this->n; $i++) {
            $cf = $this->config[$i];
            switch ($dir) {
                case 'T':
                case 't':
                    $cf->x--;
                    break;
                case 'R':
                case 'r':
                    $cf->y++;
                    break;
                case 'B':
                case 'b':
                    $cf->x++;
                    break;
                case 'L':
                case 'l':
                    $cf->y--;
                    break;
            }

            $config [] = $cf;
        }
        $this->setConfig($config);

        return TRUE;
    }

}
