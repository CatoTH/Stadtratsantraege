<?php

namespace app\controllers;

use app\models\Antrag;
use app\models\Stadtraetin;
use app\models\Tag;
use yii\web\Controller;

class SiteController extends Controller
{

    public $layout = '@app/views/layouts/main';

    /**
     * @param int $sort
     * @param int $sort_desc
     * @param int $zeitraum_jahre
     * @param int $aenderungsantraege
     *
     * @return string
     */
    public function actionIndex($sort = Antrag::SORT_DATUM_FRIST, $sort_desc = 1, $zeitraum_jahre = 2, $aenderungsantraege = 0)
    {
        $where = '';
        if (!$aenderungsantraege || $zeitraum_jahre > 0) {
            $where = ' WHERE';
            if ($zeitraum_jahre > 0) {
                $where .= ' gestellt_am > NOW() - INTERVAL ' . intval($zeitraum_jahre) . ' YEAR';
            }
            if ($zeitraum_jahre > 0 && !$aenderungsantraege) {
                $where .= ' AND';
            }
            if (!$aenderungsantraege) {
                $where .= ' typ != "Ergaenzungsantrag" AND typ != "Aenderungsantrag"';
            }
        }


        if ($sort == Antrag::SORT_DATUM_FRIST) {
            $sql = 'SELECT *, IF (fristverlaengerung > bearbeitungsfrist, fristverlaengerung, bearbeitungsfrist) frist FROM antraege' . $where;

            $sql .= ' ORDER BY frist';
            if ($sort_desc) {
                $sql .= ' DESC';
            }
            $antraege = Antrag::findBySql($sql)->all();
        } elseif ($sort == Antrag::SORT_TITEL) {
            $sql = 'SELECT * FROM antraege' . $where . ' ORDER BY titel';
            if ($sort_desc) {
                $sql .= ' DESC';
            }
            $antraege = Antrag::findBySql($sql)->all();
        } elseif ($sort == Antrag::SORT_DATUM_ANTRAG) {
            $sql = 'SELECT * FROM antraege' . $where . ' ORDER BY gestellt_am';
            if ($sort_desc) {
                $sql .= ' DESC';
            }
            $antraege = Antrag::findBySql($sql)->all();
        } elseif ($sort == Antrag::SORT_STATUS) {
            $antraege = Antrag::findBySql('SELECT * FROM antraege' . $where)->all();
            usort($antraege, function (Antrag $antrag1, Antrag $antrag2) use ($sort_desc) {
                $status1 = $antrag1->getStatusId();
                $status2 = $antrag2->getStatusId();
                if ($sort_desc) {
                    return $status2 <=> $status1;
                } else {
                    return $status1 <=> $status2;
                }
            });
        } else {
            return 'Unbekannter Sortierungstyp';
        }

        return $this->render('index', [
            'antraege'           => $antraege,
            'sort'               => $sort,
            'sort_desc'          => $sort_desc,
            'zeitraum_jahre'     => $zeitraum_jahre,
            'aenderungsantraege' => $aenderungsantraege,
        ]);
    }

    /**
     * @return string
     */
    public function actionAddantrag()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        \Yii::$app->response->headers->add('Content-Type', 'application/json');

        if (!isset($_POST['antrag'])) {
            return json_encode(['error' => 'no data']);
        }

        $data = $_POST['antrag'];

        $antrag                     = new Antrag();
        $antrag->titel              = $data['titel'];
        $antrag->typ                = Antrag::$TYPEN[$data['typ']];
        $antrag->bearbeitungsfrist  = (isset($data['bearbeitungsfrist']) && $data['bearbeitungsfrist'] ? $data['bearbeitungsfrist'] : '0000-00-00');
        $antrag->fristverlaengerung = (isset($data['fristverlaengerung']) ? $data['fristverlaengerung'] : null);
        $antrag->gestellt_am        = (isset($data['gestellt_am']) ? $data['gestellt_am'] : null);
        $antrag->status             = Antrag::$STATI[$data['status']];
        $antrag->notiz              = $data['notiz'];
        $antrag->status_override    = '';
        $antrag->antrags_nr         = '';
        $antrag->save();

        foreach ($data['tags'] as $tagName) {
            if ($tagName !== '') {
                $tag = Tag::findOne(['name' => $tagName]);
                if (!$tag) {
                    $tag       = new Tag();
                    $tag->name = $tagName;
                    $tag->save();
                }
                $antrag->link('tags', $tag);
            }
        }

        if (isset($data['stadtraetinnen'])) {
            foreach ($data['stadtraetinnen'] as $stadtraetinId) {
                /** @var Stadtraetin $stadtraetin */
                $stadtraetin = Stadtraetin::findOne($stadtraetinId);
                $antrag->link('stadtraetinnen', $stadtraetin);
            }
        }

        $antrag->refresh();
        $row = $this->renderPartial('index_antrag_row', ['antrag' => $antrag]);

        return json_encode(['success' => 1, 'content' => $row]);
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
        if ($_POST['antrag']['abgeschlossen'] == 1) {
            $antrag->status_override = ($antrag->status == 'erledigt' ? '' : 'erledigt');
        } else {
            $antrag->status_override = ($antrag->status == 'erledigt' ? 'In Bearbeitung' : '');
        }
        if (!$antrag->save()) {
            return json_encode(['error' => 'Es ist ein (seltsamer) Fehler beim Speichern aufgetreten.']);
        }

        foreach ($antrag->tags as $tag) {
            $antrag->unlink('tags', $tag, true);
        }
        if (isset($_POST['antrag']['tags'])) {
            foreach ($_POST['antrag']['tags'] as $tagName) {
                if ($tagName !== '') {
                    $tag = Tag::findOne(['name' => $tagName]);
                    if (!$tag) {
                        $tag       = new Tag();
                        $tag->name = $tagName;
                        $tag->save();
                    }
                    $antrag->link('tags', $tag);
                }
            }
        }

        $row = $this->renderPartial('index_antrag_row', ['antrag' => $antrag]);

        return json_encode(['success' => 1, 'content' => $row]);
    }

    /**
     * return string
     */
    public function actionDelantrag()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        \Yii::$app->response->headers->add('Content-Type', 'application/json');

        /** @var Antrag $antrag */
        $antrag = Antrag::findOne($_POST['antrag_id']);
        foreach ($antrag->stadtraetinnen as $stadtraetin) {
            $antrag->unlink('stadtraetinnen', $stadtraetin, true);
        }
        foreach ($antrag->tags as $tag) {
            $antrag->unlink('tags', $tag, true);
        }
        if ($antrag->delete()) {
            return json_encode(['success' => 1]);
        } else {
            return json_encode(['error' => $antrag->getErrors()]);
        }
    }

    /**
     * @return string
     */
    public function actionError()
    {
        return "Fehler: ";
    }
}
