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
class Dealer extends Player
{
    /**
     * @return bool
     */
    public function isBroke()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getCurrentBet()
    {
        return 0;
    }
}
 