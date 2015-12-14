<?php

namespace App\Models;

use Elocache\Observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    const SEND_TYPE_QUEUE = 'QUEUE';
    const SEND_TYPE_SYNC = 'NOW';

    protected $fillable = ['to', 'send_type', 'subject', 'html', 'origin', 'from', 'reply_to'];

    public static function boot()
    {
        parent::boot();

        Self::observe(new BaseObserver());
    }
}
