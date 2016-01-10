<?php

namespace app\commands;

use app\components\Mandrill;
use app\models\Antrag;
use yii\console\Controller;

class NotificationController extends Controller
{

    public $defaultAction = 'email';

    /**
     * E-Mail-Benachrichtigungen überfälliger Anträge verschicken
     */
    public function actionEmail()
    {
        /** @var Antrag[] $abgelaufen */
        $abgelaufen = [];
        /** @var Antrag[] $verlaengert */
        $verlaengert = [];

        $abgelaufenStr = $verlaengertStr = '';

        /** @var Antrag[] $antraege */
        $antraege = Antrag::find()->where(Antrag::getAbgelaufenSql())->all();
        foreach ($antraege as $antrag) {
            $abgelaufen[] = $antrag;
            if ($abgelaufenStr == '') {
                $abgelaufenStr = "Die Bearbeitungsfrist folgender Anträge ist abgelaufen:\n\n";
            }
            $abgelaufenStr .= "- " . str_replace("\n", "", $antrag->titel) . "\n";
            $abgelaufenStr .= "  https://www.muenchen-transparent.de/antraege/" . $antrag->ris_id . "\n";
            $abgelaufenStr .= "  Status: " . $antrag->status . "\n";
            $abgelaufenStr .= "  Gestellt am: " . $antrag->gestellt_am . "\n";
            $abgelaufenStr .= "  Ursprüngliche Frist: " . $antrag->bearbeitungsfrist . "\n";
            if ($antrag->fristverlaengerung) {
                $abgelaufenStr .= "  Fristverlängerung: " . $antrag->fristverlaengerung . "\n";
            }
            $abgelaufenStr .= "\n";
        }

        $verlaengertSql = 'fristverlaengerung IS NOT NULL AND fristverlaengerung_benachrichtigung IS NULL';
        $verlaengertSql .= ' AND status != "erledigt"';
        /** @var Antrag[] $antraege */
        $antraege = Antrag::find()->where($verlaengertSql)->all();
        foreach ($antraege as $antrag) {
            $verlaengert[] = $antrag;
            if ($verlaengertStr == '') {
                $verlaengertStr = "Die Bearbeitungsfrist folgender Anträge wurde verlängert:\n\n";
            }
            $verlaengertStr .= "- " . str_replace("\n", "", $antrag->titel) . "\n";
            $verlaengertStr .= "  https://www.muenchen-transparent.de/antraege/" . $antrag->ris_id . "\n";
            $verlaengertStr .= "  Status: " . $antrag->status . "\n";
            $verlaengertStr .= "  Gestellt am: " . $antrag->gestellt_am . "\n";
            $verlaengertStr .= "  Ursprüngliche Frist: " . $antrag->bearbeitungsfrist . "\n";
            $verlaengertStr .= "  Neue (verlängerte) Frist: " . $antrag->fristverlaengerung . "\n\n";
        }

        if (count($abgelaufen) == 0 && count($verlaengert) == 0) {
            return;
        }

        $mail = "Hallo,\n\n" . $abgelaufenStr . $verlaengertStr;
        $mail .= "\n\nMit freundlichen Grüßen,\n  Der nette grüne Stadtrats-Roboter";

        foreach (\Yii::$app->params['mailTo'] as $mailTo) {
            Mandrill::sendWithLog($mailTo, 'Verspätungsalarm: Stadtrats-Anträge', $mail);
        }
        echo $mail; // @TODO


        foreach ($abgelaufen as $antrag) {
            $antrag->bearbeitungsfrist_benachrichtigung = date('Y-m-d H:i:s');
            $antrag->save();
        }
        foreach ($verlaengert as $antrag) {
            $antrag->fristverlaengerung_benachrichtigung = date('Y-m-d H:i:s');
            $antrag->save();
        }
    }
}