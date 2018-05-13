<?php 

    final class PP
    {
        private $fileName;
        private $logs;

        public function __construct($file = 'data.txt') {
            if (!filesize($file)) {
                return 'Not found!';
            } else {
                $this->fileName = $file;
                $this->logs = fopen($file, 'r');
            }
        }

        public static function get_duplicates( $array ) {
            return array_unique( array_diff_assoc( $array, array_unique( $array ) ) );
        }

        public static function fireRandom($sizemap) {
            $x = rand(0, $sizemap);
            $y = rand(0, $sizemap);
            return "A {$x} {$y}";
        }

        public static function fireRandomWithException($exceptions, $sizemap) {
            $x = rand(0, $sizemap);
            $y = rand(0, $sizemap);

            for ($ie = 0; $ie < count($exceptions); $ie++) {
                if ($x == $exceptions[$ie]->x && $y == $exceptions[$ie]->y) {
                    self::fireRandomWithException($exceptions, $sizemap);
                } else {
                    return "A {$x} {$y}";
                }
            }
        }

        public static function runRandom() {
            $dir = 'T';
            $r = rand(0, 4);
            if ($r == 0) {
                $dir = 'T';
            } elseif ($r == 1) {
                $dir = 'L';
            } elseif ($r == 2) {
                $dir = 'B';
            } elseif ($r == 3) {
                $dir = 'R';
            }
            return "D {$dir}";
        }

        public function getLastLine() {
            $data = file($this->fileName);
            $line = $data[count($data)-1];

            return $line;
        }

        public function countLine() {
            $data = file($this->fileName);
            $lines = count($data)-1;

            return $lines;
        }

        public function getLastAction() {
            $data = file($this->fileName);
            $line = $data[count($data)-2];

            return $line;
        }

        public function get() {
            $logs = fopen($this->fileName, 'r');

            while(!feof($logs)) {
                $line[] = fgets($logs);
            }

            return $line;
        }

        public function human($action) {
            if ($action) {
                $arrayOfAction = explode(' ', $action);

                if ($arrayOfAction[1] == 'A') {
                    return (object) [
                        'p' => $arrayOfAction[0],
                        't' => $arrayOfAction[1],
                        'y' => $arrayOfAction[2],
                        'x' => $arrayOfAction[3],
                        's' => $arrayOfAction[4]
                    ];
                } else {
                    return (object ) [
                        'p' => $arrayOfAction[0],
                        't' => $arrayOfAction[1],
                        'm' => $arrayOfAction[2],
                    ];
                }
            }
        }
    }

?>