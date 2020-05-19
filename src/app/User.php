<?php

namespace App;

use App\Traits\ApiTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes,
        ApiTrait;

    /**
     * Set a constant with expression in value, in a static content
     *
     * @return string
     */
    public static function API_BORDER_URL() {
        return env('COVID19_DSP_API') . 'border/checkpoint';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'checkpoint', 'username', 'import_order_number', 'generic_password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

}
