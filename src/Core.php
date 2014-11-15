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

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Core
{
    /**
     * @type int Starting balance for the players
     */
    public static $startingBalance = 1000;

    /**
     * @type int[] Allowed Bets
     */
    public static $allowedBets = [5, 10, 25, 50, 100, 500, 1000];

    /**
     * @type array
     */
    private $config;

    /**
     * @type Decks
     */
    private $decks;

    /**
     * @type Player
     */
    private $mainPlayer;

    /**
     * @type Player[]
     */
    private $players;

    /**
     * @type OutputInterface
     */
    private $output;

    /**
     * @type InputInterface
     */
    private $input;

    /**
     * @type HelperSet
     */
    private $helperSet = [];

    /**
     * @type Dealer
     */
    private $dealer;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->initialize();
    }

    /**
     *
     */
    private function initialize()
    {
        $this->output    = new ConsoleOutput();
        $this->input     = new ArgvInput();
        $this->helperSet = new HelperSet(['question' => new QuestionHelper(), 'formatter' => new FormatterHelper()]);
    }

    public function play()
    {

        $question        = new Question('What is your name? ', 'Player 1');
        $name            = $this->helperSet->get('question')->ask($this->input, $this->output, $question);
        $this->dealer    = new Dealer('Dealer');
        $this->decks     = new Decks($this->config);
        $this->players   = [];
        $this->players[] = $mainPlayer = new Player($name, self::$startingBalance, $this->config['defaultBet'], true);
        for ($i = 0; $i < $this->config['aiCount']; $i++) {
            $this->players[] = new Player('Player '.($i + 2), self::$startingBalance, 10, false);
        }

        $this->output->writeln(["", "Lets play some BlackJack!", ""]);

        while (!$mainPlayer->isBroke()) {
            $this->output->writeln(["", "-----------------", "", 'Current Balance: '.$mainPlayer->getBalance()]);
            $this->startRound();
            $this->showRound();

            foreach ($this->players as $player) {
                $this->handleRound($player);
                sleep(1);
            }

            $this->handleRound($this->dealer);

            foreach ($this->players as $player) {

                if ($player->isHuman()) {
                    $this->output->writeln("");
                }
                if ($this->playerLost($player, $this->dealer)) {
                    if ($player->isHuman()) {
                        $this->output->writeln(
                            $this->helperSet->get('formatter')->formatBlock('You Lose!', 'bg=red;fg=white', true)
                        );
                    }
                    $player->setBalance($player->getBalance() - $player->getCurrentBet());
                    continue;
                }

                if ($player->getHand()->getValue() === $this->dealer->getHand()->getValue()) {
                    if ((count($player->getHand()->getCards()) != 2) && !$player->getHand()->is21()) {
                        if ($player->isHuman()) {
                            $this->output->writeln(
                                $this->helperSet->get('formatter')->formatBlock('You Tied!', 'bg=yellow;fg=black', true)
                            );
                        }
                        continue;
                    }
                }

                if ($player->isHuman()) {
                    $this->output->writeln(
                        $this->helperSet->get('formatter')->formatBlock('You Won!', 'bg=green;fg=white', true)
                    );
                }
                $player->setBalance($player->getBalance() + $player->getCurrentBet());
            }

            if ($this->decks->checkShuffle($this->config['shuffleAt'])) {
                ;
            }
        }

        $this->output->writeln(["", "Uh Oh. You are broke."]);
        $question = new ChoiceQuestion("Would you like to play again? ", ['y' => 'yes', 'n' => 'no'], 'y');
        if ($this->helperSet->get('question')->ask($this->input, $this->output, $question) === 'yes') {
            $this->play();
        }
    }

    /**
     * @param Player $player
     * @param Dealer $dealer
     *
     * @return bool
     */
    private function playerLost(Player $player, Dealer $dealer)
    {
        if ($player->getHand()->isBust()) {
            return true;
        }

        if (!$dealer->getHand()->isBust() && $player->getHand()->getValue() < $dealer->getHand()->getValue()) {
            return true;
        }

        return false;
    }

    /**
     * Deal the cards out
     */
    private function startRound()
    {
        $this->output->writeln(["", ""]);

        foreach ($this->players as $player) {
            $player->getRoundBet($this->helperSet->get('question'), $this->input, $this->output);
        }

        foreach ($this->players as $player) {
            $hand = new Hand();
            $hand->addCard($this->decks->dealNextCard());
            $player->setHand($hand);
        }
        $dealerHand = new Hand();
        $dealerHand->addCard($this->decks->dealNextCard());
        $this->dealer->setHand($dealerHand);

        foreach ($this->players as $player) {
            $player->getHand()->addCard($this->decks->dealNextCard());
        }
        $dealerHand->addCard($this->decks->dealNextCard()->setVisible(false));
    }

    private function handleRound(Player $player)
    {
        $this->output->writeln(
            [
                "",
                $this->helperSet->get('formatter')->formatBlock($player->getName().'\'s turn!', 'bg=blue;fg=white', true),
                ""
            ]
        );

        if ($player instanceof Dealer) {
            $player->getHand()->setVisible();
            $this->showRound();
        }

        while (true) {
            if ($player->getHand()->isBust() || $player->getHand()->is21()) {
                sleep(2);
                return;
            }

            if ($player->isHuman()) {
                $question = new ChoiceQuestion('Stay/Hit', ['s' => 'stay', 'h' => 'hit'], 's');
                if ($this->helperSet->get('question')->ask($this->input, $this->output, $question) === 'stay') {
                    return;
                }
            } else {
                if ($player->getHand()->is17OrHigher()) {
                    $this->output->writeln(" // ".$player->getName().' stayed');
                    return;
                }
                $this->output->writeln(" // ".$player->getName().' hit!');
            }

            $player->getHand()->addCard($this->decks->dealNextCard());
            $this->showRound();

            if ($player->getHand()->isBust() || $player->getHand()->is21()) {
                sleep(2);
                return;
            }

            sleep(1);
            $this->output->writeln(["", ""]);
        }
    }

    private function showRound()
    {
        $table = new Table($this->output);
        $table->setHeaders(['Player', 'Cards', 'Value']);

        $table->addRow(
            [
                $this->dealer->getName(),
                $this->dealer->getHand()->getCards(),
                $this->dealer->getHand()->getDisplayValue()
            ]
        );

        foreach ($this->players as $player) {
            $table->addRow([$player->getName(), $player->getHand()->getCards(), $player->getHand()->getDisplayValue()]);
        }

        $table->render();
    }
}
 