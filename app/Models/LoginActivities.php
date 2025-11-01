<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class LoginActivities extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['browser', 'platform'];

    public static function add($user_id = null)
    {
        $model = new static;
        $model->user_id = $user_id ?? auth()->id();
        $model->ip = request()->ip();
        $model->location = getLocation()->name;
        $model->agent = request()->userAgent();
        $model->save();

        return $model;
    }

    private function getAgent($show)
    {
        $agent = new Agent;
        $agent->setUserAgent($this->agent);

        return $agent->$show();
    }

    public function getBrowserAttribute(): string
    {
        return self::getAgent('browser');
    }

    public function getPlatformAttribute(): string
    {
        return self::getAgent('platform');
    }
}
