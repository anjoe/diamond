<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    protected $table = 'packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'password', 'name', 'cover', 'user_id',
        'card_num', 'type', 'delete_at'
    ];

    public $timestamps = true;
    protected $dates = ['delete_at'];
}