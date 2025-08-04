<?php
use yii\helpers\Html;
use app\modules\seaBattle\models\Game;

$this->title = 'Морской бой';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sea-battle-game">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if ($game->message): ?>
        <div class="alert alert-info"><?= Html::encode($game->message) ?></div>
    <?php endif; ?>
    
    <?php if ($game->phase === Game::PHASE_SETUP): ?>
        <?= $this->render('_setup', ['game' => $game]) ?>
    <?php else: ?>
        <?= $this->render('_game', ['game' => $game]) ?>
    <?php endif; ?>
</div>
<style>
.battle-board {
    margin-bottom: 20px;
}

.battle-board table {
    border-collapse: collapse;
}

.battle-board td {
    width: 30px;
    height: 30px;
    text-align: center;
    vertical-align: middle;
    position: relative;
}

.battle-board .empty {
    background-color: #e6f7ff;
}

.battle-board .ship {
    background-color: #aaa;
}

.battle-board .miss {
    background-color: #fff;
}

.battle-board .miss:after {
    content: "•";
    color: #333;
    font-size: 20px;
    line-height: 20px;
}

.battle-board .hit {
    background-color: #ff9999;
}

.battle-board .hit:after {
    content: "✖";
    color: #cc0000;
    font-size: 20px;
    line-height: 20px;
}

.btn-transparent {
    background: transparent;
    border: none;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    padding: 0;
}

.ship-placement {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.ship-option {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
}
</style>