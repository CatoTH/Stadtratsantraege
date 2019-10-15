<?php

use app\models\Stadtraetin;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<li class="adder-row hidden" data-target="<?= Html::encode(Url::toRoute('site/addantrag')) ?>">
    <div class="titelRow">
        <div class="btn-group selectlist typen-input" data-resize="auto" data-initialize="selectlist">
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
                <span class="selected-label">&nbsp;</span>
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <?php foreach (\app\models\Antrag::$TYPEN as $id => $name) {
                    echo '<li data-value="' . Html::encode($id) . '"><a href="#">' . Html::encode($name) . '</a></li>';
                } ?>
            </ul>
            <input class="hidden hidden-field" name="typ" readonly="readonly" aria-hidden="true" type="text">
        </div>

        <input name="titel" type="text" class="titel-input form-control" title="Titel" placeholder="Titel">
    </div>
    <div class="body">
        <div class="antragsdatumCol col-md-2">
            <div class="datum-label">Antrag:</div>
            <div class="form-group" style="float: left; width: 135px;">
                <div class='input-group date antrag_datum'>
                    <input type='text' class="form-control" title="Antragsdatum" name="gestellt_am">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>

            <br>
            <div class="datum-label">Frist:</div>
            <div class="form-group" style="float: left; width: 135px;">
                <div class='input-group date antrag_datum'>
                    <input type='text' class="form-control" title="Frist" name="bearbeitungsfrist">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="dokumentStatusCol col-md-3">
            <div class="btn-group selectlist" data-resize="auto" data-initialize="selectlist">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
                    <span class="selected-label">&nbsp;</span>
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach (\app\models\Antrag::$STATI as $id => $name) {
                        echo '<li data-value="' . Html::encode($id) . '"><a href="#">' . Html::encode($name) . '</a></li>';
                    } ?>
                </ul>
                <input class="hidden hidden-field" name="status" readonly="readonly" aria-hidden="true" type="text">
            </div>
            <br>

            <?php foreach (Stadtraetin::alleFraktionsmitglieder() as $stadtraetin) {
                $name = explode(' ', str_replace('Dr. ', '', $stadtraetin->name));
                ?>
                <label style="font-weight: normal;">
                    <input type="checkbox" name="neu[stadtraetIn][]" value="<?= $stadtraetin->id ?>">
                    <?= Html::encode($name[0]) ?>
                </label>
            <?php } ?>
        </div>

        <div class="notizCol col-md-3">
            <textarea placeholder="Notiz, aktueller Stand" name="notiz" class="form-control"></textarea>
        </div>
        <div class="tagsCol col-md-3">
            <input type="text" value="" class="entertags" title="Themen">
        </div>
        <div class="aktionCol col-md-1">
            <br>
            <button class="btn btn-default btn-sm save-button" type="button">
                <span class="glyphicon glyphicon-ok"></span>
            </button>
        </div>
    </div>
</li>
