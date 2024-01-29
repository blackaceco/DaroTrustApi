<?php

namespace App\Http\Resources;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'title' => $detail['value'] ?? null
        ];
    }
}
