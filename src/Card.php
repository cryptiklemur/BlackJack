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
class Card
{
    /**
     * @type string
     */
    private $name;

    /**
     * @type int
     */
    private $value;

    /**
     * @type bool
     */
    private $ace;

    /**
     * @type bool Has this card dealt
     */
    private $dealt = false;

    /**
     * @type bool
     */
    private $inPlay = false;

    /**
     * @type bool
     */
    private $visible = true;

    /**
     * @param string $name
     * @param int    $value
     * @param bool   $ace
     * @param bool   $visible
     */
    public function __construct($name, $value, $ace, $visible = true)
    {
        $this->name    = $name;
        $this->value   = $value;
        $this->ace     = $ace;
        $this->visible = $visible;
    }

    /**
     * @return boolean
     */
    public function isDealt()
    {
        return $this->dealt;
    }

    /**
     * Places the card back in the deck
     *
     * @return $this
     */
    public function putInDeck()
    {
        $this->inPlay = false;
        $this->dealt  = false;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInPlay()
    {
        return $this->inPlay;
    }

    /**
     * Puts card in discard pile.
     *
     * @return $this
     */
    public function removeFromPlay()
    {
        $this->inPlay = false;

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function deal()
    {
        if ($this->isDealt() || $this->isInPlay()) {
            throw new \Exception("Cannot deal this card again.");
        }

        $this->dealt  = true;
        $this->inPlay = true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAce()
    {
        return $this->ace;
    }

    /**
     * @param bool $ace
     *
     * @return $this
     */
    public function setAce($ace)
    {
        $this->ace = $ace;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->visible ? $this->name : 'X';
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->visible ? $this->value : 0;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     *
     * @return Card
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }
}
 