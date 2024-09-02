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

}
