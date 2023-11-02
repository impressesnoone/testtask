<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'title',
        'description',
        'slug',
        'p_id',
        'cost',
        'width',
        'height',
        'weight',
    ];
    public static function getCategories(){
        // Получаем одним запросом все разделы
        $arr = self::orderBy('p_id')->get();
        // Запускаем рекурсивную постройку дерева и отдаем на выдачу
        return self::buildTree($arr, 0);
    }

    public static function buildTree($arr, $pid = 0) {
        // Находим всех детей раздела
        $found = $arr->filter(function($item) use ($pid){return $item->p_id == $pid; });

        // Каждому детю запускаем поиск его детей и записываем в свойство sub
        foreach ($found as $key => $cat) {
            $sub = self::buildTree($arr, $cat->id);
            // То что ниже можно заменить на - $cat->sub = $sub;
            $found[$key]->sub = $sub;
        }

        return $found;
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_products')->withPivot('count');
    }
}
