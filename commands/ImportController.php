<?php

namespace app\commands;

use app\models\Antrag;
use app\models\Stadtraetin;
use app\models\Tag;
use yii\console\Controller;

class ImportController extends Controller
{

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
                }
                if ($antrag->fristverlaengerung != $antragData['fristverlaengerung']) {
                    $antrag->notiz = trim($antrag->notiz) . "\n";
                    $antrag->notiz .= date("d.m.Y.") . ": Fristverlängerung: " .
                                      $antrag->fristverlaengerung . " -> " . $antragData['fristverlaengerung'];
                }
                if ($antrag->gestellt_am != $antragData['gestellt_am']) {
                    $antrag->notiz = trim($antrag->notiz) . "\n";
                    $antrag->notiz .= date("d.m.Y.") . ": Gestellt am: " .
                                      $antrag->gestellt_am . " -> " . $antragData['gestellt_am'];
                }
            }
            $antrag->titel              = $antragData['betreff'];
            $antrag->gestellt_am        = $antragData['gestellt_am'];
            $antrag->bearbeitungsfrist  = $antragData['bearbeitungsfrist'];
            $antrag->fristverlaengerung = $antragData['fristverlaengerung'];
            $antrag->status             = $antragData['status'];
            $antrag->save();
        }
    }

    /**
     * Test
     */
    public function actionTest()
    {
        $antrag                     = new Antrag();
        $antrag->ris_id             = null;
        $antrag->titel              = 'Test';
        $antrag->bearbeitungsfrist  = date('Y-m-d');
        $antrag->fristverlaengerung = date('Y-m-d');
        $antrag->benachrichtigung   = date('Y-m-d');
        $antrag->gestellt_am        = date('Y-m-d');
        $antrag->status             = 'Gestellt';
        $antrag->notiz              = 'Bla';
        $antrag->save();

        $tag            = new Tag();
        $tag->tag       = 'Test';
        $tag->antrag_id = $antrag->id;
        $tag->save();

        $stadtraetin         = new Stadtraetin();
        $stadtraetin->name   = 'Sabine Nallinger';
        $stadtraetin->ris_id = 1418551;
        $stadtraetin->save();

        $antrag->link('stadtraetinnen', $stadtraetin);
    }
}