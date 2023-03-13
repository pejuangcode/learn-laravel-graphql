<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CurlThaisanJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thaisan:get-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getJson();
    }

    public function downloadAssetsProduct()
    {
       
        $productByCategory = Storage::get('public/products.json');
        $productByCategory = collect(json_decode($productByCategory));
        
        foreach($productByCategory as $category_key => $category) {

            //besok 45
            if ($productByCategory[$category_key]->id === 46) {
                foreach($category->data as $key => $product) {   
                    $this->info($key.' - '.$productByCategory[$category_key]->id.' - '.$productByCategory[$category_key]->name.' - '.' '.$product->id); 
                    // if ($key > 128) {
                        /*
                            storing single image
                        */
                        // if (!empty($product->image)) {
                        //     $this->download($product->image);
                        // }

                        // if (!empty($product->file)) {
                        //     $this->download($product->file);
                        // }

                        // /*
                        //     storing single multiple image
                        // */
                        // foreach($product->images as $i => $image) {
                        //     if (!empty($image)) {
                        //         $this->download($image);
                        //     }
                        // }
                        
                        if (! is_null($product->case)) {
                            foreach($product->case as $i => $case) {
                                if (!empty($case->image)) {
                                    $this->download($case->image);
                                }
                            }
                        }
                        
                    // } else {
                    //     $this->info($product->id.' skip');
                    // }
                }
            }
        }
    }

    public function getJson() 
    {
        $productByCategory = Storage::get('public/datafile.json');
        $productByCategory = collect(json_decode($productByCategory));
        
        foreach($productByCategory as $category_key => $category) {
            foreach($category->data as $key => $product) {
                $product = $this->getDetail($product->id);
                $product['catid'] = $productByCategory[$category_key]->data[$key]->catid;

                // $product['image']= str_replace('\/', '/', $product['image']);
                // $product['image']= str_replace('//', '/', $product['image']);



                $this->info($product['image']);
                
                $productByCategory[$category_key]->data[$key] = $product; 
                $this->info($category_key.' - '.$key.' done...');  
            }
        }
        
        $fileName = 'products2.json';   
        Storage::put('public/'.$fileName, $productByCategory->toJson());   
        $this->info('data berhasil di simpan di /public'); 
    }

    public function getListData() 
    {
        $listCategories = [ 
            [ 'id' => 1, 'name' => 'Gymnastic', 'limit' => 183, 'data' => []], //done, 
            [ 'id' => 2, 'name' => 'Athletics', 'limit' => 155, 'data' => []], //done
            [ 'id' => 3, 'name' => 'Balls & Equipment', 'limit' => 99, 'data' => []], //done
            [ 'id' => 4, 'name' => 'Judo', 'limit' => 13, 'data' => []], // done
            [ 'id' => 116, 'name' => 'Wrestling', 'limit' => 14, 'data' => []], // done
            [ 'id' => 117, 'name' => 'Taekwondo', 'limit' => 19, 'data' => []], // done
            [ 'id' => 118, 'name' => 'Boxing', 'limit' => 37, 'data' => []], // done
            [ 'id' => 42, 'name' => 'Wushu', 'limit' => 22, 'data' => []], // done
            [ 'id' => 119, 'name' => 'Karate', 'limit' => 4, 'data' => []], // done
            [ 'id' => 44, 'name' => 'Fitness', 'limit' => 129, 'data' => []], // done
            [ 'id' => 45, 'name' => 'Education', 'limit' => 37, 'data' => []], //done
            [ 'id' => 46, 'name' => 'More', 'limit' => 79, 'data' => []], // done
        ];
        
        foreach($listCategories as $key => $category) {
            $listUrl = "https://taishansports.cn/index.php?a=getProductList&cid={$category['id']}&limit={$category['limit']}";
            $response = Http::get($listUrl); 
            $listData = json_decode($response->body(), true);        
            $listCategories[$key]['data'] = $listData['data']['list'];
        }
        
        return $listCategories;
    }

    public function getDetail($productId) 
    {        
        $detailUrl = "https://taishansports.cn/index.php?a=getProductDetail&id={$productId}";
        $response = Http::get($detailUrl);
        $detailData = json_decode($response->body(), true);
    
        return $detailData['data']['info'];
    }

    public function download($url)
    {
        $response = Http::head($url);
        if ($response->ok() && $response->header('Content-Length') > 0) {
            $filename = Str::afterLast($url, '/');
            $urlComponents = parse_url($url);
            $basePath = $urlComponents['scheme'] . '://' . $urlComponents['host'];
            $folderPath = Str::after($urlComponents['path'], '/');
            $folderPath = Str::beforeLast($folderPath, $filename);
            Storage::put($folderPath . $filename, Http::get($url));  
            $this->info("store: ". $folderPath . $filename);      
        } 
    }
}
