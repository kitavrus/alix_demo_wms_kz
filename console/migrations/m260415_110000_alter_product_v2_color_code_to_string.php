<?php

use yii\db\Migration;

/**
 * Изменяет тип product_v2.color_code с INT на VARCHAR(32).
 *
 * Причина: Alix 1C отдаёт color_code не только числом (0, 101), но и
 * строкой с буквенным префиксом: "AF501", "LF301", "P01", "G03", "MF401".
 * INT не позволяет хранить такие значения.
 */
class m260415_110000_alter_product_v2_color_code_to_string extends Migration
{
    public function up()
    {
        $this->alterColumn(
            '{{%product_v2}}',
            'color_code',
            $this->string(32)->null()->comment('Код цвета (допускает буквенные префиксы: AF501, LF301, P01, G03 и т.п.)')
        );
    }

    public function down()
    {
        // Откат может потерять буквенно-цифровые значения, т.к. INT приведёт к 0.
        $this->alterColumn(
            '{{%product_v2}}',
            'color_code',
            $this->integer()->null()->comment('Код цвета')
        );
    }
}
