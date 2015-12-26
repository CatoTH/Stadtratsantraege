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

    <script src="/js/bower/bower-asset/html5shiv/dist/html5shiv.min.js"></script>
    <script src="/js/bower/bower-asset/es5-shim/es5-shim.js"></script>
    <script src="/js/bower/bower-asset/jquery/dist/jquery.min.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/fuelux-3.13.0/dist/js/fuelux.min.js"></script>
    <script src="/js/bower/bower-asset/typeahead.js/dist/typeahead.bundle.js"></script>
    <script src="bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
    <script src="/js/bower/bower-asset/moment/min/moment-with-locales.js"></script>
    <script src="/js/bower/bower-asset/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
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