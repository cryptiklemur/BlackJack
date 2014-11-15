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
class Decks
{
    /**
     * @type Card[]
     */
    private $cards;

    /**
     * @type bool
     */
    private $initialized = false;

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $decks = isset($config['decks']) ? $config['decks'] : 6;
        if ($decks < 1) {
            throw new \Exception("You need at least one deck.");
        }

        for ($i = 1; $i <= $decks; $i++) {
            $this->addDeck();
        }

        $this->shuffle();
    }

    /**
     * @throws \Exception
     */
    private function addDeck()
    {
        if ($this->initialized) {
            throw new \Exception("Cannot add more decks.");
        }

        foreach (Cards::$suits as $suit) {
            foreach (Cards::$cards as $name => $value) {
                $this->cards[] = new Card($name, $value, $value === 11, true);
            }
        }
    }

    /**
     * @return Card
     */
    public function dealNextCard()
    {
        foreach ($this->cards as $card) {
            if ($card->isDealt()) {
                continue;
            }

            $card->deal();

            return $card;
        }

        $this->shuffle();
    }

    /**
     * Shuffles the deck.
     *
     * First, it should grab all teh cards currently in play, and add them to the new deck (still in play, and dealt)
     * Second, it should take the remaining cards, and shuffle their index, then run through, and mark as not dealt.
     *
     * @return $this
     */
    public function shuffle()
    {
        $newDeck = [];
        foreach ($this->cards as $index => $card) {
            if ($card->isInPlay()) {
                $newDeck[] = $card;
                unset($this->cards[$index]);
            }
        }

        shuffle($this->cards);
        foreach ($this->cards as $card) {
            $card->putInDeck();
            $newDeck[] = $card;
        }

        $this->cards = $newDeck;

        return $this;
    }

    /**
     * @param $shuffleAt
     *
     * @return $this|Decks
     */
    public function checkShuffle($shuffleAt)
    {
        $remaining = 0;
        foreach ($this->cards as $card) {
            if (!$card->isDealt()) {
                $remaining++;
            }

            if ($remaining > $shuffleAt) {
                return $this;
            }
        }

        return $this->shuffle();
    }
}
 