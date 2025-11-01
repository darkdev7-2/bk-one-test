<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
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
            'message' => $this->message,
            'avatar' => $this->getAvatarPath(),
            'name' => $this->user->first_name.' '.$this->user->last_name,
            'email' => $this->user->email,
            'is_admin' => $this->model == 'admin',
            'attachments' => collect(json_decode($this->attachments, true))->map(function ($attachment) {
                return asset($attachment);
            }),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }

    private function getAvatarPath()
    {
        if ($this->model == 'admin') {
            return asset('front/images/user.jpg');
        }

        return $this->user->avatar !== null && file_exists(base_path('assets/'.$this->user->avatar)) ? asset($this->user->avatar) : asset('front/images/user.jpg');
    }
}
