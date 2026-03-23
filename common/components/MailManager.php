<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 10:49
 */
namespace common\components;

use common\modules\leads\models\TransportationOrderLead;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use Yii;
use yii\base\Component;
use common\modules\client\models\ClientEmployees;
use common\modules\store\models\Store;
use yii\helpers\VarDumper;
use yii\validators\EmailValidator;


class MailManager extends Component
{
    /**
     * @var string
     */
    public $viewPath = '@common/views/mail';

    /**
     * @var string|array
     */
    public $sender = 'no-reply@nomadex.kz';

    /*
     * @var string
     * */
    public $url = "client-wms.nmdx.kz";

    /*
     * Send mail if created new delivery proposal
     * @param $dpModel Delivery Proposal model
     * */
    public function sendNewDeliveryProposalMessage($dpModel)
    {
        $emails = [];
        $emails[] = 'kitavrus@ya.ru';

        if($routeTo = $dpModel->routeTo) {

//            $emails[] = $routeTo->email;
            $validator = new EmailValidator();
            if ($validator->validate($routeTo->email)) {
                $emails[] = $routeTo->email;
            } else {
                //TODO Send mail to admin
            }

            // находим всех директоров магазина и отправляем им имейлы
            $clientEmployees = ClientEmployees::find()
                                ->where([
                                    'deleted'=>0,
                                   'client_id'=>$dpModel->client_id,
                                   'store_id'=>$routeTo->id,
                                   'manager_type'=>[
                                       ClientEmployees::TYPE_BASE_ACCOUNT,
                                       ClientEmployees::TYPE_LOGIST,
                                       ClientEmployees::TYPE_DIRECTOR,
                                       ClientEmployees::TYPE_DIRECTOR_INTERN,
                                   ]
                                ])
            ->all();

            foreach($clientEmployees as $employee) {
//                $emails[] = $employee->email;
//                $validator = new EmailValidator();
                if ($validator->validate($employee->email)) {
                    $emails[] = $employee->email;
                } else {
                    //TODO Send mail to admin
                }
            }
        }

        return $this->sendMessage( $emails,
                Yii::t('custom-mail','В ваш магазин ожидается поступление товара (passmail)'),
                'new-delivery-proposal',
                ['dpModel'=>$dpModel,
                 'url'=>$this->url
                ]
        );
    }


    /*
     * Send email to store if expected delivery
     * @param array $storeIds
     * @param string $type Ok - expected delivery Cancel - cancel delivery
     * @return boolean
     * */
    public function SendEmailToStoreIfExpectedDelivery($storeIds,$type)
    {
        $emails = [];
//        $emails[] = 'kitavrus@ya.ru';
        $return = false;
        if(empty($storeIds) || empty($type)) {
            return false;
        }

        if($stores = Store::findAll($storeIds)) {
            foreach($stores as $store) {
                $emails = [];
                $emails[] = 'kitavrus@ya.ru';


                $validator = new EmailValidator();
                if ($validator->validate(trim($store->email))) {
                    $emails[] = trim($store->email);
                } else {
                    //TODO Send mail to admin
                    $this->sendMessage($emails,'Validate EMAIL messages',
                        'validate-error-message',['mail'=>trim($store->email),'storeId'=>$store->id]
                    );
                }
//              $emails[] = $store->email;

                // находим всех директоров магазина и отправляем им имейлы
                $clientEmployees = ClientEmployees::find()
                    ->where([
                        'deleted'=>0,
                        'client_id'=>$store->client_id,
                        'store_id'=>$store->id,
                        'manager_type'=>[
                            ClientEmployees::TYPE_BASE_ACCOUNT,
                            ClientEmployees::TYPE_LOGIST,
                            ClientEmployees::TYPE_DIRECTOR,
                            ClientEmployees::TYPE_DIRECTOR_INTERN,
                        ]
                    ])
                    ->all();

                foreach($clientEmployees as $employee) {
//                  $emails[] = $employee->email;
                    if ($validator->validate(trim($employee->email))) {
                        $emails[] = trim($employee->email);
                    } else {
                        //TODO Send mail to admin
                        $this->sendMessage($emails,'Validate EMAIL messages',
                            'validate-error-message',['mail'=>trim($employee->email),'employeeId'=>$employee->id]
                        );
                    }
                }

                //        $subject = Yii::t('custom-mail','В ваш магазин отменено ожидаемое поступление');
                $subject = Yii::t('custom-mail','На сегодня ПОСТАВКА ОТМЕНЕНА (passmail)');

                if($type == 'ok') {
                    $subject = Yii::t('custom-mail','В ваш магазин предположительно ОЖИДАЕТСЯ ПОСТАВКА (passmail)');
                }

                $return = false;

                if(!empty($emails)) {
                    $return =  $this->sendMessage($emails,$subject,
                        'first-notice-delivery-proposal',['type'=>$type]
                    );
                }
            }
        }

        return $return;
    }

    /*
     * Send mail if client created new Delivery Proposal
     * @param $dpModel Delivery Proposal model
     * */
    public function sendMailToStockIfClientCreateNewDP($dpModel)
    {
        $emails = [];
        $emails[] = 'kitavrus@ya.ru';
        $emails[] = 'aualiev@nomadex.kz';
        $emails[] = 'abualiev@nomadex.kz';

        $subject = Yii::t('custom-mail','Клиент создал новую заявку');

        return $this->sendMessage($emails,$subject,
            'to-stock-if-client-create-new-delivery-proposal',[
                'dpModel'=>$dpModel,
            ]
        );
    }

    /*
    * Send mail if client created new review for Delivery Proposal
    * @param $model client review
    * */
    public function sendMailToStockIfClientCreateNewReviewDP($model)
    {
        $emails = [];
        $emails[] = 'kitavrus@ya.ru';
        $emails[] = 'aualiev@nomadex.kz';
        $emails[] = 'abualiev@nomadex.kz';

        $subject = Yii::t('custom-mail','Клиент создал новый отзыв на заявку');

        return $this->sendMessage($emails,$subject,
            'to-stock-if-client-create-new-review-delivery-proposal',[
                'model'=>$model,
            ]
        );
    }


    /*
    * Send mail if client created new delivery proposal and stock man change status to route formatted
    * @param $dpModel Delivery Proposal model
    * */
    public function sendIfStatusRouteFormattedDeliveryProposalToMessage($dpModel)
    {
        $emails = [];
        $emails[] = 'kitavrus@ya.ru';

        if($routeFrom = $dpModel->routeFrom) {

            $validator = new EmailValidator();
            if ($validator->validate(trim($routeFrom->email))) {
                $emails[] = trim($routeFrom->email);
            } else {
                //TODO Send mail to admin
                $this->sendMessage($emails,'Validate EMAIL messages',
                    'validate-error-message',['mail'=>trim($routeFrom->email),'storeId'=>$routeFrom->id]
                );
            }


            // находим всех директоров магазина и отправляем им имейлы
            $clientEmployees = ClientEmployees::find()
                ->where([
                    'deleted'=>0,
                    'client_id'=>$dpModel->client_id,
                    'store_id'=>$routeFrom->id,
                    'manager_type'=>[
//                        ClientEmployees::TYPE_BASE_ACCOUNT,
//                        ClientEmployees::TYPE_LOGIST,
                        ClientEmployees::TYPE_DIRECTOR,
                        ClientEmployees::TYPE_DIRECTOR_INTERN,
                    ]
                ])
                ->all();

            foreach($clientEmployees as $employee) {
//                $emails[] = $employee->email;
                if ($validator->validate($employee->email)) {
                    $emails[] = trim($employee->email);
                } else {
                    //TODO Send mail to admin
                    $this->sendMessage($emails,'Validate EMAIL messages',
                        'validate-error-message',['mail'=>trim($employee->email),'employeeId'=>$employee->id]
                    );
                }
            }
        }

        $filterMails[] = 'EkaterinaYurchakova@Tupperware.com'; // для топера
        $emails = $this->filterMail($emails,$filterMails);

        return $this->sendMessage( $emails,
            Yii::t('custom-mail','К созданной вами заявке добавлен маршрут'),
            'if-status-route-formatted-delivery-proposal',
            ['dpModel'=>$dpModel,
                'url'=>$this->url
            ]
        );
    }

    public function filterMail($emails,$filterMails)
    {
        if(!empty($emails) && is_array($emails)) {
            foreach($emails as $key=>$email) {
                if(in_array($email,$filterMails)) {
                    unset($emails[$key]);
                }
            }
        }

        return $emails;
    }

    /*
    * Send mail to client if operator confirm lead order
    * */
    public function sendOrderConfirmMail(TransportationOrderLead $order){
        if(is_object($order->client) && is_object($order->deliveryProposal)){
            if(!empty($order->client->email)){
                return $this->sendMessage(
                    $order->client->email,
                    Yii::t('client/mail', 'NMDX: your order №{0} was confirmed',[$order->order_number]),
                    'lead-order-confirmed',
                    [
                        'order'=>$order
                    ]
                );
            }
        }
        return false;
    }

    /*
     * Send mail when corporate client does not have
     * individual tariff for current route
    **/
    public function sendTariffMissingWarningMail(TlDeliveryProposal $dp){
                return $this->sendMessage(
                    'kitavrus@ya.ru',
                    Yii::t('custom-mail', 'Individual tariff missing for contract client'),
                    'contract-client-tariff-missing',
                    [
                        'dp'=>$dp
                    ]
                );
    }

    /*
     * Send mail with problem DP
     **/
    public function sendProblemProposalMail($data, $statusArray){
        return $this->sendMessage(
            'kitavrus@ya.ru',
            Yii::t('custom-mail', 'Problem status Delivery Proposal find'),
            'problem-dp-find',
            [
                'data'=>$data,
                'statusArray'=>$statusArray,
            ]
        );
    }

    /*
     * Send mail with problem DP
     **/
    public function sendEmptyShippedDatetimeProposalMail($data){
        return $this->sendMessage(
            'kitavrus@ya.ru',
            Yii::t('custom-mail', 'Empty shipped_datetime Cars Delivery Proposal find'),
            'empty-shipped-dt-dp-find',
            [
                'data'=>$data,
            ]
        );
    }

    /*
    * Send mail when sub route does not have
    * individual tariff for current route
    **/
    public function sendAgentTariffMissingWarningMail($agent_id,$from_id,$to_id,$dpId)
    {
        $agentTitle = '';
        if($agent = TlAgents::findOne($agent_id)) {
            $agentTitle = $agent->name;
        }

        $fromPointTitle = '';
        if($fromPoint = Store::findOne($from_id)) {
            $fromPointTitle = $fromPoint->getDisplayFullTitle();
        }

        $toPointTitle = '';
        if($toPoint = Store::findOne($to_id)) {
            $toPointTitle = $toPoint->getDisplayFullTitle();
        }

        $deliveryProposalTitle = '';
        if($deliveryProposal = TlDeliveryProposal::findOne($dpId)) {
            $deliveryProposalTitle = $deliveryProposal->id;
        }

        return $this->sendMessage(
            ['kitavrus@ya.ru','abualiev@nomadex.kz'],
            Yii::t('custom-mail', 'Individual tariff missing for sub route'),
            'sub-route-tariff-missing',
            [
                'dpID'=>$dpId,
                'agentTitle'=>$agentTitle,
                'fromPointTitle'=>$fromPointTitle,
                'toPointTitle'=>$toPointTitle,
                'deliveryProposalTitle'=>$deliveryProposalTitle,
            ]
        );
    }

    /**
     * @param  string $to
     * @param  string $subject
     * @param  string $view
     * @param  array  $params
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, $params = [])
    {
//        $mailer = \Yii::$app->mailer;
        $mailer = \Yii::$app->mailer;
        $mailer->viewPath = $this->viewPath;


//        $to[] = 'igogo.ypa@gmail.com';

        return $mailer->compose(['html'=>$view], $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }

    /**
     * @param  string $to
     * @param  string $subject
     * @param  string $filepath path to attached file
     * @return bool
     */
    public function sendMailWithAttach($to, $subject, $filepath)
    {
        $mailer = \Yii::$app->mailer;
        $mailer->viewPath = $this->viewPath;

        return $mailer->compose()
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->attach($filepath)
            ->send();
    }

    /*
    * Send information about errors
    * @param array $data
    * */
    public function sendErrorsMessageMail($data)
    {
        return $this->sendMessage(
            'kitavrus@ya.ru',
            'System error message',
            'errors-message',
            [
                'data'=>$data
            ]
        );
    }

    /*
     * Send mail with problem DP
     **/
    public function sendKpiDeliveryLastDatetimeMail($data){
        return $this->sendMessage(
            ['kitavrus@ya.ru','abualiev@nomadex.kz'],
            "Список заявок на доставку у которых последний день доставки",
            'kpi-delivery-last-datetime',
            [
                'data'=>$data,
            ]
        );
    }


    /*
    * */
    public function sendOneMail($toEmail,$data)
    {
        $subject = Yii::t('custom-mail','Внимание, изменения по работе с системой Nomadex');
        return $this->sendMessage([$toEmail,'ipotema@nomadex.kz'],$subject,
            'one-email',[
                'data'=>$data,
            ]
        );
    }
}