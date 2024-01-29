<?php

namespace App\Http\Resources;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $languageId = Language::where('abbreviation', app()->getLocale())->firstOrFail()->id;
        $detail = null;

        foreach ($this->details as $detailObject)
            if ($detailObject->languageId == $languageId)
                $detail = $detailObject;
                
        return [
            'id' => $this->id,
            // 'featureTitle' => $this->featureTitle,

            'title' => $detail['title'] ?? null
        ];
    }
}
