<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;


function download ()
{
    $url = 'https://www.taishansports.cn/Public/Upload/image/20221201/1669876513f6f41a455ea57b33.jpg';

    $response = Http::head($url);
    if ($response->ok() && $response->header('Content-Length') > 0) {
        $filename = Str::afterLast($url, '/');
        $urlComponents = parse_url($url);
        $basePath = $urlComponents['scheme'] . '://' . $urlComponents['host'];
        $folderPath = Str::after($urlComponents['path'], '/');
        $folderPath = Str::beforeLast($folderPath, $filename);
        Storage::put($folderPath . $filename, Http::get($url));  
        dd("store: ". $folderPath . $filename);      
    } 
}

function getListData() 
{
    $listCategories = [ 
        [ 'id' => 1, 'name' => 'Gymnastic', 'limit' => 183, 'data' => []], 
        [ 'id' => 2, 'name' => 'Athletics', 'limit' => 155, 'data' => []], 
        [ 'id' => 3, 'name' => 'Balls & Equipment', 'limit' => 99, 'data' => []], 
        [ 'id' => 4, 'name' => 'Judo', 'limit' => 13, 'data' => []], 
        [ 'id' => 116, 'name' => 'Wrestling', 'limit' => 14, 'data' => []], 
        [ 'id' => 117, 'name' => 'Taekwondo', 'limit' => 19, 'data' => []], 
        [ 'id' => 118, 'name' => 'Boxing', 'limit' => 37, 'data' => []], 
        [ 'id' => 42, 'name' => 'Wushu', 'limit' => 22, 'data' => []], 
        [ 'id' => 119, 'name' => 'Karate', 'limit' => 4, 'data' => []], 
        [ 'id' => 44, 'name' => 'Fitness', 'limit' => 129, 'data' => []], 
        [ 'id' => 45, 'name' => 'Education', 'limit' => 37, 'data' => []], 
        [ 'id' => 46, 'name' => 'More', 'limit' => 79, 'data' => []], 
    ];

    foreach($listCategories as $key => $category) {
        $listUrl = "https://taishansports.cn/index.php?a=getProductList&cid={$category['id']}&limit={$category['limit']}";
        $response = Http::get($listUrl); 
        $listData = json_decode($response->body(), true);        
        $listCategories[$key]['data'] = $listData['data']['list'];
    }

    $listCategories = collect($listCategories);

    $fileName = 'datafile.json';

    $response = response( $listCategories->toJson(), 200)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');

    return $response;
}

function getDetail($productId) 
{        
    $detailUrl = "https://taishansports.cn/index.php?a=getProductDetail&id={$productId}";
    $response = Http::get($detailUrl);
    $detailData = json_decode($response->body(), true);

    return $detailData['data']['info'];
}

Route::get('/', function () {

   // return download();
    $productByCategory = Storage::get('public/products.json');
    return $productByCategory;

//     foreach($productByCategory as $category_key => $category) {
//         foreach($category->data as $key => $product) {
//             $product = getDetail($product->id);
//             $product['catid'] = $productByCategory[$category_key]->data[$key]->catid;
//             $productByCategory[$category_key]->data[$key] = $product;   
//         }
//     }

//    return $productByCategory->toJson();
});
