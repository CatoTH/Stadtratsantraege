<?php

use app\models\Antrag;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var Antrag $antrag */

$row_classes    = [];
$stadtraetinnen = [];
$tags           = [];
$link           = 'https://www.muenchen-transparent.de/antraege/' . $antrag->ris_id;
foreach ($antrag->initiatorinnen as $stadtraetin) {
    $row_classes[] = 'stadtraetin_' . $stadtraetin->id;
    if ($stadtraetin->fraktionsmitglied) {
        $namen            = explode(' ', str_replace('Dr. ', '', $stadtraetin->name));
        $stadtraetinnen[] = Html::encode($namen[0]);
    } else {
        $stadtraetinnen[] = '<span class="nicht-fraktion">' . Html::encode($stadtraetin->name) . '</span>';
    }
}
foreach ($antrag->tags as $tag) {
    $tags[]        = $tag->name;
    $row_classes[] = 'tag_' . $tag->id;
}
$row_classes[] = 'typ_' . $antrag->getTypId();
if ($antrag->istAbgelaufen()) {
    $row_classes[] = 'abgelaufen';
}
if ($antrag->fristverlaengerung) {
    $row_classes[] = 'verlaengert';
}
$target     = Url::toRoute(['site/saveantrag', 'antrag_id' => $antrag->id]);
$del_target = Url::toRoute(['site/delantrag']);
if ($antrag->typ == 'Ergaenzungsantrag') {
    $typ_name = 'Ergänzungsantrag';
} elseif ($antrag->typ == 'Aenderungsantrag') {
    $typ_name = 'Änderungsantrag';
} else {
    $typ_name = $antrag->typ;
}
?>
<tr class="<?= implode(' ', $row_classes) ?>"
    data-antrag-id="<?= $antrag->id ?>" data-target="<?= Html::encode($target) ?>">
    <td class="titelRow">
        <div class="typ"><?= Html::encode($typ_name) ?></div>
        <a href="<?= Html::encode($link) ?>" target="_blank"><?= Html::encode($antrag->titel) ?></a>

    </td>
    <td class="antragstellerin"><br><?= implode(', ', $stadtraetinnen) ?></td>
    <td class="antragsdatum"><br>
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
            <?php }
            if ($antrag->erledigt_am) { ?>
                <dt class="verlaengert">Erledigt:</dt>
                <dd><?= Antrag::formatDate($antrag->erledigt_am) ?></dd>
            <?php }
            ?>
        </dl>
    </td>
    <td>
        <?= Html::encode($antrag->status) ?>
        <br>
        <textarea placeholder="Notiz, aktueller Stand" name="notiz" class="form-control"><?
            echo Html::encode($antrag->notiz);
            ?></textarea>
    </td>
    <td class="tags">
        <br>
        <input type="text" value="<?= Html::encode(implode(',', $tags)) ?>" class="entertags" title="Themen">
    </td>
    <td class="aktion">
        <br>
        <button class="btn btn-default btn-sm save-button" type="button">
            <span class="glyphicon glyphicon-ok"></span>
        </button>
        <div class="saved hidden">
            <span class="glyphicon glyphicon-ok-sign" style="font-size: 1.5em;"></span>
            Gespei&shy;chert
        </div>
        <?php if ($antrag->ris_id === null) { ?>
            <button class="btn btn-xs del-button" type="button" data-target="<?= Html::encode($del_target) ?>">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        <?php } ?>
    </td>
</tr>
