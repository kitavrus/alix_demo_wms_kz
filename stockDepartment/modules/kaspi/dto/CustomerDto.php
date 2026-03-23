<?php

namespace stockDepartment\modules\kaspi\dto;

/**
 * @property string $firstName Имя клиента
 * @property string $lastName Фамилия клиента
 * @property string $cellPhone Телефон клиента
 */
class CustomerDto
{
    public $firstName;
    public $lastName;
    public $cellPhone;

    /**
     * @param string $firstName Имя клиента
     * @param string $lastName Фамилия клиента
     * @param string $cellPhone Телефон клиента
     */
    public function __construct($firstName, $lastName, $cellPhone)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->cellPhone = $cellPhone;
    }

    /**
     * Преобразует объект в массив для API
     * @return array
     */
    public function toArray()
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'cellPhone' => $this->cellPhone,
        ];
    }
}

