<?php

namespace App\Http\Resources;

use App\Enums\DpsStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DpsHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'dps_name' => $this->plan->name,
            'dps_id' => $this->dps_id,
            'status' => $this->status->value,
            'is_cancellable' => $this->isCancellable(),
            'created_at' => $this->created_at,
        ];
    }

    public function isCancellable()
    {
        $isRunningOrDue = $this->status == DpsStatus::Running || $this->status == DpsStatus::Due;
        $canCancel = $this->plan->can_cancel && $isRunningOrDue;

        $isFixedCancelable = $this->plan->cancel_type === 'fixed'
            && now()->diffInDays($this->created_at) <= $this->plan->cancel_days;

        $isAnytimeCancelable = $this->plan->cancel_type === 'anytime';

        return $canCancel && ($isFixedCancelable || $isAnytimeCancelable);
    }
}
