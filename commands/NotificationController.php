<?php

namespace app\commands;

use app\models\Antrag;
use app\models\Stadtraetin;
use app\models\Tag;
use yii\console\Controller;

class NotificationController extends Controller
{

    public $defaultAction = 'email';

    /**
     * E-Mail-Benachrichtigungen überfälliger Anträge verschicken
     */
    public function actionEmail()
    {
        $abgelaufen = $verlaengert = [];

        $abgelaufenSql = 'bearbeitungsfrist <= CURRENT_DATE() ' .
                         'AND (fristverlaengerung IS NULL OR fristverlaengerung <= CURRENT_DATE()) ' .
                         'AND status != "erledigt" ' .
                         'AND bearbeitungsfrist_benachrichtigung IS NULL';
        /** @var Antrag[] $antraege */
        $antraege = Antrag::find()->where($abgelaufenSql)->all();
        foreach ($antraege as $antrag) {
            $abgelaufen[] = $antrag;
        }

        $verlaengertSql = 'fristverlaengerung IS NOT NULL AND fristverlaengerung_benachrichtigung IS NULL';
        /** @var Antrag[] $antraege */
        $antraege = Antrag::find()->where($verlaengertSql)->all();
        foreach ($antraege as $antrag) {
            $verlaengert[] = $antrag;
        }

        var_dump($verlaengert);
    }
}