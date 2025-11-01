<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'service_name' => $this->service->name ?? 'N/A',
            'service_type' => $this->service->type ?? 'N/A',
            'amount' => $this->amount,
            'charge' => $this->charge,
            'status' => ucfirst($this->status->value),
            'method' => ucfirst($this->service->method ?? 'N/A'),
            'created_at' => $this->created_at->format('d M Y h:i A'),
            'metadata' => $this->data,
        ];
    }
}
