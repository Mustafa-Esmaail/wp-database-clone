<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/parent-category', function (){
        $parent=App\Category::where('level',0)->get();

    $categoreis=[];
    foreach($parent as $cat){

   
    $category_data = [
        'id' => $cat->id,
        'name' => $cat->name,
        'slug' => $cat->slug,
        'description' => $cat->slug,
        'parent' => 0, // Set to 0 for a top-level category
        'image' => [
            'src' =>(isset($cat->banner) ) ? uploaded_asset($cat->banner): 'https://toolsworldeg.com/public/assets/img/placeholder.jpg',
            'alt' => $cat->name,
        ],
        'meta_data' => [
            [
                'key' => '_yoast_wpseo_title',
                'value' => $cat->meta_title,
            ],
            [
                'key' => '_yoast_wpseo_metadesc',
                'value' =>  $cat->meta_description,
            ],
        ],
    ];
    array_push($categoreis, $category_data);

    }

    return response()->json(['categories' => $categoreis]);

});
Route::get('/sub-category', function (){
        $sub=App\Category::where('level',3)
        ->orderBy('id', 'asc')
        ->skip(0)
        ->take(40)
        ->get();

    $categoreis=[];
    foreach($sub as $cat){
        $parent=App\Category::where('id',$cat->parent_id)->first('name');
      
        

   
    $category_data = [
        'id' => $cat->id,
        'name' => $cat->name,
        'slug' => $cat->slug,
        'description' => $cat->slug,
        'parent' => $parent->name, // Set to 0 for a top-level category
        'image' => [
            'src' =>(isset($cat->banner) ) ? uploaded_asset($cat->banner): 'https://toolsworldeg.com/public/assets/img/placeholder.jpg',
            'alt' => $cat->name,
        ],
        'meta_data' => [
            [
                'key' => '_yoast_wpseo_title',
                'value' => $cat->meta_title,
            ],
            [
                'key' => '_yoast_wpseo_metadesc',
                'value' =>  $cat->meta_description,
            ],
        ],
    ];
    array_push($categoreis, $category_data);

    }

    return response()->json(['categories' => $categoreis]);

});
 Route::get('/brand-clone', function (){
        $parent=App\Brand::get();
    $brands=[];
    foreach($parent as $brand){
    $brand_data = [
        'name' => $brand->name,
        'slug' => $brand->slug,
        'logo' =>  (isset($brand->logo) ) ?  uploaded_asset($brand->logo): '',
    ];
    array_push($brands, $brand_data);
    }
   




    return response()->json(['brands' => $brands]);
});



Route::get('/product-miss-sku', function (){
       

$url = 'https://toolsworld.ivalleytraining.com/wp-json/database-clone/v1/sku';

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session and fetch the content
$content = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Display the output

$data=json_decode($content, true);

  $parent = App\Product::whereIn('sku', $data)
  ->skip(0)
        ->take(100)
        ->get();
    $products=[];
    

   
    foreach($parent as $product){

    $photoar=[];
    if(isset($product->photos) && !empty($product->photos)){
            $photos =  explode(',',$product->photos);

         foreach ($photos as $key => $photo){
         
               array_push($photoar, (isset($photo) ) ? uploaded_asset($photo): 'https://toolsworldeg.com/public/assets/img/placeholder.jpg');
            }
    }
    else{
        array_push($photoar,'https://toolsworldeg.com/public/assets/img/placeholder.jpg');        
    }
    
    $tags = explode(',',$product->tags);
 $tagsar=[];
     foreach ($tags as $key => $tag){
         
               array_push($tagsar, $tag);
            }

    $product_data = [
    'name' => $product->name,
    'type' => 'variable',
    'slug'=>$product->slug,
    'sku' => $product->sku,
    'price' => $product->unit_price,
    'total_sales'=>$product->num_of_sale,
    'price_html' => '<span class=\"woocommerce-Price-amount amount\"><span class=\"woocommerce-Price-currencySymbol\">&#36;</span>'. $product->unit_price .'</span> ' ,
    'regular_price' =>$product->unit_price,
    'description' => $product->description,
    'short_description' => $product->description,
    'categories' => [['id' => $product->category->name]],

        'images' => $photoar,
        'tags' => $tagsar,

    'attributes' => [
        [
            'name' => 'Color',
            'options' =>$product->colors,
            'visible' => true,
            'variation' => true,
        ],
        
    ],
    
    'brands' =>   (isset($product->brand->name) ) ?  $product->brand->name: '',
    'video_link' =>   (isset($product->video_link) ) ?  $product->video_link: '',
    "downloadable"=> (isset($product->pdf)  ) ? true: false,
    "downloads"=>(isset($product->pdf)  ) ?  uploaded_asset($product->pdf): '',
    'stock_quantity' =>  $product->current_stock,
    'manage_stock' => true,
    'meta_data' => [
            [
                'key' => '_meta_title',
                'value' => $product->meta_title,
            ],
            [
                'key' => '_meta_description',
                'value' =>  $product->meta_description,
            ],
            [
                            'key' => '_meta_img',
                            'value' =>    (isset($product->meta_img)  ) ?  uploaded_asset($product->meta_img): '',
                        ],
        ],
];
array_push($products, $product_data);
    }

    return response()->json(['products' => $products]);




  
     



});
Route::get('/product-sku', function (){
       
 $parent=App\Product::
            orderBy('id', 'asc')
        ->get('sku');
        
        

    $products=[];
   
    foreach($parent as $product){

   
array_push($products,  $product->sku);
    }

    return $products;


});
Route::get('/product-clone', function (){
        $parent=App\Product::
            orderBy('id', 'asc')
        ->skip(22000) //زود الف 
        ->take(1000)// بعده اعمل حفظ من الزرار الازرق علي اليمنين
        ->get();// بعدها  روح الtab اللي جمبها و اعمل رفريش
        
        

    $products=[];
   
    foreach($parent as $product){

    $photos = explode(',',$product->photos);
    $photoar=[];
     foreach ($photos as $key => $photo){
         
               array_push($photoar, uploaded_asset($photo));
            }
    $tags = explode(',',$product->tags);
 $tagsar=[];
     foreach ($tags as $key => $tag){
         
               array_push($tagsar, $tag);
            }

    $product_data = [
    'name' => $product->name,
    'type' => 'variable',
    'slug'=>$product->slug,
    'sku' => $product->sku,
    'price' => $product->unit_price,
    'total_sales'=>$product->num_of_sale,
    'price_html' => '<span class=\"woocommerce-Price-amount amount\"><span class=\"woocommerce-Price-currencySymbol\">&#36;</span>'. $product->unit_price .'</span> ' ,
    'regular_price' =>$product->unit_price,
    'description' => $product->description,
    'short_description' => $product->description,
    'categories' => [['id' => $product->category->name]],

        'images' => $photoar,
        'tags' => $tagsar,

    'attributes' => [
        [
            'name' => 'Color',
            'options' =>$product->colors,
            'visible' => true,
            'variation' => true,
        ],
        
    ],
    
    'brands' =>   (isset($product->brand->name) ) ?  $product->brand->name: '',
    'video_link' =>   (isset($product->video_link) ) ?  $product->video_link: '',
    "downloadable"=> (isset($product->pdf)  ) ? true: false,
    "downloads"=>(isset($product->pdf)  ) ?  uploaded_asset($product->pdf): '',
    'stock_quantity' =>  $product->current_stock,
    'manage_stock' => true,
    'meta_data' => [
            [
                'key' => '_meta_title',
                'value' => $product->meta_title,
            ],
            [
                'key' => '_meta_description',
                'value' =>  $product->meta_description,
            ],
            [
                            'key' => '_meta_img',
                            'value' =>    (isset($product->meta_img)  ) ?  uploaded_asset($product->meta_img): '',
                        ],
        ],
];
array_push($products, $product_data);
    }

    return response()->json(['products' => $products]);

});

