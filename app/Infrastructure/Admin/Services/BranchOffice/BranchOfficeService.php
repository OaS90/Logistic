<?php

namespace App\Infrastructure\Admin\Services\BranchOffice;

use App\Infrastructure\Repositories\Hru\FilialRepository;

class BranchOfficeService
{
    public function __construct(private readonly FilialRepository $filialRepo)
    {
    }

    public function setActiveInQuotes(int $id, bool $isActive): void
    {
        $this->filialRepo->setActiveInQuotes($id, $isActive);
    }

    public function setActiveInTkQuotes(int $id, bool $isActive): void
    {
        $this->filialRepo->setActiveInTkQuotes($id, $isActive);
    }
}
