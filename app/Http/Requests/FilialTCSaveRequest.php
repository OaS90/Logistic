<?php

namespace App\Http\Requests;

use App\Domain\Admin\TCSettingDTO;
use App\Domain\Admin\TCSettingsDTO;
use App\Domain\DTO\Requests\FilialTCSaveRequestDTO;
use Illuminate\Foundation\Http\FormRequest;

class FilialTCSaveRequest extends FormRequest
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
            '*.id' => 'required|int|min:1',
            '*.code' => 'required|string|min:1',
            '*.name' => 'required|string|min:1',
            '*.region_id' => 'required|int|min:1',
            '*.quote' => 'required|int|min:0',
            '*.delay_days' => 'required|int|min:0',
            '*.settings' => 'required|array',
            '*.settings.*.id' => 'required|int|min:1',
            '*.settings.*.tc_name' => 'required|string|min:1',
            '*.settings.*.last_time' => 'string|min:1|nullable',
            '*.settings.*.enabled' => 'required|bool',
            '*.settings.*.days' => 'required|array',
            '*.settings.*.departure_id' => 'string|min:1|nullable',
        ];
    }

    public function getDTOsArray(): array
    {
        $data = $this->validated();
        $filials = [];

        foreach ($data as $filial) {
            $settings = [];

            foreach ($filial['settings'] as $setting) {
                $settings[] = new TCSettingsDTO(
                    tcName: $setting['tc_name'],
                    settingId: $setting['id'],
                    lastTime: $setting['last_time'],
                    days: $setting['days'],
                    enabled: $setting['enabled'],
                    departureTerminalId: $setting['departure_id'],
                );
            }

            $filials[] = new FilialTCSaveRequestDTO(
                filialId: $filial['id'],
                filialCode: $filial['code'],
                regionId: $filial['region_id'],
                delayDays: $filial['delay_days'],
                quote: $filial['quote'],
                settings: $settings
            );
        }

        return $filials;
    }
}
