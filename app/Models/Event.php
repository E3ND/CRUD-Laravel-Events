<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // dizendo que a variavel items é array
    protected $casts = [
        'items' => 'array'
    ];

    protected $dates = ['date'];

    // update
    protected $guarded = [];

    // Pertece a apenas um usuario
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    // many to many
    public function users() {
        return $this->belongsToMany('App\Models\User');
    }
}
