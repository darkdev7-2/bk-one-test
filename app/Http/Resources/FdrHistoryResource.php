<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FdrHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fdr_name' => $this->plan->name,
            'fdr_id' => $this->fdr_id,
            'status' => $this->status->value,
            'is_cancellable' => $this->isCancellable(),
            'created_at' => $this->created_at->format('d M Y h:i A'),
        ];
    }

    public function isCancellable()
    {
        $isRunningOrDue = $this->status == \App\Enums\FdrStatus::Running;
        $canCancel = $this->plan->can_cancel && $isRunningOrDue;

        $isFixedCancelable = $this->plan->cancel_type === 'fixed'
            && now()->diffInDays($this->created_at) <= $this->plan->cancel_days;

        $isAnytimeCancelable = $this->plan->cancel_type === 'anytime';

        return $canCancel && ($isFixedCancelable || $isAnytimeCancelable);
    }
}
