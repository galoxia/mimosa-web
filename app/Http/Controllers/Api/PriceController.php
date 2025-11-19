<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Degree;
use App\Models\Product;
use Illuminate\Http\Request;

class PriceController extends Controller
{
//    public function get( Product $product, Degree $degree = null )
//    {
//        if ( $degree ) {
//            $degreePrice = $product->prices()->where( 'degree_id', $degree->id )->first();
//        }
//
//        $price = $degreePrice?->price ?? $product->price;
//
//        return response()->json( compact( 'price' ) );
//    }
}
