<?php
// ini_set('display_errors', 'On');
// ini_set('display_startup_errors', true);
// error_reporting(E_ALL);

include "GameController.php";
include "Board.php";
include "Draw.php";

$host = explode(":", $argv[1])[0];
$port = explode(":", $argv[1])[1];
$numOfWeights = $argv[2];
$slow = false;

foreach ( $argv as $arg){
    if($arg == "-w"){
	$slow = true;
	echo "Slowing down game\n";
    }
}
$myController = new GameController($host, $port);
$myController->createConnection($numOfWeights);
$myGame = new Board(30, $numOfWeights, 3, $myController->player1, $myController->player2);

while(!$myGame->gameOver) {
    echo "--------------------------------------------------------------------------------------------------------------\n";
    
    $sendingString = $myGame->generateSendingString();
    if($sendingString[0] == '0') {
        echo "Placing Stage, board state: \n";
    } else {
        echo "Removing Stage, board state: \n";
    }

    echo substr($sendingString, 2 , strlen($sendingString) - 4) . "\n";
    draw($myGame, false);

    $myController->send($myGame->currentTurn, $myGame->generateSendingString());
    $time1 = microtime(true);
    $move = $myController->recvMove($myGame->currentTurn);
    $time2 = microtime(true);
    $myGame->updateTime($myGame->currentTurn, $time2 - $time1);
    $myMove = explode(" ", $move);

    if($myGame->gameOver) {
        break;
    }

    if ($myGame->currentState == "place") {
        $myGame->move((int)$myMove[0], (int)$myMove[1]);
    } else {
        $myGame->remove((int)$myMove[0]);
    }

    if ($slow) {
	   usleep(500000); // sleep for half a second
    }

}

$myController->send(1, $myGame->generateSendingString());
$myController->send(2, $myGame->generateSendingString());
draw($myGame ,true);
$myController->closeConnection();

