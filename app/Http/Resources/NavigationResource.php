<?php

namespace App\Http\Resources;

use App\Models\NavigationItemHierarchy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $childIds = NavigationItemHierarchy::pluck('childId');
        $items = $this->items()->with(['locale_details', 'children'])->whereNotIn('navigation_items.id', $childIds)->get();
        
        return [
            'id' => $this->id,
            'navigation' => $this->navigation,
            'items' => NavigationItemResource::collection($items)
        ];
    }
}
