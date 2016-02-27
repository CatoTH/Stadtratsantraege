<?php
use app\models\Antrag;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var Antrag[] $antraege
 * @var int $sort
 * @var int $sort_desc
 * @var int $zeitraum_jahre
 * @var int $aenderungsantraege
 */

$this->title = 'Antragsverwaltung der Grünen Stadtratsfraktion';

$stadtraetinnen_alle = \app\models\Stadtraetin::find()->orderBy('name')->all();

$sortTitle = function ($titel, $curr_sort, $curr_desc, $my_sort, $default_desc, $zeitraum_jahre, $aenderungsantraege) {
    if ($curr_sort == $my_sort) {
        $url = Url::toRoute([
            'site/index',
            'sort'               => $my_sort,
            'sort_desc'          => ($curr_desc ? 0 : 1),
            'zeitraum_jahre'     => $zeitraum_jahre,
            'aenderungsantraege' => $aenderungsantraege
        ]);
        if ($curr_desc) {
            $text = '<span style="color: black; font-weight: bold;"><span class="glyphicon glyphicon-chevron-down"></span> ' . $titel . '</span>';
        } else {
            $text = '<span style="color: black; font-weight: bold;"><span class="glyphicon glyphicon-chevron-up"></span> ' . $titel . '</span>';
        }
    } else {
        $url = Url::toRoute(['site/index', 'sort' => $my_sort, 'sort_desc' => $default_desc, 'zeitraum_jahre' => $zeitraum_jahre, 'aenderungsantraege' => $aenderungsantraege]);
        if ($default_desc) {
            $text = '<span class="sort_hidden"><span class="glyphicon glyphicon-chevron-down"></span></span> ' . $titel . '</span>';
        } else {
            $text = '<span class="sort_hidden"><span class="glyphicon glyphicon-chevron-up"></span></span> ' . $titel . '</span>';
        }
    }
    echo '<li>' . Html::a($text, $url, ['class' => 'sort_link']) . '</li>';
};

echo Html::beginForm('', 'post', ['class' => 'antrag-form']);
?>
    <div class="zeitraumFilter">
        <?php echo $this->render('index_zeitraum_typ_filter', [
            'sort'               => $sort,
            'sort_desc'          => $sort_desc,
            'zeitraum_jahre'     => $zeitraum_jahre,
            'aenderungsantraege' => $aenderungsantraege,
        ]); ?>
    </div>

    <button style="float: right;" class="btn btn-default eintrag_add_button" type="button">Neuer Eintrag
    </button>

    <div id="filterWidget" style="margin-bottom: 20px;">
        <?php echo $this->render('index_filterwidget'); ?>
    </div>

<ul class="sort-row">
    <?php
    $sortTitle('Titel', $sort, $sort_desc, Antrag::SORT_TITEL, 0, $zeitraum_jahre, $aenderungsantraege);
    $sortTitle('Frist/Verlängerung', $sort, $sort_desc, Antrag::SORT_DATUM_FRIST, 1, $zeitraum_jahre, $aenderungsantraege);
    $sortTitle('Antragsdatum', $sort, $sort_desc, Antrag::SORT_DATUM_ANTRAG, 1, $zeitraum_jahre, $aenderungsantraege);
    $sortTitle('Status', $sort, $sort_desc, Antrag::SORT_STATUS, 0, $zeitraum_jahre, $aenderungsantraege);
    ?>
</ul>

    <ul id="antragsliste">
        <?php
        echo $this->render('index_adder_row', ['stadtraetinnen' => $stadtraetinnen_alle]);
        foreach ($antraege as $antrag) {
            echo $this->render('index_antrag_row', ['antrag' => $antrag]);
        }
        ?>

    </ul>

<?php
echo Html::endForm();
