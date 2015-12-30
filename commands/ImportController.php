<?php

namespace app\commands;

use app\models\Antrag;
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
        /** @var Antrag $antrag */
        $antrag = Antrag::findOne(['ris_id' => $antragData['id']]);
        if ( ! $antrag) {
            $antrag         = new Antrag();
            $antrag->ris_id = $antragData['id'];
        } else {
            if ($antrag->status != $antragData['status']) {
                $antrag->notiz = trim($antrag->notiz) . "\n";
                $antrag->notiz .= date("d.m.Y.") . ": Status: " . $antrag->status . " -> " . $antragData['status'];
            }
            if ($antrag->bearbeitungsfrist != $antragData['bearbeitungsfrist']) {
                $antrag->notiz = trim($antrag->notiz) . "\n";
                $antrag->notiz .= date("d.m.Y.") . ": Bearbeitungsfrist: " .
                                  $antrag->bearbeitungsfrist . " -> " . $antragData['bearbeitungsfrist'];
                $antrag->bearbeitungsfrist_benachrichtigung = null;
            }
            if ($antrag->fristverlaengerung != $antragData['fristverlaengerung']) {
                $antrag->notiz = trim($antrag->notiz) . "\n";
                $antrag->notiz .= date("d.m.Y.") . ": Fristverlängerung: " .
                                  $antrag->fristverlaengerung . " -> " . $antragData['fristverlaengerung'];
                $antrag->fristverlaengerung_benachrichtigung = null;
            }
            if ($antrag->gestellt_am != $antragData['gestellt_am']) {
                $antrag->notiz = trim($antrag->notiz) . "\n";
                $antrag->notiz .= date("d.m.Y.") . ": Gestellt am: " .
                                  $antrag->gestellt_am . " -> " . $antragData['gestellt_am'];
            }
        }
        $antrag->titel              = mb_substr($antragData['betreff'], 0, 200);
        $antrag->typ                = $antragData['typ'];
        $antrag->antrags_nr         = $antragData['antrags_nr'];
        $antrag->gestellt_am        = $antragData['gestellt_am'];
        $antrag->bearbeitungsfrist  = $antragData['bearbeitungsfrist'];
        $antrag->fristverlaengerung = $antragData['fristverlaengerung'];
        $antrag->status             = $antragData['status'];
        $antrag->notiz              = '';
        $antrag->save();

        foreach ($antrag->stadtraetinnen as $stadtraetin) {
            $antrag->unlink('stadtraetinnen', $stadtraetin, true);
        }

        foreach ($antragData['stadtraetInnen'] as $stadtraetInData) {
            $stadtraetin = Stadtraetin::findOne(['ris_id' => $stadtraetInData['id']]);
            if ( ! $stadtraetin) {
                $stadtraetin         = new Stadtraetin();
                $stadtraetin->ris_id = $stadtraetInData['id'];
                $stadtraetin->name   = $stadtraetInData['name'];
                $stadtraetin->save();
            }
            $antrag->link('stadtraetinnen', $stadtraetin);
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
            return -1;
        }
        $data = json_decode($data, true);
        foreach ($data as $antragData) {
            $this->importDatarow($antragData);
        }
    }
}