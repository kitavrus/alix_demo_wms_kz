<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 29.10.14
 * Time: 10:49
 */
namespace stockDepartment\modules\report\service\reportToDay;

use Yii;

class MailService extends \common\components\MailManager
{
    public function sendMailIfReadyReportToDay($outboundToDay,$inboundToDay,$acceptedCrossDockBoxToDay)
    {
        $emails = [];
        $emails[] = 'ipotema@nomadex.kz';
//        $emails[] = 'bmambetsadykova@nomadex.kz';
        $emails[] = 'aamankeldy@nomadex.kz';
        $emails[] = 'Azamat.Zholdasbekov@defacto.com.tr';
        $emails[] = 'aualiev@nomadex.kz';
        $emails[] = 'mjalilov@nomadex.kz';

        $subject = Yii::t('custom-mail', 'Отчет о выполненой работе за 24 часа');
        return $this->sendMessage($emails, $subject,
            'if-ready-report-to-day', [
                'outboundToDay' => $outboundToDay,
                'inboundToDay' => $inboundToDay,
                'acceptedCrossDockBoxToDay' => $acceptedCrossDockBoxToDay,
            ]
        );
    }
}