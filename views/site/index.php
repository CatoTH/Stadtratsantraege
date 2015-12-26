<?php
use app\models\Antrag;
use yii\helpers\Html;

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
        $row_classes    = [];
        $stadtraetinnen = [];
        $tags           = [];
        $link           = 'https://www.muenchen-transparent.de/' . $antrag->ris_id; // @TODO
        foreach ($antrag->stadtraetinnen as $stadtraetin) {
            $row_classes[]    = 'stadtraetin_' . $stadtraetin->id;
            $stadtraetinnen[] = $stadtraetin->name;
        }
        foreach ($antrag->tags as $tag) {
            $tags[]        = $tag->name;
            $row_classes[] = 'tag_' . $tag->id;
        }
        $frist      = ($antrag->fristverlaengerung ? $antrag->fristverlaengerung : $antrag->bearbeitungsfrist);
        $abgelaufen = (date('Ymd') <= str_replace('-', '', $frist));
        if ($abgelaufen) {
            $row_classes[] = 'abgelaufen';
        }
        if ($antrag->fristverlaengerung) {
            $row_classes[] = 'verlaengert';
        }
        ?>
        <tr class="<?= implode(' ', $row_classes) ?>" data-antrag-id="<?= $antrag->id ?>">
            <td class="titelRow">
                <a href="<?= Html::encode($link) ?>" target="_blank"><?= Html::encode($antrag->titel) ?></a>
            </td>
            <td><?= Html::encode(implode(', ', $stadtraetinnen)) ?></td>
            <td class="antragsdatum">
                <dl>
                    <dt>Antrag:</dt>
                    <dd><?= Antrag::formatDate($antrag->gestellt_am) ?></dd>
                    <br>
                    <dt>Frist:</dt>
                    <dd><?= Antrag::formatDate($antrag->bearbeitungsfrist) ?></dd>
                    <br>
                    <?php if ($antrag->fristverlaengerung) { ?>
                        <dt class="verlaengert">Verlängert:</dt>
                        <dd><?= Antrag::formatDate($antrag->fristverlaengerung) ?></dd>
                    <?php } ?>
                </dl>
            </td>
            <td>
                <?= Html::encode($antrag->status) ?>
                <br><textarea placeholder="Notiz, aktueller Stand"><?= Html::encode($antrag->notiz) ?></textarea>
            </td>
            <td class="pillboxHolder">
                <input type="text" value="<?= Html::encode(implode(',', $tags)) ?>" class="entertags" title="Themen">
            </td>
            <td>
                <button class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-ok"></span>
                </button>
            </td>
        </tr>
        <?php
    }
    ?>

    </tbody>
</table>

<?php
echo Html::endForm();
