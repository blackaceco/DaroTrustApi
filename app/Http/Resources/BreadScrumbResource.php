<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BreadScrumbResource extends JsonResource
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
            'path' => $this->path,
            'level' => $this->level,
            'title' => $this->whenLoaded('breadcrumb_locale', $this->breadcrumb_locale[0]->title ?? null)
        ];
    }
}
