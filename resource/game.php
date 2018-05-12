<?php

require 'ship.php';

class Game {

    public $ship1;
    public $ship2;
    public $sizemap;

    public function __construct($n, $sizemap = 10) {
        $this->ship1 = new Ship($n);
        $this->ship2 = new Ship($n);
        $this->sizemap = $sizemap;
    }

    public function setConfig($config1, $config2) {
        if (!$this->ship1->setConfig($config1)) {
            return FALSE;
        }
        if (!$this->ship2->setConfig($config2)) {
            return FALSE;
        }

        return $this->validConfig();
    }

    public function validConfig() {
        if ($this->ship1->n != $this->ship2->n) {
            return FALSE;
        }
        $config1 = $this->ship1->config;
        $config2 = $this->ship2->config;
        for ($i = 0; $i < $this->ship1->n; $i++) {
            for ($j = 0; $j < $this->ship2->n; $j++) {
                if ($config1[$i]->isEqualsPosition($config2[$j])) {
                    if ($config1[$i]->status == 0 && $config2[$j]->status == 0) {
                        //cell broken
                        return FALSE;
                    }
                }
            }
        }

        if (!$this->ship1->validConfig()) {
            return FALSE;
        }

        if (!$this->ship2->validConfig()) {
            return FALSE;
        }

        return TRUE;
    }

    public function revert($dir) {
        switch ($dir) {
            case 'T':
            case 't':
                return 'B';
                break;
            case 'R':
            case 'r':
                return 'L';
                break;
            case 'B':
            case 'b':
                return 'T';
                break;
            case 'L':
            case 'l':
                return 'R';
                break;
        }
    }

    public function shipShoot1($x = -1, $y = -1) {

        if ($x < 0 || $x > $this->sizemap - 1) {
            return FALSE;
        }

        if ($y < 0 || $y > $this->sizemap - 1) {
            return FALSE;
        }

        $config = [];

        $flat = FALSE;
        $cf = $this->ship2->getConfig();
        for ($i = 0; $i < $this->ship2->n; $i++) {
            if ($cf[$i]->status == 0 && $cf[$i]->x == $x && $cf[$i]->y == $y) {
                $cf[$i]->status = 1;
                $flat = TRUE;
            }
            $config[] = $cf[$i];
        }

        $this->ship2->setConfig($config);

        return $flat;
    }

    public function getIndexWon() {
        $num1 = $this->ship1->getNumBroken();
        $num2 = $this->ship2->getNumBroken();
        if ($num1 > $num2) {
            return 2;
        } else if ($num1 < $num2) {
            return 1;
        }

        return 0;
    }

    public function isGameOver() {
        if ($this->ship1->brokenAll() || $this->ship2->brokenAll()) {
            return TRUE;
        }

        return FALSE;
    }

    public function shipShoot2($x = -1, $y = -1) {

        if ($x < 0 || $x > $this->sizemap - 1) {
            return FALSE;
        }

        if ($y < 0 || $y > $this->sizemap - 1) {
            return FALSE;
        }


        $config = [];

        $flat = FALSE;
        $cf = $this->ship1->getConfig();
        for ($i = 0; $i < $this->ship1->n; $i++) {
            if ($cf[$i]->status == 0 && $cf[$i]->x == $x && $cf[$i]->y == $y) {
                $cf[$i]->status = 1;
                $flat = TRUE;
            }
            $config[] = $cf[$i];
        }

        $this->ship1->setConfig($config);
        return $flat;
    }

    public function moveShip1($dir) {
        if (!$this->ship1->move($dir, $this->sizemap)) {
            return FALSE;
        }

        if ($this->validConfig() == FALSE) {
            $this->ship1->move($this->revert($dir), $this->sizemap);
            return FALSE;
        }

        return TRUE;
    }

    public function moveShip2($dir) {
        if (!$this->ship2->move($dir, $this->sizemap)) {
            return FALSE;
        }

        if ($this->validConfig() == FALSE) {
            $this->ship2->move($this->revert($dir), $this->sizemap);
            return FALSE;
        }

        return TRUE;
    }

    public function validXY($a) {
        return ($a >= 0 && $a < $this->sizemap);
    }

    public function getMap() {
        $map = [];

        for ($i = 0; $i < $this->sizemap; $i++) {
            $map[$i] = [];
            for ($j = 0; $j < $this->sizemap; $j++) {
                $map[$i][$j] = 0;
            }
        }

        for ($i = 0; $i < $this->ship1->n; $i++) {
            $s1 = 1;
            if ($this->ship1->config[$i]->status == 1) {
                $s1 = -1;
            }

            $s2 = 2;
            if ($this->ship2->config[$i]->status == 1) {
                $s2 = -2;
            }

            if ($this->validXY($this->ship1->config[$i]->x) && $this->validXY($this->ship1->config[$i]->y)) {
                if ($map[$this->ship1->config[$i]->x][$this->ship1->config[$i]->y] <= 0) {
                    $map[$this->ship1->config[$i]->x][$this->ship1->config[$i]->y] = $s1;
                }
            }

            if ($this->validXY($this->ship2->config[$i]->x) && $this->validXY($this->ship2->config[$i]->y)) {
                if ($map[$this->ship2->config[$i]->x][$this->ship2->config[$i]->y] <= 0) {
                    $map[$this->ship2->config[$i]->x][$this->ship2->config[$i]->y] = $s2;
                }
            }
        }

        return $map;
    }

    public function Draw($x = -1, $y = -1, $index = "") {
        $map = $this->getMap();

        $html = '<div class="main"><div class="map">';
        for ($i = 0; $i < $this->sizemap; $i++) {
            $html .= '<div class="row">';
            for ($j = 0; $j < $this->sizemap; $j++) {

                $class = "";
                switch ($map[$i][$j]) {
                    case 1:
                        $class = "ship ship1";
                        break;
                    case -1:
                        $class = "ship ship1 broken";
                        break;
                    case 2:
                        $class = "ship ship2";
                        break;
                    case -2:
                        $class = "ship ship2 broken";
                        break;
                }

                $fire = "";
                $clfire = "";
                if ($x == $i && $y == $j) {
                    $fire = $index;
                    $clfire = "fire";
                }

                $html .= '<div class="col ' . $class . ' ' . $clfire . '">';
                $html .= '<div class="cell">' . $fire . '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        $html .= '</div></div>';

        return $html;
    }

}
