<?php

/* The Game of War
 * Developer: Kyle "BlackWater" Davis
 * Updated: 12/12/2014
 * License: MIT
 * Purpose: Demonstrate logic and programming.
 * IMPORTANT:	There are many ways to program this game.
 * 				There are many OO approaches.
 * 				This is only one example of many possibilities.
 */

// Error printing (uncomment if you need this)
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class Deck {
	private $cards;
	
	public function __construct() {
		$k = 0;
		// Populate with four suits
		for($i = 0; $i < 4; $i++) {
			// Determine the suit
			switch($i) {
				case 0: $suit = "Hearts"; break;
				case 1: $suit = "Diamonds"; break;
				case 2: $suit = "Spades"; break;
				case 3: $suit = "Clovers"; break;
			}
			
			// Fill up the suit
			for($j = 2; $j < 15; $j++) {
				$this->cards[$k]["value"] = $j;
				$this->cards[$k]["suit"] = $suit;
				switch($j) {
					case 14: $this->cards[$k]["rank"] = "A"; break; // A
					case 13: $this->cards[$k]["rank"] = "K"; break; // K
					case 12: $this->cards[$k]["rank"] = "Q"; break; // Q
					case 11: $this->cards[$k]["rank"] = "J"; break; // J
					default: $this->cards[$k]["rank"] = "$j"; break;
				}
				$k++;
			}
		}
	}
	
	public function shuffle() {
		for($i = 0; $i < 51; $i++) {
			// Get random number and swap the cards
			$j = rand(0, 51);
			$swap = $this->cards[$i];
			$this->cards[$i] = $this->cards[$j];
			$this->cards[$j] = $swap;
		}
	}
	
	public function remove($amount = 1) {
		$rm = array();
		for($i = 0; $i < $amount; $i++) {
			$rm[$i] = array_pop($this->cards);
		}
		return $rm;
	}
	
	public function getCards() {
		return $this->cards;
	}
}

class War {
	private $roundNumber;
	private $x;
	private $y;
	private $xStakes;
	private $yStakes;
	
	public function __construct() {
		$deck = new Deck();
		$deck->shuffle();
		$this->x = $deck->remove(26); // Takes half the cards
		$this->y = $deck->getCards(); // Gets the rest
		$this->roundNumber = 1;
	}	

	// For development testing and CHEATERS
	// This will display what each player has in their pile
	public function showContents() {
		$print = "<br><br><b>DECK INFO</b><br>Player 1 Cards: " . count($this->x) . "<br>Player 2 Cards: " . count($this->y) . "<br>";
		foreach($this->x as $card) {
			$print .= "Player 1: {$card['rank']} of {$card['suit']}<br>";
		}
		foreach($this->y as $card) {
			$print .= "Player 2: {$card['rank']} of {$card['suit']}<br>";
		}
		return $print;
	}
	
	// Put the stakes into one player's deck (on top)
	public function compile($deck, $bottom = false) {
		if($bottom) {
			$deck = array_merge($this->xStakes, $this->yStakes, $deck);
		} else {
			$deck = array_merge($deck, $this->xStakes, $this->yStakes);
		}
		$this->xStakes = array();
		$this->yStakes = array();
		return $deck;
	}
	
	// Add all stakes into a deck pile
	public function win() {
		// Print out loot
		$xLoot = "";
		$yLoot = "";
		$xAmount = count($this->xStakes) - 1;
		$yAmount = count($this->yStakes) - 1;
		for($i = 0; $i <= $xAmount; $i++) {
			$xLoot .= "<li>{$this->xStakes[$i]['rank']} of {$this->xStakes[$i]['suit']}</li>";
		}
		for($i = 0; $i <= $yAmount; $i++) {
			$yLoot .= "<li>{$this->yStakes[$i]['rank']} of {$this->yStakes[$i]['suit']}</li>";
		}
		$print = "<br>
		Loot from Player 1: <ul>{$xLoot}</ul><br>
		Loot from Player 2: <ul>{$yLoot}</ul><br>
		";
		
		// Merge piles and print victory
		if($this->xStakes[$xAmount]['value'] > $this->yStakes[$yAmount]['value']) {
			// Player 1 has won the round!
			$this->x = $this->compile($this->x, true);
			$print .= "<b>Player 1 justed Aced Player 2!</b><br>";
		} else { 
			// Player 2 has won the round!
			$this->y = $this->compile($this->y, true);
			$print .= "<b>Player 2 justed Clubbed Player 1!</b><br>";
		}
		$print .= "Card Count: " . count($this->x) . " / " . count($this->y);
		return $print;
	}
	
	// Play a round of the game
	public function round() {
		if(count($this->x) == 0) {
			return "<p><b>No battle. Player 1 has already been destroyed!</b></p>";
		} elseif(count($this->y) == 0) {
			return "<p><b>No battle. Player 2 has already been destroyed!</b></p>";
		}
		
		// Draw cards
		$xAmount = 0;
		$yAmount = 0;
		$this->xStakes = array(array_pop($this->x));
		$this->yStakes = array(array_pop($this->y));
		
		// Print the reveal
		$print = "
			<h3>ROUND {$this->roundNumber}!</h3>
			Player 1 revealed {$this->xStakes[0]['rank']} of {$this->xStakes[0]['suit']}<br>
			Player 2 revealed {$this->yStakes[0]['rank']} of {$this->yStakes[0]['suit']}<br>
		";
		$this->roundNumber++;

		// Keep going until one side is greater
		while($this->xStakes[$xAmount]['value'] == $this->yStakes[$yAmount]['value']) {
			// Try adding to stake piles
			$xDrawable = count($this->x);
			$yDrawable = count($this->y);
			if($xDrawable >= 2) {
				$this->xStakes[] = array_pop($this->x);
				$this->xStakes[] = array_pop($this->x);
			} elseif ($xDrawable == 1) {
				$this->xStakes[] = array_pop($this->x);
			}
			if($yDrawable >= 2) {
				$this->yStakes[] = array_pop($this->y);
				$this->yStakes[] = array_pop($this->y);
			} elseif ($yDrawable == 1) {
				$this->yStakes[] = array_pop($this->y);
			}
			
			// Update meta (number in each stake pile and deck)
			$xAmount = count($this->xStakes) - 1;
			$yAmount = count($this->yStakes) - 1;
			$xDrawable = count($this->x);
			$yDrawable = count($this->y);
			
			// Print the war
			$print .= "
				<p><b>WAR!</b><br>
				- Player 1 challenges with {$this->xStakes[$xAmount]['rank']} of {$this->xStakes[$xAmount]['suit']}<br>
				- Player 2 challenges with {$this->yStakes[$yAmount]['rank']} of {$this->yStakes[$yAmount]['suit']}</p>
			";
			
			// SPECIAL CONDITION: Exit if tied and no more cards on either
			if(($this->xStakes[$xAmount]['value'] == $this->yStakes[$yAmount]['value']) && ($xDrawable == 0) && ($yDrawable == 0)) {
				$print .= "<b>DRAW!</b>";
				return $print;
			}
		}
		
		$print .= $this->win();
		return $print;
	}
}

// Create War!
$war = new War();
$content = 	"<!DOCTYPE html>
<html lang=\"en\">
<head>
	<meta charset=\"UTF-8\">
	<title>The Game Of War</title>
</head>
<body>
	" . 
	$war->round() . 
	$war->round() . 
	$war->round() . 
	$war->round() . 
	$war->round() . 
	$war->round() . 
	$war->showContents() .
	"
</body>
</html>";

echo $content;

// NOTE! File writing may require certain permissions on your server (uncomment to use)
/*
if($file = @fopen("war_results.html", "w")) {
	fputs($file, $content, strlen($content));
	fclose($file);
}
*/

?>
