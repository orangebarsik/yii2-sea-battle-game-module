<?php

namespace app\modules\seaBattle\controllers;

use Yii;
use yii\web\Controller;
use app\modules\seaBattle\models\Game;

/**
 * Default controller for the `seaBattle` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $game = Game::loadFromSession();
        
        if (Yii::$app->request->isPost) {
            if ($game->phase === Game::PHASE_SETUP) {
                if (Yii::$app->request->post('action') === 'place_ship') {
                    $x = Yii::$app->request->post('x');
                    $y = Yii::$app->request->post('y');
                    $size = Yii::$app->request->post('size');
                    $horizontal = Yii::$app->request->post('horizontal');
                    
                    if ($game->placeShip($x, $y, $size, $horizontal, true)) {
                        $game->message = "Корабль размером $size размещен!";
                    } else {
                        $game->message = "Не удалось разместить корабль здесь!";
                    }
                } elseif (Yii::$app->request->post('action') === 'start_game') {
                    $game->startGame();
                } elseif (Yii::$app->request->post('action') === 'reset_game') {
                    $game->resetGame();
                }
            } elseif ($game->phase === Game::PHASE_BATTLE) {
                if (Yii::$app->request->post('action') === 'shoot') {
                    $x = Yii::$app->request->post('x');
                    $y = Yii::$app->request->post('y');
                    $game->playerShoot($x, $y);
                } elseif (Yii::$app->request->post('action') === 'reset_game') {
                    $game->resetGame();
                }
            } elseif ($game->phase === Game::PHASE_END) {
                if (Yii::$app->request->post('action') === 'reset_game') {
                    $game->resetGame();
                }
            }
            
            $game->saveToSession();
            return $this->refresh();
        }
        
        return $this->render('index', [
            'game' => $game,
        ]);
    }
}
