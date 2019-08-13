<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DishComment extends Model
{
    protected $fillable = [
        'dish_id', 'merchant_id', 'content'
    ];

    public function dish() {
        return $this->belongsTo(Dish::class);
    }
}
