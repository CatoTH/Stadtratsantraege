<?php

namespace app\controllers;

use app\models\Antrag;
use app\models\Tag;
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

        if ( ! isset($_POST['antrag'])) {
            return json_encode(['error' => 'no data']);
        }

        return json_encode($_POST['antrag']);

        $antrag        = new Antrag();
        $antrag->titel = $data['titel'];
    }

    /**
     * @param int $antrag_id
     *
     * @return string
     */

    public function actionSaveantrag($antrag_id)
    {
        \yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        \yii::$app->response->headers->add('Content-Type', 'application/json');

        /** @var Antrag $antrag */
        $antrag = Antrag::findOne($antrag_id);
        if (!$antrag) {
            return json_encode(['error' => 'Antrag nicht gefunden.']);
        }

        $antrag->notiz = $_POST['antrag']['notiz'];
        if (!$antrag->save()) {
            return json_encode(['error' => 'Es ist ein (seltsamer) Fehler beim Speichern aufgetreten.']);
        }

        foreach ($antrag->tags as $tag) {
            $antrag->unlink('tags', $tag, true);
        }
        $tags = explode(',', $_POST['antrag']['tags']);
        foreach ($tags as $tagName) {
            $tag = Tag::findOne(['name' => $tagName]);
            if (!$tag) {
                $tag = new Tag();
                $tag->name = $tagName;
                $tag->save();
            }
            $antrag->link('tags', $tag);
        }

        $row = $this->renderPartial('index_antrag_row', ['antrag' => $antrag]);

        return json_encode(['success' => 1, 'content' => $row]);
    }


    /**
     * @return string
     */
    public function actionError()
    {
        return "Fehler: ";
    }
}