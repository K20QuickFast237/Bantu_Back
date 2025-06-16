<?php

namespace App\Http\Controllers\Api;

use App\Helper\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateUserRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        $products = Product::query()->paginate(5);

        return ProductResource::collection($products);
    }

    public function save(CreateUserRequest $request){
        $data = $request->validated();
        $data['image'] = CommonHelper::uploadFile($request->file('image'), 'product');

        Product::query()->create($data);

        return response()->json([
            'message' => 'Product saved successfully'
        ], 201);
    }
}
