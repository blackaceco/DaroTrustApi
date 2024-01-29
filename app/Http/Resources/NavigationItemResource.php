<?php

namespace App\Http\Resources;

use App\Models\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'featureTitle' => $this->featureTitle,
            'details' => NavigationItemDetailResource::collection($this->whenLoaded('locale_details')),
            'children' => NavigationItemResource::collection($this->whenLoaded('children')),
        ];
    }
}
