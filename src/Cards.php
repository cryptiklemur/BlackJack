<?php

/**
 * This file is part of php-blackjack
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Aequasi\BlackJack;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Cards
{
    const faceValue = 10;

    public static $cards = [
        'Ace'  => 11,
        2      => 2,
        3      => 3,
        4      => 4,
        5      => 5,
        6      => 6,
        7      => 7,
        8      => 8,
        9      => 9,
        10     => 10,
        'Jack'  => self::faceValue,
        'Queen' => self::faceValue,
        'King'  => self::faceValue
    ];

    public static $suits = [
        'Hearts',
        'Diamonds',
        'Spades',
        'Clubs'
    ];
}
 