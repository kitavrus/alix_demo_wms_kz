<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.01.15
 * Time: 11:36
 */

use yii\bootstrap\ActiveForm;
use \yii\helpers\Html;

?>

<?php $this->title = Yii::t('stock/titles', 'Stock inventory'); ?>

    <h1><?= $this->title ?></h1>

    <div class="stock-inventory-form">
        <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'stock-inventory-form',
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