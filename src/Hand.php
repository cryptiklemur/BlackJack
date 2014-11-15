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
class Hand
{
    /**
     * @type Card[]
     */
    private $cards = [];

    /**
     * @param Card $card
     *
     * @return $this
     */
    public function addCard(Card $card)
    {
        $this->cards[] = $card;

        return $this;
    }

    public function hasAce()
    {
        foreach ($this->cards as $card) {
            if ($card->isAce()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        $value = 0;
        foreach ($this->cards as $card) {
            $value += $card->getValue();
        }

        while (true) {
            if ($value < 22) {
                break;
            }
            if (!$this->hasAce()) {
                break;
            }
            foreach ($this->cards as $card) {
                if ($card->isAce()) {
                    $value -= 10;
                    $card->setAce(false);
                    $card->setValue(1);
                    break;
                }
            }
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function isBust()
    {
        return $this->getValue() > 21;
    }

    /**
     * @return bool
     */
    public function is21()
    {
        return $this->getValue() === 21;
    }

    /**
     * @return bool
     */
    public function is17OrHigher()
    {
        return $this->getValue() >= 17;
    }

    /**
     * @return string
     */
    public function getCards()
    {
        $cards = '';
        foreach ($this->cards as $card) {
            $cards .= $card->getName().', ';
        }
        $cards = rtrim($cards, ', ');

        return $cards;
    }

    /**
     * @return int|string
     */
    public function getDisplayValue()
    {
        if ($this->isBust()) {
            return 'Bust! '.$this->getValue();
        }

        if ($this->is21()) {
            return '21!';
        }

        return $this->getValue();
    }

    public function setVisible()
    {
        foreach ($this->cards as $card) {
            $card->setVisible(true);
        }
    }
}
 