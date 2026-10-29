<?php

namespace HuseyinFiliz\Pickem\Validator;

use Flarum\Foundation\AbstractValidator;
use Illuminate\Validation\Rule;

class TeamValidator extends AbstractValidator
{
    public ?\Illuminate\Database\Eloquent\Model $model = null;

    protected function getRules(): array
    {
        $id = $this->model ? $this->model->id : null;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pickem_teams', 'slug')->ignore($id),
            ],
            'logoPath' => ['nullable', 'string', 'url', 'max:255'],
        ];
    }

    protected function getMessages(): array
    {
        return [
            // GÜNCELLENDİ: lib.messages.slug_unique
            'slug.unique' => 'huseyinfiliz-pickem.lib.messages.slug_unique',
        ];
    }
}
