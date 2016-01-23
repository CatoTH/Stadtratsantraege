<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var int $sort
 * @var int $sort_desc
 * @var int $zeitraum_jahre
 * @var int $aenderungsantraege
 */

echo '<div class="zeitraum">';
echo '<strong>Zeitraum:</strong> &nbsp; ';
if ($zeitraum_jahre == 2) {
    echo '<strong>2 Jahre</strong>';
} else {
    echo Html::a('2 Jahre', Url::toRoute(['site/index', 'sort' => $sort, 'sort_desc' => $sort_desc, 'zeitraum_jahre' => 2, 'aenderungsantraege' => $aenderungsantraege]));
}
echo ' &nbsp; ';
if ($zeitraum_jahre == 5) {
    echo '<strong>5 Jahre</strong>';
} else {
    echo Html::a('5 Jahre', Url::toRoute(['site/index', 'sort' => $sort, 'sort_desc' => $sort_desc, 'zeitraum_jahre' => 5, 'aenderungsantraege' => $aenderungsantraege]));
}
echo ' &nbsp; ';
if ($zeitraum_jahre == 10) {
    echo '<strong>10 Jahre</strong>';
} else {
    echo Html::a('10 Jahre', Url::toRoute(['site/index', 'sort' => $sort, 'sort_desc' => $sort_desc, 'zeitraum_jahre' => 10, 'aenderungsantraege' => $aenderungsantraege]));
}
echo '</div>';


echo '<div class="aes">';
echo '<strong>Änderungs-/Ergänzungsanträge:</strong> &nbsp; ';
if ($aenderungsantraege) {
    echo '<strong>Ja</strong>';
} else {
    echo Html::a('Ja', Url::toRoute(['site/index', 'sort' => $sort, 'sort_desc' => $sort_desc, 'zeitraum_jahre' => $zeitraum_jahre, 'aenderungsantraege' => 1]));
}
echo ' &nbsp; ';
if (!$aenderungsantraege) {
    echo '<strong>Nein</strong>';
} else {
    echo Html::a('Nein', Url::toRoute(['site/index', 'sort' => $sort, 'sort_desc' => $sort_desc, 'zeitraum_jahre' => $zeitraum_jahre, 'aenderungsantraege' => 0]));
}
echo '</div>';