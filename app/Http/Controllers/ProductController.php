<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProductSimilarity;

class ProductController extends Controller
{
    function index(Request $req)
    {
        $products=DB::table('product')->get();
        $category=DB::table('product_category')->get();
        $cart=DB::table('cart')->get();
        $order=$req->otype;
        $bookdate=$req->bookdate;
        $bookpax=$req->bookpax;
        $booktime=$req->booktime;
        $booktable=$req->booktable;

        return view('catalogue')->with('products',$products)->with('category',$category)->with('cart',$cart)->with('order', $order)->with('bookdate', $bookdate)->with('bookpax', $bookpax)->with('booktable', $booktable)->with('booktime', $booktime);
    }
    function detail($P_Id)
    {
        $detail = Product::find($P_Id);

        //Product Similarity Controller
        $products        = json_decode(file_get_contents(storage_path('data/products-data.json')));
        $selectedId      = intval(app('request')->input('id') ?? '8');
        $selectedProduct = $products[0];
    
        $selectedProducts = array_filter($products, function ($product) use ($selectedId) { return $product->id === $selectedId; });
        if (count($selectedProducts)) {
            $selectedProduct = $selectedProducts[array_keys($selectedProducts)[0]];
        }
    
        $productSimilarity = new ProductSimilarity($products);
        $similarityMatrix  = $productSimilarity->calculateSimilarityMatrix();
        $products          = $productSimilarity->getProductsSortedBySimularity($selectedId, $similarityMatrix);
    
        return view('detail', compact('detail', 'selectedId', 'selectedProduct', 'products'));
    }
    function search(Request $req)
    {
       $data=Product::where('P_Name', 'like', '%'.$req->input('query').'%')->get();
       return view('search', ['product'=>$data]);
    }
}
