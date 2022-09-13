<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest('id')->paginate(10);
        if(count($products) <= 0){
            return response()->json(['message'=>'products not have']);
        }
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|unique:products|min:3|max:50',
            'unitPrice'=>'required|numeric',
            'stock'=>'required|numeric',
            "photos"=>"required",
            "photos.*"=>"file|mimes:jpeg,png|max:512",
        ]);

        DB::beginTransaction();
        try{
                $product = Product::create([
                    'name'=>$request->name,
                    'unitPrice'=>$request->unitPrice,
                    'stock'=>$request->stock,
                ]);

                $photos = [];
                foreach ($request->file('photos') as $key=>$photo){
                    $newName = $photo->store("public");
    //            Photo::create([
    //                "product_id"=>$request->product_id,
    //                "name"=>$newName,
    //            ]);
                    $photos[$key] = new Photo(['name'=>$newName]);
                }
    //        https://laravel.com/docs/9.x/eloquent-relationships#inserting-and-updating-related-models
                $product->Photos()->saveMany($photos);


            DB::commit();
        }
        catch(\exception $e){

            DB::rollBack();
            throw $e;
        }

        return response()->json(['product'=>$product],200);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if(is_null($product)){
            return response()->json(['message'=>'product not found'],404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
//            unique:posts,title,".$this->route('post')->id
            'name'=>'nullable|unique:products,name,'.$id.'|min:3|max:50',
            'unitPrice'=>'nullable|numeric',
            'stock'=>'nullable|numeric',
            "photos"=>"required",
            "photos.*"=>"file|mimes:jpeg,png|max:512",
        ]);

        $product = Product::find($id);

        if(is_null($product)){
            return response()->json(['message'=>'product not found'],404);
        }



        if($request->has('name')){
            $product->name = $request->name;
        }
        if($request->has('unitPrice')){
            $product->unitPrice = $request->unitPrice;
        }
        if($request->has('stock')){
            $product->stock = $request->stock;
        }

        $product->update();

        return response()->json(['message'=>'updated','product'=>$product]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(is_null($product)){
            return response()->json(['message'=>'product not found'],404);
        }else{
            $product->delete();
            return response()->json(['message'=>'product is deleted'],200);
        }

        // status code 204ပြန်လိုက်ရင် message ထဲက စာတွေ return မပြန်ပါ
        return response()->json(['message'=>'product is deleted'],204);


    }
}
