<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 20.08.2024
 * Time: 8:48
 */
/* @var $b2bBoxCount integer */
/* @var $b2bLotCount integer */
/* @var $b2cBoxCount integer */
/* @var $b2cProductCount integer */
/* @var $returnLotBoxCount integer */
/* @var $returnLotCount integer */
/* @var $returnPalletBoxCount integer */
/* @var $returnPalletCount integer */
echo "<br />";
echo "<h1 >Короба на остатке/товары<h1 />";
echo "B2B (мезонин, этажи: 1,2) коробки: ".Yii::$app->formatter->asDecimal($b2bBoxCount)."<br />";
echo "B2B (мезонин, этажи: 1,2) лоты: ".Yii::$app->formatter->asDecimal($b2bLotCount)."<br />"."<br />";

echo "B2C (мезонин, этажи: 3) коробки: ".Yii::$app->formatter->asDecimal($b2cBoxCount)."<br />";
echo "B2C (мезонин, этажи: 3) товары: ".Yii::$app->formatter->asDecimal($b2cProductCount)."<br />"."<br />";

echo "Return (адрес склада начинается с 4) коробки с лотами: ".Yii::$app->formatter->asDecimal($returnLotBoxCount)."<br />";
echo "Return (адрес склада начинается с 4) только лоты: ".Yii::$app->formatter->asDecimal($returnLotCount)."<br />"."<br />";

echo "Return (адрес склада начинается с 5,6,7,8) паллеты с коробами: ".Yii::$app->formatter->asDecimal($returnPalletBoxCount)."<br />";
echo "Return (адрес склада начинается с 5,6,7,8) товаров в паллетах: ".Yii::$app->formatter->asDecimal($returnPalletCount)."<br />"."<br />";

echo "Итого коробок (B2B+B2C+Return box lot+Return box on pallet): ".Yii::$app->formatter->asDecimal($b2bBoxCount+$b2cBoxCount+$returnLotBoxCount+$returnPalletBoxCount)."<br />";
echo "Итого лотов (B2B+B2C+Return lot+Return lot on pallet): ".Yii::$app->formatter->asDecimal($b2bLotCount+$b2cProductCount+$returnLotCount+$returnPalletCount)."<br />";
echo "<br />";
