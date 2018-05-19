<?php 
/**
 * PP Class
 * ---
 * This class was written by a lazy people.
 * 
 * @copyright (c) 2018 PP
 * @author PP (@senaphung)
 */
    final class PP
    {
        private $fileName;
        private $logs;
        private $player;
        private $sizeMap;
        
        private $brokenCabins = [];
        private $hitCabins = [];
        
        private $lockedAfterHit = [];
        private $locked = [];

        // Type of Action
        const ATK = 'A';
        const DEF = 'D';
        
        // Status of Action
        const TRUE = 'T';
        const FALSE = 'F';

        public function __construct($file = 'data.txt', $player = 1, $sizeMap = 10) {

            $this->player = $player;
            $this->sizeMap = $sizeMap;
            $this->tryHard = false;

            $data = file($file);
            $lines = count($data)-1;

            if ($lines > 0) {
                $this->fileName = $file;
                $this->logs = fopen($file, 'r');
            }

        }

        public function setTHMode() {
            $this->tryHard = true;
        }
    
        /**
         * SOLVE Function
         * ---
         * I don't know what i'm doing with my code, but it's work, then i afraid when i remove some lines of code, it will be die!
         * 
         * @return Attack or Defend with Code
         */
        public function solve() {
            $last = $this->getLastLine();
            $get = $this->get();
            $lines = $this->countLine();
        
            // In the case of the opponent's turn.
                if (isset($last) && $this->tryHard === false) {

                    if ($last->p != $this->player) {
                        $last = \PP::h($this->getLastLine());

                    // When an opponent declares an attack.
                        if ($last->t == self::ATK) {
                            
                        
                        // If their hit my ship.
                            if ($last->s == self::TRUE) {
                                return \PP::runRandom();
                        
                        // Or NO.
                            } else if ($last->s == self::FALSE) {
                                return $this->fireRandom();
                            }
                    
                    // Or their Defend me.
                        } else {
                            return $this->fireRandom();
                        }
                    }

                }

            // It's my turn
                $this->setHitCabins();

                if (!empty($this->hitCabins)) {
                    $action = end($this->hitCabins);

                    for ($z = end($this->hitCabins)->l+1; $z < $this->countLine(); $z++) {
                        if (\PP::h($get[$z])->a == seft::DEF) {
                            $lockedPosition = (object) [
                                'x' => \PP::h($get[$z])->x,
                                'y' => \PP::h($get[$z])->y
                            ];

                            $this->lockedAfterHit[] = $lockedPosition;
                        }
                    }

                    $xTarget = (isset($action->x)) ? $action->x : 0;
                    $yTarget = (isset($action->y)) ? $action->y : 0;
                    
                    $draftTargets = [
                        [
                            'x' => $xTarget,
                            'y' => $yTarget
                        ],
                        [
                            'x' => $xTarget,
                            'y' => $yTarget+1,
                        ],
                        [
                            'x' => $xTarget,
                            'y' => ($yTarget <= 0) ? $yTarget : $yTarget-1,
                        ],
                        [
                            'x' => $xTarget+1,
                            'y' => $yTarget,
                        ],
                        [
                            'x' => ($xTarget <= 0) ? $xTarget : $xTarget-1,
                            'y' => $yTarget,
                        ],                    
                        [
                            'x' => $xTarget+1,
                            'y' => $yTarget+1,
                        ],
                        [
                            'x' => ($xTarget <= 0) ? $xTarget : $xTarget-1,
                            'y' => ($yTarget <= 0) ? $yTarget : $yTarget-1,
                        ],
                        [
                            'x' => $xTarget+1,
                            'y' => ($yTarget <= 0) ? $yTarget : $yTarget-1,
                        ],
                        [
                            'x' => ($xTarget <= 0) ? $xTarget : $xTarget-1,
                            'y' => $yTarget+1,
                        ]
                    ];
                    
                    $targets = $this->unsetPosition($draftTargets, $this->lockedAfterHit);
                    return self::fireRandomWithTargets($targets);
                } else {
                    $myShoots = $this->getMyActions();

                    for ($x = 0; $x < count($myShoots); $x++) {
                        $lockedPositions[] = (object) [
                            'x' => \PP::hh($myShoots[$x])->x,
                            'y' => \PP::hh($myShoots[$x])->y
                        ];
                    }                

                    $this->locked = $lockedPositions;
                    
                    return $this->fireRandomWithExceptions($this->locked);
                }
        }

    // Fire! Random is the best!
        /**
         * Fire Random Function
         * ---
         * Well, I really like it. Random is da best!
         */
        public function fireRandom() {
            $x = rand(0, $this->sizeMap-1);
            $y = rand(0, $this->sizeMap-1);
            return "A {$x} {$y}";
        }

        /**
         * Fire Random With some Exceptions
         * ---
         * Hmm, Still like a Fire random, but this was added some AMAZING hope!
         */
        public function fireRandomWithExceptions($exceptions) {

            $position = $this->randomPosition();

            for ($ie = 0; $ie < count($exceptions); $ie++) {
                if ($position->x == $exceptions[$ie]->x && $position->y == $exceptions[$ie]->y) {
                    $this->fireRandomWithExceptions($exceptions);
                } else {
                    return "A {$position->x} {$position->y}";
                }
            }
        }

        /**
         * Fire Random with some Targets
         * ---
         * Ew?! What is diffrence between 'you' and above function?
         */
        public static function fireRandomWithTargets($targets) {
            if (!empty($targets)) {
                $target = array_rand($targets);

                return "A {$targets[$target]['x']} {$targets[$target]['y']}";
            }
        }

    // TODO: Not already for running!
        public function fire($target) {

            $position = \PP::p($target);

            for ($ie = 0; $ie < count($this->locked); $ie++) {
                if ($position->x == $$this->locked[$ie]->x && $position->y == $$this->locked[$ie]->y) {
                    $this->fire($$this->locked);
                } else {
                    return "A {$position->x} {$position->y}";
                }
            }
        }

    // Defend! Still Random
        /**
         * Run Random Function
         * ---
         * I feel tired!
         */
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

            $move = "D {$dir}";

            return $move;
        }

        /**
         * Get a random Position
         * ---
         */
        public function randomPosition() {
            $x = rand(0, $this->sizeMap-1);
            $y = rand(0, $this->sizeMap-1);

            return (object) [
                'x' => $x,
                'y' => $y
            ];
        }

        /**
         * Count all lines of this file.
         * ---
         */
        public function countLine() {
            if ($this->fileName) {
                $data = file($this->fileName);
                $lines = count($data)-1;

                return $lines;
            }
        }

        public function unsetPosition($draft, $locked) {
            for ($i = 0; $i < count($draft)-1; $i++) {
                for ($j = 0; $j < count($locked); $j++) {
                    if ($locked[$j]->x == $draft[$i]['x']
                        && $locked[$j]->y == $draft[$i]['y']
                    ) {
                        array_splice($draft, $i, 1);
                    }
                }
            }
            return $draft;
        }

    // SET Actions
        public function setBrokenCabins() {
            $actions = $this->getBothActions();

            for ($x = 0; $x < count($actions); $x++) {
                $action = \PP::hh($actions[$x]);
                if ($action->p == $this->player
                    && $action->t == self::ATK
                    && $action->s == self::TRUE
                ) {
                    $this->hitCabins[] = $action->l;
                }
            }
        }

        /**
         * Set hit Cabins Function
         * ---
         * If your ship was Hit by opponent, this function will be logging it.
         */
        public function setHitCabins() {

            $actions = $this->getBothActions();

            for ($x = 0; $x < count($actions); $x++) {
                $action = \PP::hh($actions[$x]);
                if ($action->p == $this->player
                    && $action->t == self::ATK
                    && $action->s == self::TRUE
                ) {
                    $this->hitCabins[] = $action;

                    $lockedPosition = (object) [
                        'x' => $action->x,
                        'y' => $action->y
                    ];

                    $this->lockedAfterHit[] = $lockedPosition;
                }
            }
        }

    // GET Actions
        public function get() {
            if ($this->fileName) {
                $logs = fopen($this->fileName, 'r');

                while(!feof($logs)) {
                    $line[] = fgets($logs);
                }

                return $line;
            }
        }

        public function getBothActions() {
            $actions = $this->get();
            $myActions = [];

            for ($x = 0; $x <= $this->countLine(); $x++) {
                $myActions[] = $x . ' ' . $actions[$x];
            }

            return $myActions;
        }

        public function getMyActions() {

            $actions = $this->get();
            $myActions = [];

            for ($x = 0; $x <= $this->countLine(); $x++) {
                if (\PP::h($actions[$x])->p == $this->player) {
                    $myActions[] = $x . ' ' . $actions[$x];
                }
            }

            return $myActions;

        }

        public function getOpponentActions() {

            $actions = $this->get();
            $myActions = [];

            for ($x = 0; $x <= $this->countLine(); $x++) {
                if (\PP::h($actions[$x])->p != $this->player) {
                    $myActions[] = $x. ' ' . $actions[$x];
                }
            }

            return $myActions;

        }

        public function getLastLine() {
            if ($this->fileName) {
                $data = file($this->fileName);
                $line = $data[count($data)-1];

                return $line;
            }
        }

        public function getLastAction() {
            if ($this->fileName) {
                $data = file($this->fileName);
                $line = $data[count($data)-2];

                return $line;
            }
        }

    // Extra functions

        /**
         * Convert action to Human (maybe) code.
         * ---
         * @param String $action
         * 
         * @return Object Action
         */
        public static function h($action) {
            if ($action) {
                $arrayOfAction = explode(' ', $action);

                if ($arrayOfAction[1] == 'A') {
                    return (object) [
                        'p' => \PP::clean($arrayOfAction[0]),
                        't' => \PP::clean($arrayOfAction[1]),
                        'y' => \PP::clean($arrayOfAction[2]),
                        'x' => \PP::clean($arrayOfAction[3]),
                        's' => \PP::clean($arrayOfAction[4])
                    ];
                } else {
                    return (object ) [
                        'p' => \PP::clean($arrayOfAction[0]),
                        't' => \PP::clean($arrayOfAction[1]),
                        'm' => \PP::clean($arrayOfAction[2]),
                    ];
                }
            }
        }

        /**
         * Convert action to Human (maybe) code with line number.
         * ---
         * @param String $action
         * 
         * @return Object Action
         */
        public static function hh($action) {
            if ($action) {
                $arrayOfAction = explode(' ', $action);

                if (\PP::clean($arrayOfAction[2]) == self::ATK) {
                    return (object) [
                        'l' => \PP::clean($arrayOfAction[0]),
                        'p' => \PP::clean($arrayOfAction[1]),
                        't' => \PP::clean($arrayOfAction[2]),
                        'x' => \PP::clean($arrayOfAction[3]),
                        'y' => \PP::clean($arrayOfAction[4]),
                        's' => \PP::clean($arrayOfAction[5])
                    ];
                } else {
                    return (object ) [
                        'l' => \PP::clean($arrayOfAction[0]),
                        'p' => \PP::clean($arrayOfAction[1]),
                        't' => \PP::clean($arrayOfAction[2]),
                        'm' => \PP::clean($arrayOfAction[3]),
                    ];
                }
            }
        }

        /**
         * Convert position to Human (maybe) code.
         * ---
         * @param String $target
         * 
         * @return Object Position
         */
        public static function p($target) {
            if ($target) {

                $arrayOfTarget = explode(' ', $target);

                return (object) [
                    'x' => \PP::clean($arrayOfTarget[1]),
                    'y' => \PP::clean($arrayOfTarget[2])
                ];
            }
        }
        
        /**
         * Clean string
         */
        public static function clean($char) {
            if (isset($char)) {
                return preg_replace('/\s+/', '',$char);
            }
        }

        /**
         * What the Hell?
         * ---
         * ...
         */
        private function wth() {
            // Handle some special positions
                // if ($action->x == 0
                //     && $action->y == 0
                // ) {
                //     $draftTargets = [
                //         [
                //             'x' => 1,
                //             'y' => 0
                //         ],
                //         [
                //             'x' => 0,
                //             'y' => 1
                //         ],
                //         [
                //             'x' => 1,
                //             'y' => 1
                //         ]
                //     ];

                //     $targets = $this->unsetPosition($draftTargets, $this->lockedAfterHit);
                // }
            
            // Handle Locked Positions
                // for ($x = $this->hitCabins[0]->l + 1; $x < $this->countLine(); $x++) {
                //     $action = \PP::h($get[$x]);
                // }
        }
    }

?>