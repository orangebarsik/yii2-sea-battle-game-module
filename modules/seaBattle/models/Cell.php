<?php
namespace app\modules\seaBattle\models;

class Cell
{
    const STATE_EMPTY = 0;
    const STATE_SHIP = 1;
    const STATE_MISS = 2;
    const STATE_HIT = 3;

    public $x;
    public $y;
    public $state;

    public function __construct($x, $y, $state = self::STATE_EMPTY)
    {
        $this->x = $x;
        $this->y = $y;
        $this->state = $state;
    }
}