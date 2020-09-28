# No Tipping Game

## Problem Statement

Given a uniform, flat board (made of a titanium alloy) 60 meters long and weighing 3 kilograms, consider it ranging from -30 meters to 30 meters. So the center of gravity is at 0. We place two supports of equal heights at positions -3 and -1 and a 3 kilogram block at position -4.

The No Tipping game is a two person game that works as follows: the two players each start with k blocks having weights 1 kg through k kg where 2k is less than 50. The first player places one block anywhere on the board, then the second player places one block anywhere on the board, and play alternates with each player placing one block until the second player places his or her last block. (No player may place one block above another one, so each position will have at most one block.) If after any ply, the placement of a block causes the board to tip, then the player who did that ply loses. Suppose that the board hasn't tipped by the time the last block is placed. Then the players remove one block at a time in turns. At each ply, each player may remove a block placed by any player or the initial block. If the board tips following a removal, then the player who removed the last block loses.

As the game proceeds, the net torque around each support is calculated and displayed. The blocks, whether on the board or in the possession of the players, are displayed with their weight values. The torque is computed by weight times the distance to each support. Clockwise is negative torque and counterclockwise is positive torque. You want the net torque on the left support to be negative and the net torque on the right support to be positive.

Check link: [https://cs.nyu.edu/courses/fall20/CSCI-GA.2965-001/notipping.html]

## Starting Server

To begin the server, run `php main.php <hostname:portnumber> <number of weights>`. This will create a socket for communication to and from the server at 'hostname:portnumber'.

## Playing the Game

Every turn the player will be sent a long string of characters separated by spaces containing the state of the game. It will include:

s[0] - A flag to let the player know if they are in a stage to add weights to the board or remove weights from the board: 0 = adding weights, 1 = removing weights.

s[1:61] - The weights at each position on the board. 0 if the space is unoccupied and a weight can be placed there.

s[62] - A flag to let the player know if the game has ended or not. 0 = The game is still going and the player must make a move, 1 = The game has ended.

To make a move, the player must send a string to the server containing the following depending on the stage of the game:

## Adding Weights

If the player is still adding weights (i.e. has weights left to add), he must send the following to the server: `x y`, where `x` is the weight in kg of the weight you'd like to add, and `y` is the position where you'd like to add it [-30, 30]. These numbers must be delimited by a single space.

## Removing Weights

If the player is removing weights, he must send a string containing the following: `x` where `x` is the position of the weight to be removed in his turn.

## Illegal Moves

Making an illegal move will result in the player immediately losing the game. This includes:

* Placing the same weight twice
* Placing a weight on an occupied space
* Attempting to place a weight in a space not represented on the board (ie. x > 30 || x < -30)
* Attempting to remove a weight from an unoccupied space

## Client

When writing a client to interact with the server, the interaction works as follows:

* Connect to the server via hostname:port.
* Send to the server `x y` where `x` is the name of your team, and `y` is a boolean value indicating whether you are first or not.
* Listen to the server and receive `z` from the game, where `z` is the number of weights.
* Continue listening to server. When it is your turn, the server will send the string describing the game state to you. You can then send your move per the instructions above.

## Random Strategy Test Client

test.py is included to test your algorithm against. NOTE: test.py was written on Python 2.7.

To run the test script, use the same host name and port number that was used for the php script. For example, using our test.py you should use this command `python test.py <hostname:portnumber>`. Therefore, your code should also accept host name and port number as an argument.

Description of random strategy: A Random Strategy should play the first or second player's game (depending on the command line). During AddMode, it should choose a random remaining block and place it as far left as possible so as to avoid tipping. During RemoveMode, it should examine all blocks on the board, determine which are will not cause tipping, and remove a random one of those. This should be a small challenge to defeat.

## Localhost Server

Start the main server as a separate process by running `php main.php <hostname:port> <number of weights> [-w]`. `-w` is an optional command line arguement which forces a 1 second pause between turns(this delay does not affect each clients allowed time to run). You Can also run an instance of the webserver for a visual depiction with the command `php -S <hostname:port>`. Make sure the port of the server and webserver are different.

Have both clients establish a connection to the server. If you are using test client you can do so with `python test.py <hostname:port> [-f] <-n name>`. You can view the running game from the index.html file (by going to `localhost:8000/index.html`.
