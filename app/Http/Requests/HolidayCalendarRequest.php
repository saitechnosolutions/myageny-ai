<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HolidayCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $holidayId = $this->route('holiday_calendar')?->id;

        return [
            'holiday_date' => [
                'required',
                'date',
                Rule::unique('holiday_calendars', 'holiday_date')
                    ->ignore($holidayId)
                    ->whereNull('deleted_at'),
            ],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
