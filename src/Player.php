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

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Player
{
    /**
     * @type string
     */
    private $name;

    /**
     * @type int
     */
    private $balance;

    /**
     * @type int
     */
    private $currentBet;

    /**
     * @type Hand|null
     */
    private $hand;

    /**
     * @type bool
     */
    private $human;

    /**
     * @param string $name
     * @param int    $startingBalance
     * @param int    $defaultBet
     * @param bool   $human
     */
    public function __construct($name, $startingBalance = 0, $defaultBet = 0, $human = false)
    {
        $this->name       = $name;
        $this->balance    = $startingBalance;
        $this->currentBet = $defaultBet;

        $this->human = $human;
    }

    public function getRoundBet(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
    {
        $allowed = [];
        foreach ($this->getAllowedBets() as $bet) {
            $allowed[$bet] = $bet;
        }

        if ($this->isHuman()) {
            $question = new ChoiceQuestion(
                'What is your bet? Current: '.$this->getCurrentBet().' ',
                $allowed,
                $this->currentBet
            );
            $this->currentBet = $helper->ask($input, $output, $question);
        } else {
            $this->currentBet = 10;
        }
    }



    /**
     * @param int $balance
     *
     * @return $this
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedBets()
    {
        $bets = [];
        foreach (Core::$allowedBets as $bet) {
            if ($bet < $this->balance) {
                $bets[] = $bet;
            }
        }

        return $bets;
    }

    /**
     * @return bool
     */
    public function isBroke()
    {
        return $this->balance < min(Core::$allowedBets);
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return int
     */
    public function getCurrentBet()
    {
        return $this->currentBet;
    }

    /**
     * @return Hand|null
     */
    public function getHand()
    {
        return $this->hand;
    }

    /**
     * @param Hand|null $hand
     *
     * @return Player
     */
    public function setHand($hand)
    {
        $this->hand = $hand;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $bet
     *
     * @return $this
     */
    public function setBet($bet)
    {
        $this->currentBet = $bet;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHuman()
    {
        return $this->human;
    }
}
