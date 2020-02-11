<?php
use yii\helpers\Html;

?>
<div class="filter">
    <label class="hidden" for="filter_titel"></label>
    <input type="text" name="filter_titel" id="filter_titel" class="form-control filter_titel"
           placeholder="Titel" autocomplete="off">
</div>

<div class="filter">
    <div class="btn-group selectlist filter_typ" data-resize="auto" data-initialize="selectlist">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
            <span class="selected-label">&nbsp;</span>
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li data-value="-1" style="font-style: italic;"><a href="#">Alle Typen</a></li>
            <?php
            foreach (\app\models\Antrag::$TYPEN as $typ_id => $typ_name) {
                echo '<li data-value="' . Html::encode($typ_id) . '"><a href="#">' .
                     Html::encode($typ_name) . '</a></li>';
            } ?>
        </ul>
        <input class="hidden hidden-field" name="filter_typ" readonly="readonly" aria-hidden="true"
               type="text"/>
    </div>
</div>

<div class="filter">
    <div class="btn-group selectlist filter_initiator" data-resize="auto" data-initialize="selectlist">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
            <span class="selected-label">&nbsp;</span>
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li data-value="-1" style="font-style: italic;"><a href="#">Alle Stadträt*innen</a></li>
            <?php
            foreach (\app\models\Stadtraetin::alleFraktionsmitglieder() as $stadtraetin) {
                echo '<li data-value="' . Html::encode($stadtraetin->id) . '"><a href="#">' .
                     Html::encode($stadtraetin->name) . '</a></li>';
            } ?>
        </ul>
        <input class="hidden hidden-field" name="filter_initiator" readonly="readonly" aria-hidden="true"
               type="text"/>
    </div>
</div>

<div class="filter">
    <div class="btn-group selectlist filter_thema" data-resize="auto" data-initialize="selectlist">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
            <span class="selected-label">&nbsp;</span>
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li data-value="-1" style="font-style: italic;"><a href="#">Alle Themen</a></li>
            <?php foreach (\app\models\Tag::find()->orderBy('name')->all() as $tag) {
                echo '<li data-value="' . $tag->id . '"><a href="#">' . Html::encode($tag->name) . '</a></li>';
            } ?>
        </ul>
        <input class="hidden hidden-field" name="filter_thema" readonly="readonly" aria-hidden="true"
               type="text"/>
    </div>
</div>

<div class="filter">
    <div class="btn-group selectlist filter_status" data-resize="auto" data-initialize="selectlist">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
            <span class="selected-label">&nbsp;</span>
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li data-value="-1" style="font-style: italic;"><a href="#">Alle Stati</a></li>
            <?php foreach (\app\models\Antrag::$STATI as $id => $name) {
                echo '<li data-value="' . $id . '"><a href="#">' . Html::encode($name) . '</a></li>';
            } ?>
        </ul>
        <input class="hidden hidden-field" name="filter_status" readonly="readonly" aria-hidden="true"
               type="text"/>
    </div>
</div>

<div class="filter">
    <div class="checkbox">
        <label class="checkbox-custom filter_abgelaufen" data-initialize="checkbox">
            <input class="sr-only" type="checkbox" name="filter_abgelaufen" value="">
            <span class="checkbox-label">Frist abgelaufen</span>
        </label>
    </div>
</div>
