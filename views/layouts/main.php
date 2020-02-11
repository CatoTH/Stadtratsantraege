<?php
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */

$this->beginPage();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <?= Html::csrfMetaTags() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <meta name="author" content="Tobias Hößl">

    <link rel="stylesheet" href="/css/styles.css">

    <script src="/js/jquery.min.js"></script>
    <script src="/js/jquery.viewport.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/fuelux-3.16.3/dist/js/fuelux.min.js"></script>
    <!--<script src="/js/typeahead.bundle.min.js"></script>-->
    <!--<script src="bootstrap-tagsinput/bootstrap-tagsinput.js"></script>-->
    <script src="/js/moment-with-locales.min.js"></script>
    <script src="/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/js/script.js"></script>
</head>
<body>
<?php $this->beginBody() ?>

<main id="main-content" class="container fuelux">
    <?php echo $content; ?>
</main>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
