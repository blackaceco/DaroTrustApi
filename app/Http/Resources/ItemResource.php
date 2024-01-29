<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'view' => $this->view,
            'featureTitle' => $this->featureTitle,
            'groups' => GroupResource::collection($this->whenLoaded('groups')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'details' => ItemDetailResource::collection($this->whenLoaded('locale_details')),
            'children' => ItemResource::collection($this->whenLoaded('children')),
            'created_at' => [
                'humans' => CarbonPrinter($this->createdAt, 'humans'),
                'datetime' => CarbonPrinter($this->createdAt, 'datetime'),
            ],
        ];
    }
}
