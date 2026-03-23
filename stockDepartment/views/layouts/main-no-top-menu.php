<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use stockDepartment\assets\AppAsset;
use stockDepartment\widgets\Alert;
use common\modules\billing\models\TlDeliveryProposalBilling;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
//    if (!Yii::$app->user->isGuest) {
        NavBar::begin([
//                'brandLabel' => 'Nomadex WMS 2.0',
            'brandLabel' => 'NMDX WMS API TEST',
            'brandUrl' => '/other/api2/',
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        $menuItems = [];


/*        if(!in_array(Yii::$app->user->id,[6,30])) {
            unset($menuItems[5]['items'][0]);
        }*/

//        $menuItems = \stockDepartment\components\StockRoleRule::showMainMenuByManagerType($menuItems);

//        if (Yii::$app->user->isGuest) {
//            $menuItems[] = ['label' => 'Login', 'url' => ['/user/security/login']];
//        } else {
//            $menuItems[] = [
//                'label' => Yii::t('titles', 'Logout') . '(' . Yii::$app->user->identity->username . ')',
//                'url' => ['/user/security/logout'],
//                'linkOptions' => ['data-method' => 'post']
//            ];
//        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
//    }
    ?>

    <div class="container-fluid">
        <br/>
        <br/>
        <br/>
        <?php if (!Yii::$app->user->isGuest) { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        <?php } ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<!--<footer class="footer">-->
<!--    <div class="container">-->
<!--        <p class="pull-left">&copy; WMS 2.0 --><?//= date('Y') ?><!--</p>-->
<!---->
<!--        <p class="pull-right">Powered by --><?//= Html::a('nomadex.kz','http://nomadex.kz/') ?><!--</p>-->
<!--    </div>-->
<!--</footer>-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
