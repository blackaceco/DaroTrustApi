<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
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
            'admin' => $this->whenLoaded('admin', $this->admin ? [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
            ] : null),
            'website' => $this->whenLoaded('website', $this->website ? [
                'id' => $this->website->id,
                'name' => $this->website->name,
            ] : null),
            'ipAddress' => $this->ipAddress,
            'entityId' => $this->entityId,
            'entity' => $this->entity,
            'action' => $this->action,
            'oldValue' => $this->oldValue,
            'newValue' => $this->newValue,
            'createdAt' => CarbonPrinter($this->createdAt, 'datetime'),
        ];
    }
}
