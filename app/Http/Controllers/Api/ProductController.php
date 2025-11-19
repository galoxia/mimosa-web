<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Degree;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show( Product $product, Degree $degree = null )
    {
        if ( $degree ) {
            $degreePrice = $product->prices()->where( 'degree_id', $degree->id )->first();
        }

        $product->price = $degreePrice?->price ?? $product->price;

        return response()->json( compact( 'product' ) );
    }
}
