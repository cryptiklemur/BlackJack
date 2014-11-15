<?php

require_once __DIR__.'/vendor/autoload.php';

$config = [
    'defaultBet' => 10,
    'decks'      => 6,
    'shuffleAt'  => 52 * 2,
    'aiCount'    => 3
];

$blackjack = new Aequasi\BlackJack\Core($config);

$blackjack->play();