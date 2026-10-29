<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\TeamResource;
use HuseyinFiliz\Pickem\Team;
use HuseyinFiliz\Pickem\Validator\TeamValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

class CreateTeamController extends AbstractCreateController
{
    public $resource = TeamResource::class;

    public function __construct(protected TeamValidator $validator)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        // --- YENİ MANTIK ---

        // 1. Specific logic: Auto-generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug(Arr::get($data, 'name'));
        }

        // 2. Convert camelCase keys (logoPath) to snake_case (logo_path)
        $attributes = [];
        foreach ($data as $key => $value) {
            $attributes[Str::snake($key)] = $value;
        }

        // 3. Validate the converted attributes
        $this->validator->assertValid($attributes);

        // 4. Create the model using the $fillable array in Team.php
        $team = Team::create($attributes);
        // --- YENİ MANTIK SONU ---

        return $team;
    }
}
