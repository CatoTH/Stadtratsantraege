<?php

?>
<button style="float: right;" class="btn btn-default eintrag_add_button" type="button">Neuer Eintrag
</button>

<div class="filter">
    <label class="hidden" for="filter_titel"></label>
    <input type="text" name="filter_titel" id="filter_titel" class="form-control filter_titel"
           placeholder="Titel">
</div>

<div class="filter">
    <div class="btn-group selectlist filter_initiator" data-resize="auto" data-initialize="selectlist">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
            <span class="selected-label">&nbsp;</span>
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li data-value="0" style="font-style: italic;"><a href="#">Alle Stadträt*innen</a></li>
            <li data-value="1"><a href="#">Florian Roth</a></li>
            <li data-value="2"><a href="#">Gülseren Demirel</a></li>
            <li data-value="0"><a href="#">...</a></li>
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
            <li data-value="0" style="font-style: italic;"><a href="#">Alle Themen</a></li>
            <li data-value="1"><a href="#">Verkehr</a></li>
            <li data-value="2"><a href="#">Stadtplanung</a></li>
            <li data-value="3"><a href="#">...</a></li>
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
            <li data-value="0" style="font-style: italic;"><a href="#">Alle Stati</a></li>
            <li data-value="1"><a href="#">Abgeschlossen</a></li>
            <li data-value="2"><a href="#">In Bearbeitung</a></li>
            <li data-value="3"><a href="#">Aufgegriffen</a></li>
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
