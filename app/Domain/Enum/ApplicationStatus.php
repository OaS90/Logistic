<?php

namespace App\Domain\Enum;

class ApplicationStatus
{
    /**
     * Создан
     */
    const CREATED = 'created';

    /**
     * Новый
     */
    const  NEW = 'new';

    /**
     * В работе
     */
    const IN_PROGRESS = 'inProgress';

    /**
     * Загружен
     */
    const LOADED = 'loaded';

    /**
     * Отложен
     */
    const POSTPONED = 'postponed';

    /**
     * Отказ
     */
    const REFUSAL = 'refusal';

    /**
     * Выполнен
     */
    const COMPLETED = 'completed';

    /**
     * Брак
     */
    const DEFECT = 'defect';

    const ALL = [
        self::CREATED => 'Создан',
        self::NEW => 'Новый',
        self::IN_PROGRESS => 'В работе',
        self::LOADED => 'Загружен',
        self::POSTPONED => 'Отложен',
        self::REFUSAL => 'Отменён',
        self::COMPLETED => 'Выполнен',
        self::DEFECT => 'Брак'
    ];

}
