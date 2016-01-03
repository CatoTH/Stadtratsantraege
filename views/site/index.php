<?php
use app\models\Antrag;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var Antrag[] $antraege
 * @var int $sort
 * @var int $sort_desc
 */

$this->title = 'Antragsverwaltung der Grünen Stadtratsfraktion';

$stadtraetinnen_alle = \app\models\Stadtraetin::find()->orderBy('name')->all();

$sortTitle = function ($titel, $curr_sort, $curr_desc, $my_sort, $default_desc) {
    if ($curr_sort == $my_sort) {
        $url = Url::toRoute(['site/index', 'sort' => $my_sort, 'sort_desc' => ($curr_desc ? 0 : 1)]);
        if ($curr_desc) {
            $text = '<span style="color: black; font-weight: bold;">v ' . $titel . '</span>';
        } else {
            $text = '<span style="color: black; font-weight: bold;">X ' . $titel . '</span>';
        }
    } else {
        $url = Url::toRoute(['site/index', 'sort' => $my_sort, 'sort_desc' => $default_desc]);
        if ($default_desc) {
            $text = '<span class="sort_hidden">v</span> ' . $titel . '</span>';
        } else {
            $text = '<span class="sort_hidden">X</span> ' . $titel . '</span>';
        }
    }
    echo Html::a($text, $url, ['class' => 'sort_link']);
};

echo Html::beginForm('', 'post', ['class' => 'antrag-form']);
?>
    <div id="filterWidget" style="margin-bottom: 20px;">
        <?php echo $this->render('index_filterwidget'); ?>
    </div>


    <table id="antragsliste">
        <thead>
        <tr>
            <th class="title"><?php
                $sortTitle('Titel', $sort, $sort_desc, Antrag::SORT_TITEL, 0);
                ?></th>
            <th class="antragstellerin">Antragsteller*in</th>
            <th class="antragsdatum"><?php
                $sortTitle('Datum', $sort, $sort_desc, Antrag::SORT_DATUM, 1);
                ?></th>
            <th class="status"><?php
                $sortTitle('Status', $sort, $sort_desc, Antrag::SORT_STATUS, 0);
                ?></th>
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
