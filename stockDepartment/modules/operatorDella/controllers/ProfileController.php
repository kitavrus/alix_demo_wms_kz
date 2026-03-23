<?php

namespace app\modules\operatorDella\controllers;

use app\controllers\SiteController;
use common\modules\user\models\User;
use app\components\ClientManager;
class ProfileController extends SiteController
{
    public function actionView()
    {
        $model = ClientManager::findClient();

        return $this->render('view',['model'=>$model]);
    }

    /**
     * Updates an existing ExternalClientLead model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionEdit()
    {
        $model = ClientManager::findClient();

        if ($model->load(\Yii::$app->request->post()) && $model->save(false)) {
            if( $userModel = User::findOne(['id'=>$model->user_id]) ) {
                $userModel->scenario = 'update';
                $userModel->email = $model->email;
                $userModel->password = $model->password;
                $userModel->save();

                // Clear password
                $model->password = '';
                $model->save();
                \Yii::$app->getSession()->setFlash('success', \Yii::t('client/messages', 'Information was successfully saved'));
            }
            return $this->redirect(['view']);
        } else {
            return $this->render('edit', [
                'model' => $model,
            ]);
        }
    }

}
