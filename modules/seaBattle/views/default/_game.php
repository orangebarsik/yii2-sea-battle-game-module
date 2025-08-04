<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\seaBattle\models\Game;
?>

<div class="battle-phase">
    <h2>Фаза боя</h2>
    <p><?= $game->playerTurn ? 'Ваш ход' : 'Ход компьютера' ?></p>
    
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
            <h3>Поле противника</h3>
            <?= $this->render('_board', [
                'game' => $game,
                'board' => $game->computerBoard,
                'showShips' => false,
                'clickable' => $game->playerTurn && $game->phase === Game::PHASE_BATTLE,
            ]) ?>
        </div>
    </div>
    
    <?php if ($game->phase === Game::PHASE_END): ?>
        <div class="game-over">
            <h2>Игра окончена!</h2>
            <?php $form = ActiveForm::begin(); ?>
                <input type="hidden" name="action" value="reset_game">
                <button type="submit" class="btn btn-primary">Новая игра</button>
            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>
    
    <?php $form = ActiveForm::begin(); ?>
        <input type="hidden" name="action" value="reset_game">
        <button type="submit" class="btn btn-danger">Сбросить игру</button>
    <?php ActiveForm::end(); ?>
</div>