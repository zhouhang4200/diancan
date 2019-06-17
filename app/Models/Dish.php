<?php

namespace App\Models;

use App\Category;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = [
        'name', 'amount', 'original_amount', 'rate', 'logo',
        'material', 'category_id', 'merchant_id', 'tag', 'intro'
    ];

    /**
     * @param $query
     * @param array $filters
     * @return mixed
     */
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['name']) {
            $query->where('name', 'like', "%".$filters['name']."%");
        }

        if ($filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
