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
        $parts = explode(",", $stadtraetin->name);
        $name = (count($parts) === 2 ? $parts[1] . ' ' . $parts[0] : $stadtraetin->name);
        $stadtraetinnen[] = '<span class="nicht-fraktion">' . Html::encode($name) . '</span>';
    }
}
usort($stadtraetinnen, function ($name1, $name2) {
    return strnatcasecmp($name1, $name2);
});
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
$row_classes[] = 'status_' . $antrag->getStatusId();

$target     = Url::toRoute(['site/saveantrag', 'antrag_id' => $antrag->id]);
$del_target = Url::toRoute(['site/delantrag']);
if ($antrag->typ == 'Ergaenzungsantrag') {
    $typ_name = 'Ergänzungsantrag';
} elseif ($antrag->typ == 'Aenderungsantrag') {
    $typ_name = 'Änderungsantrag';
} else {
    $typ_name = $antrag->typ;
}

/** @var \app\models\Dokument[] $dokumente */
$dokumente        = [];
$originaldokument = null;
foreach ($antrag->dokumente as $dokument) {
    if (in_array($dokument->titel, ['B90GrüneRL - Antrag', 'Originalantrag'])) {
        $originaldokument = $dokument;
    } else {
        $dokumente[] = $dokument;
    }
}
?>
<li class="<?= implode(' ', $row_classes) ?> row"
    data-antrag-id="<?= $antrag->id ?>" data-target="<?= Html::encode($target) ?>">
    <div class="titelRow">
        <span class="glyphicon glyphicon-chevron-right"></span>
        <a href="<?= Html::encode($link) ?>" target="_blank" class="title"><?= Html::encode($antrag->titel) ?></a>
        <?php
        if ($originaldokument) {
            echo Html::a('<span class="glyphicon glyphicon-file"></span>', $originaldokument->url, ['class' => 'dokumentLink']);
        }
        if (count($stadtraetinnen) > 0) { ?>
            <div class="titleAdd">
                <span class="typ"><?= Html::encode($typ_name) ?></span> von
                <span class="stadtraetinnen"><?= implode(', ', $stadtraetinnen) ?></span>
            </div>
        <?php } ?>
    </div>
    <div class="body">
        <div class="antragsdatumCol col-md-2">
            <dl>
                <dt>Antrag:</dt>
                <dd><?= Antrag::formatDate($antrag->gestellt_am) ?></dd>
                <br>
                <dt>Frist:</dt>
                <dd><?= Antrag::formatDate($antrag->bearbeitungsfrist) ?></dd>
                <?php if ($antrag->fristverlaengerung) { ?>
                    <br>
                    <dt class="verlaengert">Verlängert:</dt>
                    <dd><?= Antrag::formatDate($antrag->fristverlaengerung) ?></dd>
                <?php }
                if ($antrag->erledigt_am) { ?>
                    <br>
                    <dt class="verlaengert">Erledigt:</dt>
                    <dd><?= Antrag::formatDate($antrag->erledigt_am) ?></dd>
                <?php }
                ?>
            </dl>
        </div>
        <div class="dokumentStatusCol col-md-2">
            <div class="status">
                <label class="abgeschlossen_override">
                    <span>Abgeschlossen</span>
                    <input type="checkbox" name="abgeschlossen" <?php if ($antrag->istErledigt()) {
                        echo 'checked';
                    } ?>>
                </label>
                <?php
                if ($antrag->status_override != '' && $antrag->status != $antrag->status_override) {
                    echo Html::encode($antrag->status_override) . ' <span class="overridden">' . Html::encode($antrag->status) . '</span>';
                } else {
                    echo Html::encode($antrag->status);
                }
                ?>
            </div>
            <ul class="dokumentliste">
                <?php
                foreach ($dokumente as $dokument) {
                    echo '<li><span class="glyphicon glyphicon-file"></span> ';
                    $titel = ($dokument->titel ? $dokument->titel : '');
                    echo Html::a($titel, $dokument->url);
                    echo ' (' . \app\components\HtmlTools::formatDate($dokument->datum) . ')';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
        <div class="notizCol col-md-3 col-md-offset-1">
        <textarea placeholder="Notiz, aktueller Stand" name="notiz" class="form-control"><?php
            echo Html::encode($antrag->notiz);
            ?></textarea>
        </div>
        <div class="tagsCol col-md-3">
            <label>Themen:</label>
            <ul class="tagsList">
                <?php
                foreach ($tags as $tag) {
                    ?>
                    <li class="tag" data-tag="<?= Html::encode($tag) ?>">
                        <span class="name"><?= Html::encode($tag) ?></span>
                        <a href="#" class="delete">🗑</a>
                    </li>
                <?php
                }
                ?>
                <li class="neu">
                    <select size="1" autocomplete="off" title="Thema">
                        <option value="">+ Neu</option>
                        <?php
                        foreach (Antrag::$THEMEN as $thema) {
                            echo '<option value="' . Html::encode($thema) . '">' . Html::encode($thema) . '</option>';
                        }
                        ?>
                    </select>
                </li>
            </ul>
        </div>
        <div class="aktionCol col-md-1">
            <br>
            <?php if ($antrag->ris_id === null) { ?>
                <button class="btn btn-sm btn-danger del-button" type="button" data-target="<?= Html::encode($del_target) ?>">
                    <span class="glyphicon glyphicon-trash"></span>
                </button>
            <?php } ?>
            <button class="btn btn-default btn-sm save-button" type="button">
                <span class="glyphicon glyphicon-ok"></span>
            </button>
            <div class="saved hidden">
                <span class="glyphicon glyphicon-ok-sign" style="font-size: 1.5em;"></span>
                Gespei&shy;chert
            </div>
        </div>
    </div>
</li>
