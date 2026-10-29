<?php

namespace HuseyinFiliz\Pickem\Validator;

use Flarum\Foundation\AbstractValidator;
use Illuminate\Validation\Rule;

class WeekValidator extends AbstractValidator
{
    public ?\Illuminate\Database\Eloquent\Model $model = null;

    protected function getRules(): array
    {
        $required = $this->model ? 'sometimes' : 'required';

        return [
            'name' => [$required, 'string', 'max:100'],
            'seasonId' => [
                'nullable', // Sezonsuz haftalara izin ver
                'integer',
                Rule::exists('pickem_seasons', 'id') // Ama ID varsa, 'seasons' tablosunda var olmalı
            ],
            'weekNumber' => ['nullable', 'integer'],
        ];
    }
}
