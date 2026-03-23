<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'dateControlDisplay' => [
        'date' => 'php:d-M-Y',
        'time' => 'php:H:i:s A',
        'datetime' => 'php:d-m-Y H:i:s',
    ],
//
// format settings for saving each date attribute
    'dateControlSave' => [
        'date' => 'php:Y-m-d',
        'time'=> 'php:H:i:s',
        'datetime' => 'php:U',
    ],
    'dateControlDisplayTimezone'=>'Asia/Almaty',
    'dateControlSaveTimezone'=>'UTC',
    'stockDepartmentUrl'=>'http://wms.nmdx.kz',

    // тип печати 'pdf' - PDF файл который надо сохранить и затем распечатать
    // тип печати 'html' - html автопечать
    'printType' => 'pdf',
    'TttCopiesNumber' => 2,
];
