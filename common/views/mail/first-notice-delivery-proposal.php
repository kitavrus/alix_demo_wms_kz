<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 06.11.14
 * Time: 15:18
 */
?>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <b>Добрый день,</b><br />
    <?php if($type == 'ok') { ?>
        Сегодня будет поставка, не забудьте взять заявку на прием товара.<br />
    <?php } else { ?>
        На сегодня поставка отменена.<br />
    <?php } ?>
</p>
<h5 style="color: red">Если у вас возникли какие-либо вопросы связанные с доставкой в ваш магазин,
    пожалуйста, обращайтесь к Абзалу по телефону: <strong>+7 (701) 716-42-55</strong></h5>
<br />
(passmail)