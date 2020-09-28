<?php
class Player {
    public $WeightState;
    public $timeLeft;
    public $name;

    function __construct($name, $numberOfWeight) {
        for($i = 1; $i <= $numberOfWeight; $i++) {
            $this->WeightState[$i] = true;
        }

        $this->timeLeft = 120.0;
        $this->name = $name;
    }
}
