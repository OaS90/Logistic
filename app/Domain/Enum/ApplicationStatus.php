<?php

namespace App\Domain\Enum;

enum ApplicationStatus
{
    /**
     * Новый
     */
    public const NEW = 'new';

    /**
     * В работе
     */
    public const IN_PROGRESS = 'inProgress';

    /**
     * Загружен
     */
    public const LOADED = 'loaded';

    /**
     * Отложен
     */
    public const POSTPONED = 'postponed';

    /**
     * Отказ
     */
    public const REFUSAL = 'refusal';

    /**
     * Выполнен
     */
    public const COMPLETED = 'completed';

    /**
     * Брак
     */
    public const DEFECT = 'defect';

}
