<?php
namespace app\modules\seaBattle\models;

use Yii;

class Game
{
    const PHASE_SETUP = 0;
    const PHASE_BATTLE = 1;
    const PHASE_END = 2;

    public $phase = self::PHASE_SETUP;
    public $playerBoard = [];
    public $computerBoard = [];
    public $playerShips = [];
    public $computerShips = [];
    public $message = '';
    public $playerTurn = true;
    public $boardSize = 10;

    public function __construct()
    {
        $this->initializeBoards();
    }

    private function initializeBoards()
    {
        for ($x = 0; $x < $this->boardSize; $x++) {
            for ($y = 0; $y < $this->boardSize; $y++) {
                $this->playerBoard[$x][$y] = new Cell($x, $y);
                $this->computerBoard[$x][$y] = new Cell($x, $y);
            }
        }
    }

    public static function loadFromSession()
    {
        $session = Yii::$app->session;
        $game = $session->get('seaBattleGame');
        
        if (!$game instanceof self) {
            $game = new self();
            $session->set('seaBattleGame', $game);
        }
        
        return $game;
    }

    public function saveToSession()
    {
        Yii::$app->session->set('seaBattleGame', $this);
    }

    public function placeShip($x, $y, $size, $horizontal, $isPlayer = true)
    {
        $board = $isPlayer ? $this->playerBoard : $this->computerBoard;
        $ships = $isPlayer ? $this->playerShips : $this->computerShips;

        // Проверка возможности размещения
        if ($horizontal) {
            if ($x + $size > $this->boardSize) return false;
            for ($i = 0; $i < $size; $i++) {
                if ($board[$x + $i][$y]->state !== Cell::STATE_EMPTY) return false;
            }
        } else {
            if ($y + $size > $this->boardSize) return false;
            for ($i = 0; $i < $size; $i++) {
                if ($board[$x][$y + $i]->state !== Cell::STATE_EMPTY) return false;
            }
        }

        // Размещение корабля
        $ship = new Ship($size);
        if ($horizontal) {
            for ($i = 0; $i < $size; $i++) {
                $board[$x + $i][$y]->state = Cell::STATE_SHIP;
                $ship->addCell($board[$x + $i][$y]);
            }
        } else {
            for ($i = 0; $i < $size; $i++) {
                $board[$x][$y + $i]->state = Cell::STATE_SHIP;
                $ship->addCell($board[$x][$y + $i]);
            }
        }
        $ships[] = $ship;

        return true;
    }

    public function autoPlaceComputerShips()
    {
        $ships = [4, 3, 3, 2, 2, 2, 1, 1, 1, 1]; // Размеры кораблей
        
        foreach ($ships as $size) {
            $placed = false;
            $attempts = 0;
            
            while (!$placed && $attempts < 100) {
                $x = rand(0, $this->boardSize - 1);
                $y = rand(0, $this->boardSize - 1);
                $horizontal = (bool)rand(0, 1);
                
                $placed = $this->placeShip($x, $y, $size, $horizontal, false);
                $attempts++;
            }
        }
    }

    public function playerShoot($x, $y)
    {
        if (!$this->playerTurn || $this->phase !== self::PHASE_BATTLE) {
            return false;
        }

        if ($x < 0 || $x >= $this->boardSize || $y < 0 || $y >= $this->boardSize) {
            $this->message = 'Недопустимые координаты!';
            return false;
        }

        $cell = $this->computerBoard[$x][$y];
        
        if ($cell->state === Cell::STATE_HIT || $cell->state === Cell::STATE_MISS) {
            $this->message = 'Вы уже стреляли в эту клетку!';
            return false;
        }

        if ($cell->state === Cell::STATE_SHIP) {
            $cell->state = Cell::STATE_HIT;
            $this->message = 'Попадание!';
            
            // Проверяем, потоплен ли корабль
            foreach ($this->computerShips as $ship) {
                foreach ($ship->cells as $shipCell) {
                    if ($shipCell === $cell) {
                        if ($ship->checkIfSunk()) {
                            $this->message = 'Корабль потоплен!';
                        }
                        break 2;
                    }
                }
            }
            
            // Проверяем конец игры
            if ($this->checkWin(true)) {
                $this->phase = self::PHASE_END;
                $this->message = 'Вы победили!';
            }
        } else {
            $cell->state = Cell::STATE_MISS;
            $this->message = 'Промах!';
            $this->playerTurn = false;
            $this->computerShoot();
        }

        return true;
    }

    private function computerShoot()
    {
        // Простой ИИ для компьютера
        $attempts = 0;
        $hit = false;
        
        // Сначала ищем рядом с уже попаданиями
        $hits = [];
        for ($x = 0; $x < $this->boardSize; $x++) {
            for ($y = 0; $y < $this->boardSize; $y++) {
                if ($this->playerBoard[$x][$y]->state === Cell::STATE_HIT) {
                    $hits[] = [$x, $y];
                }
            }
        }
        
        if (!empty($hits)) {
            shuffle($hits);
            foreach ($hits as $hit) {
                list($hx, $hy) = $hit;
                
                // Проверяем соседние клетки
                $directions = [[0, 1], [1, 0], [0, -1], [-1, 0]];
                shuffle($directions);
                
                foreach ($directions as $dir) {
                    $nx = $hx + $dir[0];
                    $ny = $hy + $dir[1];
                    
                    if ($nx >= 0 && $nx < $this->boardSize && $ny >= 0 && $ny < $this->boardSize) {
                        $cell = $this->playerBoard[$nx][$ny];
                        if ($cell->state === Cell::STATE_EMPTY || $cell->state === Cell::STATE_SHIP) {
                            $x = $nx;
                            $y = $ny;
                            $hit = true;
                            break 2;
                        }
                    }
                }
            }
        }
        
        // Если не нашли рядом с попаданиями, стреляем случайно
        while (!$hit && $attempts < 100) {
            $x = rand(0, $this->boardSize - 1);
            $y = rand(0, $this->boardSize - 1);
            $cell = $this->playerBoard[$x][$y];
            
            if ($cell->state === Cell::STATE_EMPTY || $cell->state === Cell::STATE_SHIP) {
                $hit = true;
            }
            $attempts++;
        }
        
        // Выстрел
        if ($hit) {
            $cell = $this->playerBoard[$x][$y];
            
            if ($cell->state === Cell::STATE_SHIP) {
                $cell->state = Cell::STATE_HIT;
                
                // Проверяем, потоплен ли корабль
                foreach ($this->playerShips as $ship) {
                    foreach ($ship->cells as $shipCell) {
                        if ($shipCell === $cell) {
                            if ($ship->checkIfSunk()) {
                                $this->message = 'Ваш корабль потоплен!';
                            }
                            break 2;
                        }
                    }
                }
                
                // Проверяем конец игры
                if ($this->checkWin(false)) {
                    $this->phase = self::PHASE_END;
                    $this->message = 'Компьютер победил!';
                }
            } else {
                $cell->state = Cell::STATE_MISS;
                $this->playerTurn = true;
            }
        } else {
            $this->playerTurn = true;
        }
    }

    private function checkWin($isPlayer)
    {
        $ships = $isPlayer ? $this->computerShips : $this->playerShips;
        
        foreach ($ships as $ship) {
            if (!$ship->isSunk) {
                return false;
            }
        }
        
        return true;
    }

    public function startGame()
    {
        $this->phase = self::PHASE_BATTLE;
        $this->playerTurn = true;
        $this->message = 'Игра началась! Ваш ход.';
        $this->autoPlaceComputerShips();
    }

    public function resetGame()
    {
        $this->phase = self::PHASE_SETUP;
        $this->playerBoard = [];
        $this->computerBoard = [];
        $this->playerShips = [];
        $this->computerShips = [];
        $this->message = '';
        $this->playerTurn = true;
        $this->initializeBoards();
    }
}