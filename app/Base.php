<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    // 通过Collection生成Original数组
    public static function getOriginalArray(Collection $collection) {
        $OriginalArray = array ();
        foreach ( $collection as $key => $value ) {
            $OriginalArray [$key] = $value->getOriginal ();
        }
        return $OriginalArray;
    }
}
