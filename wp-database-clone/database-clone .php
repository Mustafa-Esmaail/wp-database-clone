<?php
/*
Plugin Name: Database Clone
Plugin URI: https://github.com/Mustafa-Esmaail/wp-database-clone
Description: Custom API for Clone Database.
Version: 1.0
Author: Mustafa Esmaail
Author URI: https://github.com/Mustafa-Esmaail
*/

if ( ! function_exists( 'wp_crop_image' ) ) {
	include( ABSPATH . 'wp-admin/includes/image.php' );
}
// Prevent direct access to the plugin file


// Register API endpoint
add_action('rest_api_init', 'database_clone_category');

add_action('rest_api_init', 'database_clone_product');

add_action('rest_api_init', 'database_clone_brand');
add_action('rest_api_init', 'database_sku');
function database_sku()
{
  register_rest_route(
    'database-clone/v1',
    '/sku',
    array(
      'methods' => 'GET',
      'callback' => 'database_sku_handler',
      'permission_callback' => '__return_true',
    )
  );
}
function database_clone_category()
{
  register_rest_route(
    'database-clone/v1',
    '/category',
    array(
      'methods' => 'GET',
      'callback' => 'database_clone_category_handler',
      'permission_callback' => '__return_true',
    )
  );
}
function database_clone_product()
{
  register_rest_route(
    'database-clone/v1',
    '/product',
    array(
      'methods' => 'GET',
      'callback' => 'database_clone_product_handler',
      'permission_callback' => '__return_true',
    )
  );
}
function database_clone_brand()
{
  register_rest_route(
    'database-clone/v1',
    '/brand',
    array(
      'methods' => 'GET',
      'callback' => 'database_clone_brand_handler',
      'permission_callback' => '__return_true',
    )
  );
}



function database_sku_handler(WP_REST_Request $request)
{
    

  
  // WooCommerce API credentials
  $consumer_key = 'ck_27e0107113e03925d3d1c8f5957f2332db3d55f1';
  $consumer_secret = 'cs_c2344f43b38675b30e96f7f177c18c52b11b3a67';

  // WooCommerce API URL
  $url = 'https://toolsworld.ivalleytraining.com/wp-json/wc/v3/products';
  // WordPress REST API URL
  $api_url = 'https://toolsworldeg.com/product-sku';
//$api_url = 'https://toolsworldeg.com/parent-category';
  // Make the API request
  // Make the API request

  $response = wp_remote_get($api_url);


  // Check for errors
  if (is_wp_error($response)) {
    echo 'Error: ' . $response->get_error_message();
  } else {
    // Get the response body
    $body = wp_remote_retrieve_body($response);
    
    // Decode the JSON response
    $laravelSKU = json_decode($body, true);
    // Create the URL for the API request

global $wpdb;

// Custom SQL query to retrieve SKUs of WooCommerce products
$query = "
  SELECT postmeta.meta_value
    FROM {$wpdb->posts} AS posts
    INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
    WHERE posts.post_type = 'product'
    AND postmeta.meta_key = '_sku'
";

$skus = $wpdb->get_col($query);
$itemsNotInB=array();
// Display SKUs
if ($skus) {
$itemsNotInB=   array_diff($laravelSKU, $skus);
  



return $itemsNotInB;

} else {
    echo 'No SKUs found.';
}

  
    
    // Display SKUs

 }
   
    
  
 
  
  
  
}





















function database_clone_category_handler(WP_REST_Request $request)
{
  
  

  
  // WooCommerce API credentials
  $consumer_key = 'ck_27e0107113e03925d3d1c8f5957f2332db3d55f1';
  $consumer_secret = 'cs_c2344f43b38675b30e96f7f177c18c52b11b3a67';

  // WooCommerce API URL
  $url = 'https://toolsworld.ivalleytraining.com/wp-json/wc/v3/products/categories';
  // WordPress REST API URL
  $api_url = 'https://toolsworldeg.com/sub-category';
//$api_url = 'https://toolsworldeg.com/parent-category';
  // Make the API request
  // Make the API request
  $args = array(
    'timeout' => 90,
	);
  $response = wp_remote_get($api_url, $args);


  // Check for errors
  if (is_wp_error($response)) {
    echo 'Error: ' . $response->get_error_message();
  } else {
    // Get the response body
    $body = wp_remote_retrieve_body($response);
    // Decode the JSON response
    $data = json_decode($body, true);


    foreach ($data as $categories) {

      foreach ($categories as $cat) {
        $term = get_term_by('name', $cat['parent'], 'product_cat');
        $category_data = [
          'name' =>  $cat['name'],
          'slug' =>  $cat['slug'],
          'description' => '', // $cat[0]['description'],
          'parent' => $term->term_id, // Set to 0 for a top-level category
          'image' => [
            'src' => $cat['image']['src'],
            'alt' => $cat['image']['alt'],
          ],
          'meta_data' => [
            [
              'key' => 'rank_math_title',
              'value' => $cat['meta_data'][0]['value'],
            ],
            [
              'key' => 'rank_math_description',
              'value' => $cat['meta_data'][1]['value'],
            ],
          ],
        ];
       // Make the API request
        $response = wp_remote_post(
          $url,
          [
            'headers' => [
              'Authorization' => 'Basic ' . base64_encode($consumer_key . ':' . $consumer_secret),
              'Content-Type' => 'application/json',
            ],
            'timeout' => 90,
            'body' => json_encode($category_data),
          ]
        );

        // Get the response body
        $response_body = wp_remote_retrieve_body($response);

        // Decode the JSON response
        $response_data = json_decode($response_body, true);

        // Print the response
        print_r($response['response']);
      }

       
    }
  }
  
  
  
  
  
  
}






function database_clone_product_handler(WP_REST_Request $request)
{
  // WooCommerce API credentials
  $consumer_key = 'ck_27e0107113e03925d3d1c8f5957f2332db3d55f1';
  $consumer_secret = 'cs_c2344f43b38675b30e96f7f177c18c52b11b3a67';

  // WooCommerce API URL
  $url = 'https://toolsworld.ivalleytraining.com/wp-json/wc/v3/products';
  // WordPress REST API URL
 // $api_url = 'https://toolsworldeg.com/product-clone';
  $api_url = 'https://toolsworldeg.com/product-miss-sku';// miss product add 

  // Make the API request
  $args = array(
    'timeout' => 90,
	);

// Make the API request using wp_remote_get

  $response = wp_remote_get($api_url, $args);
 


  // Check for errors
  if (is_wp_error($response)) {
    echo 'Error: ' . $response->get_error_message();
  } else {
    // Get the response body
    $body = wp_remote_retrieve_body($response);
    
       
    // Decode the JSON response
    $data = json_decode($body, true);

 foreach ($data as $products) {

      foreach ($products as $product) {
        $imageA = [];
        $tagsA = [];
        
        foreach ($product['images'] as $key => $photo) {
          $img['src'] = $photo;
          $img['position'] = $key;


          array_push($imageA, $img);
        }

        foreach ($product['tags'] as $key => $tag) {
          $tagg['name'] = $tag;



          array_push($tagsA, $tagg);
        }



        $term = get_term_by('name', $product['categories'][0]['id'], 'product_cat');





        $product_data = [
          'name' => $product['name'],
          'type' => 'simple',
          'slug' => $product['slug'],
          'sku' => $product['sku'],
          'price' => $product['price'],
          'total_sales' => $product['total_sales'],
          'price_html' =>  $product['price_html'],
          'regular_price' => $product['regular_price'],
          'description' => $product['description'],
          'short_description' => $product['short_description'],
          'categories' => [['id' => $term->term_id]],
          'images' =>  (isset($imageA) && !empty($imageA) ) ? $imageA:'https://toolsworldeg.com/public/assets/img/placeholder.jpg'  ,
          'attributes' => [
            [
              'name' => 'Color',
              'options' => $product['attributes'][0]['options'],
              'visible' => true,
              'variation' => true,
            ],
            [
              'id'=>2,
              'name' => 'Brand',
              'options' => $product['brands'],
              'visible' => true,
              'variation' => true,
            ],

          ],
          //'brands' => [$product['brands']],
          "downloadable" => $product['downloadable'],
          "downloads" => [
            [
              'name' => $product['name'],
              'file' => $product['downloads'],
            ]
          ],
          'tags' => $tagsA,
          'stock_quantity' => $product['stock_quantity'],
          'manage_stock' => true,
          'meta_data' => [
            [
              'key' => 'rank_math_title',
              'value' => $product['meta_data'][0]['value'],
            ],
            [
              'key' => 'rank_math_description',
              'value' => $product['meta_data'][1]['value'],
            ],
           // [
             // 'key' => 'rank_math_focus_keyword',
              //'value' => $product['meta_data'][2]['value'],
            //],
          ],
        ];
        // Make the API request
        $response = wp_remote_post(
          $url,
          [
            'headers' => [
              'Authorization' => 'Basic ' . base64_encode($consumer_key . ':' . $consumer_secret),
              'Content-Type' => 'application/json',
            ],
             
            'body' => json_encode($product_data),
          ]
        );

        // Get the response body
        $response_body = wp_remote_retrieve_body($response);


        // Decode the JSON response
        $response_data = json_decode($response_body, true);
        if (isset($response_data['id']) && !empty($response_data['id'])) {
          // Specify the post ID
          $post_id = $response_data['id']; // Replace with the actual post ID

          // Specify the meta key and meta value
          $meta_key = '_woodmart_product_video';
          $meta_value = $product['video_link'];

          // Add product video  meta
          if (isset($product['video_link']) && !empty($product['video_link'])) {
            $result = add_post_meta($post_id, $meta_key, $meta_value, true);

            print_r($result);
          }
        }

        // Print the response
        print_r($response_data);
      }
    }
  }
}


function database_clone_brand_handler(WP_REST_Request $request)
{
  // WooCommerce API credentials
  $consumer_key = 'ck_27e0107113e03925d3d1c8f5957f2332db3d55f1';
  $consumer_secret = 'cs_c2344f43b38675b30e96f7f177c18c52b11b3a67';

  // WooCommerce API URL
  $url = 'https://toolsworld.ivalleytraining.com/wp-json/wc/v3/products/attributes/2/terms';



  $api_url = 'https://toolsworldeg.com/brand-clone';

  // Make the API request
  $response = wp_remote_get($api_url);


  // Check for errors
  if (is_wp_error($response)) {
    echo 'Error: ' . $response->get_error_message();
  } else {
    // Get the response body
    $body = wp_remote_retrieve_body($response);
    // Decode the JSON response
    $data = json_decode($body, true);



    foreach ($data as $brands) {
      

      foreach($brands as $brand){
        $attribute_data = [
        'name' => $brand['name'],
        'slug' => $brand['slug'],

      ];
      $attribute_response = wp_remote_post(
        $url,
        array(
          'headers' => array(
            'Authorization' => 'Basic ' . base64_encode($consumer_key . ':' . $consumer_secret),
            'Content-Type' => 'application/json',
          ),
          'body' => json_encode($attribute_data),
        )
      );

      $response_body = wp_remote_retrieve_body($attribute_response);
        $response_data = json_decode($response_body, true);
        
      if (isset($response_data['id']) && !empty($response_data['id'])) {
       
        // Get the file information
        $file_info = wp_upload_bits(basename($brand['logo']), null, file_get_contents($brand['logo']));
        

        // Check for errors in uploading the file
        if (!$file_info['error']) {
          // Set the attachment data

          $attachment_data = array(
            'post_mime_type' => $file_info['type'],
            'post_parent'    => 0, // ID of the parent post (0 for no parent)
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($brand['logo'])),
            'post_content'   => '',
            'post_status'    => 'inherit'
          );

          // Insert the attachment
          $attachment_id = wp_insert_attachment($attachment_data, $file_info['file']);

          // Generate attachment metadata
          $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_info['file']);

          // Update attachment metadata
          wp_update_attachment_metadata($attachment_id, $attachment_data);


          
          
          
          $img = [
            'url' => $file_info['file'],
            'id' => $attachment_id,
          ];
          
          

          add_term_meta($response_data['id'], 'image', $img, true);

          echo 'Attachment added successfully with ID: ' . $attachment_id;
        } else {
          echo 'Error uploading file: ' . $file_info['error'];
        }



        // Print the response
        print_r($response_body);
      }
    }
  }
    }
}
