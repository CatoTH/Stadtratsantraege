<?php

use app\models\Stadtraetin;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<tr class="adder-row hidden" data-target="<?= Html::encode(Url::toRoute('site/addantrag')) ?>">
    <td class="title">
        <div class="btn-group selectlist" data-resize="auto" data-initialize="selectlist">
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
        <input name="titel" type="text" class="form-control" title="Titel" placeholder="Titel">
    </td>
    <td class="antragstellerin">
        <?php foreach (Stadtraetin::alleFraktionsmitglieder() as $stadtraetin) {
            $name = explode(' ', str_replace('Dr. ', '', $stadtraetin->name));
            ?>
            <label style="font-weight: normal;">
                <input type="checkbox" name="neu[stadtraetIn][]" value="<?= $stadtraetin->id ?>">
                <?= Html::encode($name[0]) ?>
            </label>
        <? } ?>
    </td>
    <td class="antragsdatum">
        <div class="datum-label">Antrag:</div>
        <div class="form-group" style="float: left; width: 135px;">
            <div class='input-group date antrag_datum'>
                <input type='text' class="form-control" title="Antragsdatum" name="erstellt_am">
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
    </td>
    <td class="status">
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
        <textarea placeholder="Notiz, aktueller Stand" name="notiz" class="form-control"></textarea>
    </td>
    <td class="themen">
        <input type="text" value="" class="entertags" title="Themen">
    </td>
    <td class="aktion">
        <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus-sign"></span></button>
    </td>
</tr>
