<?php

namespace stockDepartment\modules\other\controllers;

class MobController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $response = [];
        $response [] = $this->obchepit();
        $response [] = $this->music();
        $response [] = $this->otherSpec();

        return $this->asJson($response);
    }

    private function obchepit() {
        $result = [];
        $result['category'] = "Общепит";
        $result['specializations'][] = [
            "name"=>  "Кухня",
            "specializations"=> [
                "Повар",
                "Шеф-повар",
                "Су-Шеф",
                "Кондитер",
                "Пекарь",
                "Повар горячего цеха",
                "Повар мясного цеха",
                "Повар холодного цеха",
                "Повар-универсал",
                "Сушист",
                "Пиццмейкер",
                "Помощник повара"
            ]
        ];

        $result['specializations'][] = [
            "name"=>  "Зал и Бар",
            "specializations"=> [
                "Старший официант",
                "Официант",
                "Старший бармен",
                "Бармен",
                "Бариста",
                "Кассир",
                "Хостес",
                "Сомелье",
                "Кальянный мастер"
            ]
        ];

        $result['specializations'][] = [
            "name"=>  "Управление",
            "specializations"=> [
                "Управляющий заведения",
                "Администратор зала",
                "Менеджер ресторана",
                "Бар-менеджер"
            ]
        ];

        return $result;
    }

    private function music() {
        $result = [];
        $result['category'] = "Музыканты";
        $result['specializations'][] = [
            "name"=>  "Исполнители",
            "specializations"=> [
                "Певец",
                "Инструменталист",
                "DJ",
                "Шоу - программа"
            ]
        ];
        return $result;
    }

    private function otherSpec() {
        $result = [];
        $result['category'] = "Другие специалисты";
        $result['specializations'][] = [
            "name"=>  "Доставка",
            "specializations"=> [
                "Курьер",
            ]
        ];

        $result['specializations'][] = [
            "name"=>  "Бухгалтерия",
            "specializations"=> [
                "Бухгалтер", "Калькулятор", "Главный бухгалтер"
            ]
        ];

        $result['specializations'][] = [
            "name"=>  "Мойка-Уборка",
            "specializations"=> [
                "Посудомойщица",
                "Уборщица",
                "Горничная"
            ]
        ];

        return $result;

    }
}