<?php
use yii\helpers\Html;
use stockDepartment\assets\AppAsset;


/* @var $this \yii\web\View */
/* @var $content string */

//AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <style>
        .ver {
            margin-bottom: 0;
            margin-left: 10mm;
            position: relative;
            top: -18mm;
            transform: rotate(90deg);
            transform-origin: 70% 270% 0;

        }
        td {
            padding: 0 0 0 2mm !important;
        }
        .inside-ver {
            padding-left: 3mm;
            font-size: 8pt;

        }
        .a4 {
            height: 297mm;
            width: 210mm;
            padding: 8mm 8mm 8mm 8mm;
            margin-bottom: 3mm;
        }

        .a4-l {
            height: 210mm;
            width: 297mm;
            /*padding: 5mm 3mm 3mm 3mm;*/
            /*margin-bottom: 3mm;*/
        }
        .ttn {
            font-size: 3.5mm;
        }
        .picking-list-table {
            margin-top: 10mm;
        }
        .NOMADEX70X100 {
            height: 70mm;
            width: 100mm;
            border: 1mm solid black;
            margin-bottom: 1mm;
        }

        .NOMADEX70X100 table {
            margin-bottom: 1mm;
            margin-left: 1mm;
        }

        .v-label-barcode {
            left: 87mm;
            position: relative;
            top: -80mm;
        }
        .h-label-barcode {
            margin-top: 1mm;
        }
        .logo {
            left: 30mm;
            margin-top: 1mm;
            position: relative;
            top: -4mm;
        }
        .ttn-logo {
           float: right;
            margin-right: 3mm;
        }

        .h-picking-list-barcode{
             margin-left: 10mm;
         }

        .page-counter{
            font-size: 5mm;
            font-weight: bold;
            left: 190mm;
            position: relative;
            top: 2mm;
        }
        .time-reminder{
            /*font-size: 4mm;*/
            /*position: relative;*/
            /*top: 180mm;*/
        }
        .hidden-print {
            display: none;
        }
        .row-title {
            font-weight: bold;
        }



    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php echo $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
