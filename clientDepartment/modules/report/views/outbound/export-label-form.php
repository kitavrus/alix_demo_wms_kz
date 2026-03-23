<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:36
 */

use yii\bootstrap\ActiveForm;
use \yii\helpers\Html;

$this->title = Yii::t('outbound/titles', 'Print Export Label');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-order-index">
    <h1><?= $this->title ?></h1>
    <div class="export-label-form">
        <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'export-label-form',
                'validateOnChange' => false,
            ]
        ); ?>

        <?= $form->field($model, 'file')->fileInput() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('buttons', 'Upload'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end() ?>
    </div>

<?php if ($href) { ?>
    <div class="form-group">
        <?= Html::a(Yii::t('buttons', 'Download file'), $href, ['class' => 'btn btn-warning']) ?>
    </div>
<?php } ?>

</div>
