<?php

namespace app\controllers;

use app\models\Antrag;
use yii\web\Controller;

class SiteController extends Controller
{
    public $layout = '@app/views/layouts/main';

    /**
     * @return string
     */
    public function actionIndex()
    {
        $sql      = 'SELECT *, IF (fristverlaengerung > bearbeitungsfrist, fristverlaengerung, bearbeitungsfrist) frist ' .
                    'FROM antraege ORDER BY frist DESC';
        $antraege = Antrag::findBySql($sql)->all();

        return $this->render('index', ['antraege' => $antraege]);
    }

    /**
     * @return string
     */
    public function actionAddantrag()
    {
        \yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        \yii::$app->response->headers->add('Content-Type', 'application/json');

        if (!isset($_POST['antrag'])) {
            return json_encode(['error' => 'no data']);
        }

        return json_encode($data);

        $antrag = new Antrag();
        $antrag->titel = $data['titel'];

    }

    /**
     * @return string
     */
    public function actionError()
    {
        return "Fehler: ";
    }
}