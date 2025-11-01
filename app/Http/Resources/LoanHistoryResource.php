<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'plan_name' => $this->plan->name,
            'loan_id' => $this->loan_no,
            'status' => $this->status->value,
            'is_cancellable' => $this->isCancellable(),
            'created_at' => $this->created_at->format('d M Y h:i A'),
        ];
    }

    public function isCancellable()
    {
        return $this->status == \App\Enums\LoanStatus::Reviewing;
    }
}
