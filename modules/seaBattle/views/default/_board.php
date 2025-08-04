<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\seaBattle\models\Cell;
?>

<div class="battle-board">
    <table class="table table-bordered">
        <tr>
            <th></th>
            <?php for ($x = 0; $x < $game->boardSize; $x++): ?>
                <th><?= $x ?></th>
            <?php endfor; ?>
        </tr>
        
        <?php for ($y = 0; $y < $game->boardSize; $y++): ?>
            <tr>
                <th><?= $y ?></th>
                <?php for ($x = 0; $x < $game->boardSize; $x++): ?>
                    <?php $cell = $board[$x][$y]; ?>
                    <td class="
                        <?= $cell->state === Cell::STATE_EMPTY ? 'empty' : '' ?>
                        <?= $cell->state === Cell::STATE_SHIP && $showShips ? 'ship' : '' ?>
                        <?= $cell->state === Cell::STATE_MISS ? 'miss' : '' ?>
                        <?= $cell->state === Cell::STATE_HIT ? 'hit' : '' ?>
                    ">
                        <?php if ($clickable && $cell->state === Cell::STATE_EMPTY): ?>
                            <?php $form = ActiveForm::begin(); ?>
                                <input type="hidden" name="action" value="shoot">
                                <input type="hidden" name="x" value="<?= $x ?>">
                                <input type="hidden" name="y" value="<?= $y ?>">
                                <button type="submit" class="btn btn-xs btn-transparent"></button>
                            <?php ActiveForm::end(); ?>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>
</div>