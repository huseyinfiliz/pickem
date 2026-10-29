<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\SeasonResource;
use HuseyinFiliz\Pickem\Season;
use HuseyinFiliz\Pickem\Validator\SeasonValidator;
use Illuminate\Support\Arr; // EKLENDİ
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

class UpdateSeasonController extends AbstractShowController
{
    public $resource = SeasonResource::class;

    public function __construct(protected SeasonValidator $validator)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        $id = Arr::get($request->getQueryParams(), 'id');
        $season = Season::findOrFail($id);

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        // --- YENİ MANTIK ---
        // 1. Gelen camelCase (startDate) veriyi snake_case (start_date) veriye dönüştür
        $attributes = [];
        foreach ($data as $key => $value) {
            $attributes[Str::snake($key)] = $value;
        }

        // 2. Modeli ve veriyi doğrula
        $this->validator->model = $season;
        $this->validator->assertValid($data);

        // 3. Tarih alanlarını Carbon nesnesine dönüştür
        if ($startDate = Arr::get($attributes, 'start_date')) {
            $attributes['start_date'] = Carbon::parse($startDate);
        }
        if ($endDate = Arr::get($attributes, 'end_date')) {
            $attributes['end_date'] = Carbon::parse($endDate);
        }

        // 4. Modeli $fillable kullanarak doldur ve kaydet
        $season->fill($attributes);
        $season->save();
        // --- YENİ MANTIK SONU ---

        return $season;
    }
}
