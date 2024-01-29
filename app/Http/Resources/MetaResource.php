<?php

namespace App\Http\Resources;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MetaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $detail = null;

        foreach ($this->details as $detailObject)
            if ($detailObject->languageId == $request->languageId)
                $detail = $detailObject;

                
        return [
            'id' => $this->id,
            'page' => $this->page,
            'websiteName' => $this->whenLoaded('website', $this->website()->first()->title),
            'title' => $detail->title ?? null,
            'description' => $detail->description ?? null,
            'keywords' => $detail->keywords ?? null,
            'image' => !is_null($detail) ? getFileLink($detail->image):null,
        ];
    }
}
