<?php
use app\models\Antrag;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var string $content
 *
 * @var Antrag[] $antraege
 */

$this->title = 'Antragsverwaltung der Grünen Stadtratsfraktion';

$stadtraetinnen_alle = \app\models\Stadtraetin::find()->orderBy('name')->all();

echo Html::beginForm('', 'post', ['class' => 'antrag-form']);
?>
<div id="filterWidget" style="margin-bottom: 20px;">
    <?php echo $this->render('index_filterwidget'); ?>
</div>


<table id="antragsliste">
    <thead>
    <tr>
        <th class="title">Titel</th>
        <th class="antragstellerin">Antragsteller*in</th>
        <th class="antragsdatum">Datum</th>
        <th class="status">Status</th>
        <th class="themen">Themen</th>
        <th class="aktion"></th>
    </tr>
    </thead>
    <tbody>
    <?php
    echo $this->render('index_adder_row', ['stadtraetinnen' => $stadtraetinnen_alle]);
    foreach ($antraege as $antrag) {
        echo $this->render('index_antrag_row', ['antrag' => $antrag]);
    }
    ?>

    </tbody>
</table>

<?php
echo Html::endForm();
