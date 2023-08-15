<?php

namespace App\Domain;

trait ApplicationStatuses
{
    private array $statuses = [
        'created' => 'Создано',
        'new' => 'Новый',
        'inProgress' => 'В работе',
        'loaded' => 'Загружен',
        'postponed' => 'Отложен',
        'refusal' => 'Отказ',
        'completed' => 'Выполнен',
        'defect' => 'Брак'
    ];

    public function getStatus($status)
    {
        if ($status)
            return $this->statuses[$status];

        return '';
    }
}