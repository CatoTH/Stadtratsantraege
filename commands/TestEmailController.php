<?php

namespace app\commands;

use app\components\HtmlTools;
use app\components\Mailjet;
use yii\console\Controller;

class TestEmailController extends Controller
{
    public $defaultAction = 'email';

    public function actionTest(string $to) {
        Mailjet::sendWithLog($to, 'Stadtratsanträge: Test-E-Mail', 'Hallo 🌏');
    }
}
