<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$shipsToPlace = [
    4 => 1,
    3 => 2,
    2 => 3,
    1 => 4,
];
?>

<div class="setup-phase">
    <h2>Расстановка кораблей</h2>
    
    <div class="row">
        <div class="col-md-6">
            <h3>Ваше поле</h3>
            <?= $this->render('_board', [
                'game' => $game,
                'board' => $game->playerBoard,
                'showShips' => true,
                'clickable' => false,
            ]) ?>
        </div>
        
        <div class="col-md-6">
            <h3>Разместить корабль</h3>
            
            <div class="ship-placement">
                <?php foreach ($shipsToPlace as $size => $count): ?>
                    <?php for ($i = 0; $i < $count; $i++): ?>
                        <div class="ship-option">
                            <?php $form = ActiveForm::begin(); ?>
                                <input type="hidden" name="action" value="place_ship">
                                <input type="hidden" name="size" value="<?= $size ?>">
                                
                                <div class="form-group">
                                    <label>X (0-9):</label>
                                    <input type="number" name="x" min="0" max="9" required class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Y (0-9):</label>
                                    <input type="number" name="y" min="0" max="9" required class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="horizontal" checked> Горизонтально
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    Разместить корабль (<?= $size ?>)
                                </button>
                            <?php ActiveForm::end(); ?>
                        </div>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </div>
            
            <?php $form = ActiveForm::begin(); ?>
                <input type="hidden" name="action" value="start_game">
                <button type="submit" class="btn btn-success">Начать игру</button>
            <?php ActiveForm::end(); ?>
            
            <?php $form = ActiveForm::begin(); ?>
                <input type="hidden" name="action" value="reset_game">
                <button type="submit" class="btn btn-danger">Сбросить игру</button>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>