<?php

class GameController {
    private $socket;
    private $resources;
    public $player1 = "Player 1";
    public $player2 = "Player 2";

    function __construct($address, $port) {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_bind($this->socket, $address, $port);
    }

    function createConnection($numOfWeights) {
        socket_listen($this->socket);
        echo "Waiting for Player...\n";

        while (true) {
            if ($this->resources[1] = socket_accept($this->socket)) {
                socket_set_nonblock($this->resources[1]);
                $data = explode(" ", $this->recv(1));
                $this->player1 = $data[0];
                $this->send(1, $numOfWeights);
                echo "Connection from " . $this->player1 . ", established\n";
                break;
            }
        }

        echo "Waiting for Player...\n";
        while (true) {
            if ($this->resources[2] = socket_accept($this->socket)) {
                socket_set_nonblock($this->resources[2]);
                $data = explode(" ", $this->recv(2));
                $this->player2 = $data[0];
                $this->send(2, $numOfWeights);
                echo "Connection from " . $this->player2 . ", established\n";
                break;
            }
        }

        if($data[1] == 1) {
            $tmp = $this->resources[1];
            $this->resources[1] = $this->resources[2];
            $this->resources[2] = $tmp;

            $tmp = $this->player1;
            $this->player1 = $this->player2;
            $this->player2 = $tmp;
        }
    }

    function closeConnection() {
        socket_close($this->resources[1]);
        socket_close($this->resources[2]);
        socket_close($this->socket);
    }

    function send($player, $string) {
        socket_write($this->resources[$player], "$string\n");
    }

    function recvMove($player) {
        $data = $this->recv($player);
        $player = $player == '1' ? $this->player1 : $this->player2;

        echo "Received move " . $data . " from " . $player . "\n";
        return $data;
    }

    function recv($player) {
        while (true) {
            $data = socket_read($this->resources[$player], 1024, PHP_BINARY_READ);
            if ($data != "") {
                return $data;
            }
        }
    }
}

