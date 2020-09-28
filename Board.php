<?php
include 'Player.php';

class Board {
    public $player;
    public $maxWeight;
    public $boardState;
    private $boardLength;
    public $currentState;
    public $currentTurn;
    private $boardWeight;
    private $currentPut;
    public $gameOver;
    public $boardColor;
    public $leftTorque;
    public $rightTorque;

    /**
     * This function is used to check the balance, and check whether it is over or not
     * 
     * @return bool true means game is over, false means not over
     */
    public function isGameOver() {
        $leftTorque = 0;
        $rightTorque = 0;
        for ($i = -$this->boardLength; $i <= $this->boardLength; $i++) {
            $leftTorque += ($i + 3) * $this->boardState[$i];
            $rightTorque += ($i + 1) * $this->boardState[$i];
        }
	    // add information about the board weight, now let's do 3
        $leftTorque += 3 * $this->boardWeight;
	    $rightTorque += 1 * $this->boardWeight;
	    $this->leftTorque = $leftTorque;
	    $this->rightTorque = $rightTorque;
        return $leftTorque < 0 || $rightTorque >0;
    }


    /**
     * first integer: 1 means place stage, 2 means remove stage;
     * last integer: 1 means game is over, 0 means game is not over;
     * 
     * @return string the information for players
     */
    function generateSendingString(){
        if($this->currentState == "place"){
            $s = "0";
        } else {
            $s = "1";
        }

        for($i = -$this->boardLength; $i <= $this->boardLength; $i++){
            $s = $s . " " . $this->boardState[$i];
        }

        if($this->gameOver){
            $s = $s . " 1";
        } else {
            $s = $s . " 0";
        }

        return $s;
    }

    /**
     * Print out the information about which player wins the game
     * 
     * @param $gameOverReason the reason to print out to screen
     */
    public function setGameOver($gameOverReason){
        $this->gameOver = true;
        echo "$gameOverReason. " . $this->player[3 - $this->currentTurn]->name . " wins \n";
    }


    /**
     * The move function puts weight on position, and using isGameOver to check the balance
     * 
     * @param $weight the weight that currentUser want to move
     * @param $position the position that the currentUser want to place
     */
    public function move($weight, $position) {
        if($position < -$this->boardLength || $position > $this->boardLength || $this->boardState[$position] != 0) {
            $this->setGameOver("Wrong position from " . $this->player[$this->currentTurn]->name);
        } else if (!$this->player[$this->currentTurn]->WeightState[$weight]) {
            $this->setGameOver("Wrong weight from " .  $this->player[$this->currentTurn]->name);
        } else {
            $this->boardState[$position] = $weight;
            $this->boardColor[$position] = $this->currentTurn;
            $this->player[$this->currentTurn]->WeightState[$weight] = false;

            if ($this->isGameOver()){
                $this->setGameOver("Tipping by " . $this->player[$this->currentTurn]->name);
            } else {
                echo $this->player[$this->currentTurn]->name . " put weight $weight on position $position; still balanced\n";
                $this->currentPut++;
                if ($this->currentPut == $this->boardLength * 2 + 1 || $this->currentPut == 2 * $this->maxWeight){
                    $this->currentState = "remove";
                    echo "It's the removal stage now.\n";
                }

                $this->currentTurn = 3 - $this->currentTurn;
            }
        }
    }


    /**
     * Remove funciton remove the weight from position, check if there is no weight.
     * 
     * @param $position the position that player want to remove weight.
     */
    public function remove($position){
        if($position < -$this->boardLength || $position > $this->boardLength || $this->boardState[$position] == 0){
            $this->setGameOver("Wrong position from " . $this->player[$this->currentTurn]->name . "\n");
        }

        else{
            $weight = $this->boardState[$position];
            $this->boardState[$position] = 0;
            if($this->isGameOver()){
                $this->setGameOver("Tipping by " . $this->player[$this->currentTurn]->name . "\n");
            }
            else {
                echo $this->player[$this->currentTurn]->name . " removes weight $weight on position $position; still balanced\n";
                $this->currentTurn = 3 - $this->currentTurn;
            }
        }
    }

    /**
     * Give player turn and the time he consumed, update the time of him.
     * 
     * @param $turn
     * @param $time
     */
    public function updateTime($turn, $time){
        $this->player[$turn]->timeLeft -= $time;

        if($this->player[$turn]->timeLeft <= 0) {
            $this->setGameOver($this->player[$turn]->timeLeft . " has ran out of time");
        } else {
            echo $this->player[$turn]->name . " has " . $this->player[$turn]->timeLeft . " seconds left\n";
        }
    }

    function __construct($boardLength, $numberOfWeight, $boardWeight, $player1, $player2) {
        if ($boardLength <= 3 || $numberOfWeight <= 0) {
            throw new Exception("Not proper initialization parameter $boardLength $numberOfWeight");
        }

        $this->gameOver = false;
        $this->currentPut = 0;
        $this->currentState = "place";
        $this->currentTurn = 1;
        $this->boardWeight = $boardWeight;
        $this->maxWeight = $numberOfWeight;
        $this->boardLength = $boardLength;
        
        for($i = -$this->boardLength; $i <= $this->boardLength; $i++){
            $this->boardState[$i] = 0;
    	    $this->boardColor[$i] = 0;
        }

        $this->player[1] = new Player($player1, $numberOfWeight);
        $this->player[2] = new Player($player2, $numberOfWeight);
        $this->boardState[-4] = 3;
    }
}
