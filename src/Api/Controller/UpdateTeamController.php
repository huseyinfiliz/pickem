<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\TeamResource;
use HuseyinFiliz\Pickem\Team;
use HuseyinFiliz\Pickem\Validator\TeamValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str; // EKLENDİ
use Psr\Http\Message\ServerRequestInterface;

class UpdateTeamController extends AbstractShowController
{
    public $resource = TeamResource::class;

    public function __construct(protected TeamValidator $validator)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        $id = Arr::get($request->getQueryParams(), 'id');
        $team = Team::findOrFail($id);

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        // --- YENİ MANTIK ---
        // 1. Gelen camelCase (logoPath) veriyi snake_case (logo_path) veriye dönüştür
        $attributes = [];
        foreach ($data as $key => $value) {
            $attributes[Str::snake($key)] = $value;
        }

        // 2. Modeli ve veriyi doğrula
        $this->validator->model = $team;
        $this->validator->assertValid($attributes);

        // 3. Modeli $fillable kullanarak doldur ve kaydet
        $team->fill($attributes);
        $team->save();
        // --- YENİ MANTIK SONU ---

        return $team;
    }
}
