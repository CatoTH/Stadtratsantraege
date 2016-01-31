<?php

namespace app\commands;

use app\components\HtmlTools;
use app\models\Antrag;
use app\models\Dokument;
use app\models\Stadtraetin;
use yii\console\Controller;

class ImportController extends Controller
{

    public $defaultAction = 'import-from-mt';

    /**
     * @param array $antragData
     */
    private function importDatarow($antragData)
    {
        if ($antragData['status'] == 'Sitzungsvorlage') {
            return;
        }
        /** @var Antrag $antrag */
        $antrag = Antrag::findOne(['ris_id' => $antragData['id']]);
        if (!$antrag) {
            $antrag         = new Antrag();
            $antrag->ris_id = $antragData['id'];
            $antrag->notiz  = '';
        } else {
            if ($antrag->status != $antragData['status']) {
                $antrag->notiz = date("d.m.Y.") . ": Status: " . $antrag->status . " -> " . $antragData['status'] . "\n" . trim($antrag->notiz);
            }
            if ($antrag->bearbeitungsfrist != $antragData['bearbeitungsfrist']) {
                $antrag->notiz                              = date("d.m.Y.") . ": Bearbeitungsfrist: " .
                    HtmlTools::formatDate($antrag->bearbeitungsfrist) . " -> " . HtmlTools::formatDate($antragData['bearbeitungsfrist']) .
                    "\n" . trim($antrag->notiz);
                $antrag->bearbeitungsfrist_benachrichtigung = null;
            }
            if ($antrag->fristverlaengerung != $antragData['fristverlaengerung']) {
                $antrag->notiz                               = date("d.m.Y.") . ": Fristverlängerung: " .
                    HtmlTools::formatDate($antragData['fristverlaengerung']) . "\n" . trim($antrag->notiz);
                $antrag->fristverlaengerung_benachrichtigung = null;
            }
            if ($antrag->gestellt_am != $antragData['gestellt_am']) {
                $antrag->notiz = date("d.m.Y.") . ": Gestellt am: " .
                    HtmlTools::formatDate($antrag->gestellt_am) . " -> " . HtmlTools::formatDate($antragData['gestellt_am']) .
                    "\n" . trim($antrag->notiz);
            }
        }
        $antrag->titel              = mb_substr($antragData['betreff'], 0, 200);
        $antrag->typ                = $antragData['typ'];
        $antrag->antrags_nr         = $antragData['antrags_nr'];
        $antrag->gestellt_am        = $antragData['gestellt_am'];
        $antrag->bearbeitungsfrist  = $antragData['bearbeitungsfrist'];
        $antrag->fristverlaengerung = $antragData['fristverlaengerung'];
        $antrag->status             = $antragData['status'];
        $antrag->erledigt_am        = $antragData['erledigt_am'];
        if ($antrag->status == $antrag->status_override) {
            $antrag->status_override = '';
        }
        $antrag->save();

        foreach ($antrag->stadtraetinnen as $stadtraetin) {
            $antrag->unlink('stadtraetinnen', $stadtraetin, true);
        }
        foreach ($antrag->initiatorinnen as $initiatorin) {
            $antrag->unlink('initiatorinnen', $initiatorin, true);
        }

        foreach ($antragData['stadtraetInnen'] as $stadtraetInData) {
            $ris_id      = ($stadtraetInData['id'] > 0 ? $stadtraetInData['id'] : 0);
            $stadtraetin = Stadtraetin::findOne(['ris_id' => $ris_id]);
            if (!$stadtraetin) {
                $stadtraetin         = new Stadtraetin();
                $stadtraetin->ris_id = $stadtraetInData['id'];
                $stadtraetin->name   = $stadtraetInData['name'];
                $stadtraetin->save();
            }
            try {
                $antrag->link('stadtraetinnen', $stadtraetin);
            } catch (\Exception $e) {
            }
        }

        foreach ($antragData['initiatorInnen'] as $initiatorInData) {
            $ris_id      = ($initiatorInData['id'] > 0 ? $initiatorInData['id'] : 0);
            $initiatorin = Stadtraetin::findOne(['ris_id' => $ris_id]);
            if (!$initiatorin) {
                $initiatorin         = new Stadtraetin();
                $initiatorin->ris_id = $initiatorInData['id'];
                $initiatorin->name   = $initiatorInData['name'];
                $initiatorin->save();
            }
            try {
                $antrag->link('initiatorinnen', $initiatorin);
            } catch (\Exception $e) {
            }
        }

        foreach ($antragData['dokumente'] as $dokumentData) {
            $dokument = Dokument::findOne(['dokument_id' => $dokumentData['id']]);
            if (!$dokument) {
                $dokument              = new Dokument();
                $dokument->dokument_id = $dokumentData['id'];
            }
            $dokument->antrag_id = $antrag->id;
            $dokument->titel     = $dokumentData['titel'];
            $dokument->datum     = $dokumentData['datum'];
            $dokument->url       = $dokumentData['pdf'];
            $dokument->save();
        }
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws \Exception
     */
    private function downloadUrl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Stadtrats-Antrags-Tool');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $text = curl_exec($ch);
        if ($text === false) {
            throw new \Exception("Import fehlgeschlagen: keine Daten von MT erhalten.");
        }
        curl_close($ch);

        return $text;
    }

    /**
     * Daten aus einer JSON-Datei importieren
     *
     * @param string $filename
     */
    public function actionImportFromFile($filename)
    {
        $data = file_get_contents($filename);
        $data = json_decode($data, true);
        foreach ($data as $antragData) {
            $this->importDatarow($antragData);
        }
    }

    /**
     * Daten von München Transparent importieren
     */
    public function actionImportFromMt()
    {
        $importUrl = \Yii::$app->params['importUrl'];
        try {
            $data = $this->downloadUrl($importUrl);
        } catch (\Exception $e) {
            $this->stderr($e->getMessage() . "\n\n");
            return;
        }
        $data = json_decode($data, true);
        foreach ($data as $antragData) {
            $this->importDatarow($antragData);
        }
    }
}