<?php

//use yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\client\models\ClientEmployees;
use clientDepartment\modules\client\components\ClientManager;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientEmployees */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$showSelectStore = false;

if(!Yii::$app->user->isGuest) {
//    if($userModel = Yii::$app->getModule('user')->manager->findUserById(Yii::$app->user->id)) {
        if ($client = ClientManager::getClientByUserID()) {
//                    VarDumper::dump($client,10,true);
            switch ($client->manager_type) {
                case ClientEmployees::TYPE_BASE_ACCOUNT:
                case ClientEmployees::TYPE_LOGIST:
                    $showSelectStore = true;

                    break;
                case ClientEmployees::TYPE_DIRECTOR:
                case ClientEmployees::TYPE_DIRECTOR_INTERN:
                    $showSelectStore = false;

                    break;
                case ClientEmployees::TYPE_MANAGER:
                case ClientEmployees::TYPE_MANAGER_INTERN:
                    $showSelectStore = false;
                    break;
                default:
                    break;
            }
        }
    }
//}
?>



<div class="client-managers-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?//= $form->field($model, 'store_id')->textInput() ?>
    <?php if($showSelectStore) {?>
        <?= $form->field($model, 'store_id')->dropDownList($storeList,['prompt'=>Yii::t('titles', 'Please select')]) ?>
    <?php } else{ ?>
<!--        --><?//= $form->field($model, 'store_id')->textInput(['value'=>$storeList[$model->store_id],'disabled']); ?>
    <?php }
    ?>
<!--    --><?//= $form->field($model, 'client_id')->textInput() ?>

<!--    --><?//= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>

<!--    --><?//= $form->field($model, 'manager_type')->dropDownList($model::getTypeArray(),['prompt'=>Yii::t('titles', 'Please select')]) ?>

<!--    --><?//= $form->field($model, 'status')->dropDownList($model::getStatusArray()); ?>

    <?= $form->field($model, 'password')->passwordInput()->hint(Yii::t('titles','At least 6 characters, Latin characters and digits')) ?>

    <?= Html::hiddenInput('rt',Yii::$app->request->get('rt',0)) ?>

<!--    --><?//= $form->field($model, 'client_id',['template'=>'{input}'])->hiddenInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
