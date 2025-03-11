<?php

namespace App\Http\Requests\Application;

use App\Domain\DTO\Requests\StatusFrom1cRequestDTO;
use App\Domain\Enum\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;

class StatusesFrom1cRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.id' => 'required|string|min:1',
            '*.docVer' => 'integer|min:1',
            '*.statuses' => 'required|array|min:1',
            '*.statuses.*.dateTime' => 'required|date_format:Y-m-d H:i:s',
            '*.statuses.*.status' => 'required|in:' . implode(',', array_keys(ApplicationStatus::ALL)),
        ];
    }

    public function getDTOs(): array
    {
        $appStatuses = [];
        $data = $this->validated();

        foreach ($data as $appWithStatuses) {
            $statusInfo = last($appWithStatuses['statuses']);

            $appStatuses[] = new StatusFrom1cRequestDTO(
                orderNumber: $appWithStatuses['id'],
                lastStatus: $statusInfo['status'],
                statusDateTime: $statusInfo['dateTime']
            );
        }

        return $appStatuses;
    }
}
