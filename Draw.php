
<?php
function draw($board, $finished) {
    $file = 'index.html';
    $handle = fopen($file, 'w') or die('Cannot open file: ' . $file);

    writeHeader($handle, $board, $finished);
    beginScript($handle, $board, $finished);
    writeBoardState($handle, $board);
    writeEndScript($handle, $board);


    fclose($handle);
}

function writeBoardState($handle, $board) {
	fwrite($handle, "\t\tboardState = [];\n");
	fwrite($handle, "\t\t\t\tboardColor = [];\n");


	foreach($board->boardState as $key => $value) {
	   fwrite($handle, "\t\t\t\tboardState.push($value);\n");
	}

	foreach($board->boardColor as $key => $value){
	    fwrite($handle, "\t\t\t\tboardColor.push($value);\n");
	}

}

function writeHeader($handle, $board, $finished){
	fwrite($handle,'
	
	<!DOCTYPE html>
	<html>
		<head>
			<style>
				canvas {
					border:1px solid #d3d3d3;
					background-color: #f1f1f1;
				}
			</style>
			    ');
	if( $finished == false){
	fwrite($handle,'
			 <meta http-equiv="refresh" content="1; URL=http://localhost:8000/index.html">');

	
	}
	fwrite($handle, '
		</head>

		<body onload="startGame()">
		</body>
	');
}

function beginScript($handle, $board, $finished) {
	fwrite($handle, "
		<script>
			var player1 = {
				weights: [],
				name: '{$board->player[1]->name}'
			};
			var player2 = {
				weights: [],
				name: '{$board->player[2]->name}'
			};

			var leftTorque = {$board->leftTorque};
			var rightTorque = {$board->rightTorque};
			var turn = {$board->currentTurn};
			var maxWeight = {$board->maxWeight};
			var boardState;
			var boardColor;

			function startGame() {
				myGameArea.start();
			}

			var myGameArea = {
				canvas : document.createElement('canvas'),
				start : function() {
					this.canvas.width = 1600;
					this.canvas.height = 900;
					this.context = this.canvas.getContext('2d');
					document.body.insertBefore(this.canvas, document.body.childNodes[0]);
					drawTiles(this.canvas.width, this.canvas.height);
					drawPlayers(this.canvas.width, this.canvas.height);
					drawWeights(this.canvas.width, this.canvas.height);
					drawBoard(this.canvas.width, this.canvas.height);
					drawUnusedWeights(this.canvas.width, this.canvas.height);
\n");
			

	if( $finished == true){
	    fwrite($handle, "
					drawWin(this.canvas.width, this.canvas.height);\n");
	}
	fwrite($handle,"

				},
			}


			function drawTiles(width, height) {
	");
}

function writeEndScript($handle, $board){
	fwrite($handle,'

			    ctx = myGameArea.context;
			    var start = 60;
			    var length = width -168;
			    var flip = 30;
			    var step = length/60;
			    for( var i =0; i <= 60; ++i){
				if ( boardState[i] == 0)continue;
				if (boardColor[i] == 1){
				    ctx.fillStyle = "blue";
				}else{
				    ctx.fillStyle = "red";
				}
				ctx.font = "12px Consolas";
				ctx.fillText(boardState[i], start + i * step - 3, 3 * height /4 - 60);


			    }
			    ctx.fill();
			}

			function drawBoard(width, height) {
				// DRAW RECTANGULAR BOARD
				ctx = myGameArea.context;
				var start = 60;
				var length = width - 168;
				ctx.rect(start, 3 * height/4, length, 1);
				ctx.stroke();

				// INDICES FOR TICK MARKS
				var flip = 30;
				var step = length / 60;
				for(var i = 0; i <= 60; ++i) {
					ctx.fillStyle = "black";
					ctx.font = "12px Consolas";
					if(i % 5 == 0) {
						ctx.fillText(i - 30, start + i * step - 3, 3 * height / 4 + 60);
					}
					ctx.moveTo(start + i * step, 3 * height / 4 - 10);
					ctx.lineTo(start + i * step, 3 * height / 4 + 10);
					ctx.stroke();
				}

				// SUPPORT at -4
				ctx.moveTo(start + 26 * step, 3 * height / 4);
				ctx.lineTo(start + 25 * step, 3 * height / 4 + 40);
				ctx.lineTo(start + 27 * step, 3 * height / 4 + 40);
				ctx.fill();

				// SUPPORT AT -1
				ctx.moveTo(start + 29 * step, 3 * height / 4);
				ctx.lineTo(start + 28 * step, 3 * height / 4 + 40);
				ctx.lineTo(start + 30 * step, 3 * height / 4 + 40);
				ctx.fill();


		}

		function drawWeights(width, height){
			ctx = myGameArea.context;
			ctx.font = "20px Consolas";
			ctx.fillStyle = "black";
			var start = 60;
			var length = width - 168;
			var step = length/60;
			ctx.fillText(leftTorque, start + 24 * step, 3.2 * height /4 + 60);
			ctx.fillText(rightTorque, start + 28 * step, 3.2 * height /4 + 60);
		}

		function drawUnusedWeights(width, height){
			ctx = myGameArea.context;
			ctx.font = "12px Consolas";
			ctx.fillStyle = "black";
			var start = 60;
			var length = width - 168;
			var step = length/60;
			var right = 0;
			var down = 80;
			for(var i = 1; i <= maxWeight; ++i){
				var found = false;
				for( var j = 0; j <= 60; ++j){
					if ( boardState[j] == i && boardColor[j] == 1){
					    found = true;
					}
				}
				if ( !found ){
					//ctx.fillText(i, start + (right * step), down * step);
					ctx.fillText(i, start+right*step, down );
				}
				right = right + 2;
				if( right > 10 ){
					right = 0;
					down = down + 30;
				}
			}
			
			start = (width/2)+400;
			down = 80;
			right = 0;
			for(var i = 1; i <= maxWeight; ++i){
				var found = false;
				for( var j = 0; j <= 60; ++j){
					if( boardState[j] == i && boardColor[j] == 2){
						found = true;
					}
				}
				if(!found){
					ctx.fillText(i, start + right * step, down);

				}
				right = right + 2;
				if( right > 10 ){
					right = 0;
					down = down + 30;
				}
			}
		}

		function drawPlayers(width, height) {
			ctx = myGameArea.context;
			// Player 1 Text
			ctx.font = "30px Consolas";
			ctx.fillStyle = "red";
			ctx.fillText(player1.name, 100, 50);

			// Player 2 Text
			ctx.fillStyle = "blue";
			ctx.fillText(player2.name, width - 250, 50);
		}


		function drawWin(width, height){
			ctx = myGameArea.context;
			ctx.font = "30px Consolas";
			ctx.fillStyle = "black";
			var out = player2.name;
			if( turn == 2){
				out = player1.name;
			}
			out = out + " Wins!";
			var length = width - 168;
			ctx.fillText(out, length/2,  height/2);
			
		}

	    </script>
	</html>

	');
}
