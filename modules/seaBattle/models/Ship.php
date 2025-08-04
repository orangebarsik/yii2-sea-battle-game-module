<?php
namespace app\modules\seaBattle\models;

class Ship
{
    public $size;
    public $cells = [];
    public $isSunk = false;

    public function __construct($size)
    {
        $this->size = $size;
    }

    public function addCell(Cell $cell)
    {
        $this->cells[] = $cell;
    }

    public function checkIfSunk()
    {
        foreach ($this->cells as $cell) {
            if ($cell->state !== Cell::STATE_HIT) {
                $this->isSunk = false;
                return false;
            }
        }
        $this->isSunk = true;
        return true;
    }
}