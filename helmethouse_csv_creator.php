<?php
/* Purpose : Script for converting helmet house CSV into Magento2 compatible product CSV.
   Author : Ritika Bhawsar
   Email : ritika.infowind@gmail.com
   Copyright : Infowind Technologies
   DateTime : 19.Dec.2018 10.36 AM 
*/
namespace app\code\Ves\Brand\Controller\Adminhtml\Brand;
include('functions.php');
error_reporting(1);
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$obj = $bootstrap->getObjectManager();


/* deleteStoreCategories($obj);
function deleteStoreCategories($objectManager)
{
    $categoryFactory = $objectManager->get('Magento\Catalog\Model\CategoryFactory');
    $newCategory = $categoryFactory->create();
    $collection = $newCategory->getCollection();
    $objectManager->get('Magento\Framework\Registry')->register('isSecureArea', true);

    foreach ($collection as $category) {
        $category_id = $category->getId();

        if ($category_id <= 116) {
            continue;
        }

        try {
            $category->delete();
            echo 'Category Removed ' . $category_id . PHP_EOL;
        } catch (\Exception $e) {
            echo 'Failed to remove category ' . $category_id . PHP_EOL;
            echo $e->getMessage() . "\n" . PHP_EOL;
        }
    }
}
die;
*/

function getImage($image)
{
    if(!empty($image))
    {
        $imagePathRoot =  $_SERVER['DOCUMENT_ROOT'].'/motorcsvimport/motorcsvimport/pub/media/catalog/product/'.$image;
        if(!file_exists($imagePathRoot))
        {
            $image = 'http://img.helmethouse.com/'.$image;
        }
    }
    return $image;
}

//Code for reading helmet house master.csv and converting it into associative array
$array = $fields = array(); $i = 0;
$fileName = 'master.csv';
if(isset($_GET['file']) && !empty($_GET['file']))
{
    $fileName = $_GET['file'];
}
$handle = @fopen("pub/media/csv/$fileName", "r");

if ($handle) {
    while (($row = fgetcsv($handle, 4096)) !== false) {
        if (empty($fields)) {
            $fields = $row;
            continue;
        }
        foreach ($row as $k=>$value) {
            $array[$i][$fields[$k]] = $value;
        }
        $i++;
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

$model = array_column($array, 'Model','Alt Part#');
$arrayCount = array_count_values($model);
$details = unique_multidim_array($array,'Model',$arrayCount); 

$configurableArray = array(); 
$associatedProductSku = array();
foreach ($arrayCount as $k=>$ac)
{
    if($ac>1)
    {
        $cArray = array();
        $sku = $name = preg_replace('#[^0-9a-z]+#i', '-', $k);
        $cArray['sku'] = $sku;
        $cArray['store_view_code'] = '';
        $cArray['attribute_set_code'] = 'Default';
        $cArray['product_type'] = 'configurable';

        $categories = array_column($details[$k], 'Category');
        $uniqueCategories = array_unique($categories);
        $validCategoryArray = array_addstuff($uniqueCategories,'Default Category/');
        $allCategories = implode(",",$validCategoryArray);

        $cArray['categories'] = $allCategories;
        $cArray['product_websites'] = 'base';      
        $cArray['name'] = $name; 
        $cArray['description'] = $name; 
        $cArray['short_description'] = $name; 
        $cArray['weight'] = ''; 
        $cArray['product_online'] = 1;
        $cArray['tax_class_name'] = 'Taxable Goods';
        $cArray['visibility'] = 'Catalog, Search';

        $prices = array_column($details[$k], 'MAPP Price'); 
        $finalPrice = $prices[0];
        foreach ($prices as $price) 
        {
            if($finalPrice < $price)
            {
                $finalPrice = $price;
            }
        }
        if(empty($finalPrice))
        {
            $prices = array_column($details[$k], 'Retail');         
            $finalPrice = $prices[0];
            foreach ($prices as $price) 
            {
                if($finalPrice < $price)
                {
                    $finalPrice = $price;
                }
            }
        }
        if(empty($finalPrice))
        {
            $prices = array_column($details[$k], 'Dealer');
            $finalPrice = $prices[0];
            foreach ($prices as $price) 
            {
                if($finalPrice < $price)
                {
                    $finalPrice = $price;
                }
            }
        }
       
        $cArray['price'] = $finalPrice; 
        $cArray['special_price'] = '';
        $cArray['special_price_from_date'] = '';
        $cArray['special_price_to_date'] = '';
        $cArray['url_key'] = $sku; 
        $cArray['meta_title'] = $name; 
        $cArray['meta_keywords'] = $name; 
        $cArray['meta_description'] = $name; 

        $images = array_column($details[$k], 'Photo');
        $uniqueImages = array_unique($images);
        $uniqueImagesArray = array();
        foreach ($uniqueImages as $uniqueImage) 
        {
            if(isset($uniqueImage) && !empty($uniqueImage))
            {
                // $uniqueImagesArray[] = getImage($uniqueImage);
                $uniqueImagesArray[] = $uniqueImage;
            }
        }
        $additionalImages = implode(',', $uniqueImagesArray);

        $labels = array_column($details[$k], 'Alt Photos');
        $uniqueLabels = array_unique($labels);
        
        $image = (isset($uniqueImages[0]) && !empty($uniqueImages[0]) && $uniqueImages[0] != ' ')?$uniqueImages[0]:'';
        $label = (isset($uniqueLabels[0]) && !empty($uniqueLabels[0]) && $uniqueLabels[0] != ' ')?$uniqueLabels[0]:'';
        // $image = getImage($image);
        $cArray['base_image'] = $image; 
        $cArray['base_image_label'] = $label;
        $cArray['small_image'] = $image; 
        $cArray['small_image_label'] = $label;
        $cArray['thumbnail_image'] = $image; //need to check image size with image import
        $cArray['thumbnail_image_label'] = $label;
        $cArray['swatch_image'] = $image;  //need to check image size with image import
        $cArray['swatch_image_label'] = $label;
        $cArray['created_at'] = date('m/d/y, h:i A');
        $cArray['updated_at'] = '';
        $cArray['new_from_date'] = '';
        $cArray['new_to_date'] = '';
        $cArray['display_product_options_in'] = 'Block after Info Column';
        $cArray['map_price'] = '';
        $cArray['msrp_price'] = '';
        $cArray['map_enabled'] = '';
        $cArray['gift_message_available'] = 'Use config';
        $cArray['custom_design'] = '';
        $cArray['custom_design_from'] = '';
        $cArray['custom_design_to'] = '';
        $cArray['custom_layout_update'] = '';
        $cArray['page_layout'] = '';   
        $cArray['product_options_container'] = '';  
        $cArray['msrp_display_actual_price_type'] = '';

        $countryOfManufacture = array_column($details[$k], 'Origin');
        $uniqueCountryOfManufacture = array_unique($countryOfManufacture);
        $origin =  (isset($uniqueCountryOfManufacture[0]) && !empty($uniqueCountryOfManufacture[0]) && $uniqueCountryOfManufacture[0] != ' ')?trim($uniqueCountryOfManufacture[0]):''; 
        $country_of_manufacture = countryCodeToCountry($origin);
        $cArray['country_of_manufacture'] = $country_of_manufacture; 

        $brands = array_column($details[$k], 'Brand');
        $uniqueBrands = array_unique($brands);  
        $brand = (isset($uniqueBrands[0]) && !empty($uniqueBrands[0]) && $uniqueBrands[0] != ' ')?trim($uniqueBrands[0]):''; 
        $allBrand = implode('|', $uniqueBrands);
        $cArray['brand'] = $brand; 

        $cArray['catalog_page'] = ''; 
        $cArray['color'] = '';   
        $cArray['cost'] = ''; 
        $cArray['dealer'] = ''; 
        $cArray['depth'] = ''; 
        $cArray['east'] = ''; 
        $cArray['feature_text_file'] = ''; 
        $cArray['length'] = ''; 
        $cArray['mapp_y_n'] = ''; 
        $cArray['origin'] = ''; 
        $cArray['retail'] = ''; 
        $cArray['size'] = ''; 
        $cArray['ts_dimensions_height'] = '';
        $cArray['ts_dimensions_length'] = '';
        $cArray['ts_dimensions_width'] = '';
        $cArray['upc'] = '';
        $cArray['west'] = '';
        $cArray['width'] = '';     
        $cArray['additional_attributes'] = ''; 
        $cArray['qty'] = 0;
        $cArray['out_of_stock_qty'] = 0;
        $cArray['use_config_min_qty'] = 1;
        $cArray['is_qty_decimal'] = 0;
        $cArray['allow_backorders'] = 0;
        $cArray['use_config_backorders'] = 1;
        $cArray['min_cart_qty'] = 1;
        $cArray['use_config_min_sale_qty'] = 1;
        $cArray['max_cart_qty'] = 10000;
        $cArray['use_config_max_sale_qty'] = 1;
        $cArray['is_in_stock'] = 1;
        $cArray['notify_on_stock_below'] = 1;
        $cArray['use_config_notify_stock_qty'] = 1;
        $cArray['manage_stock'] = 1;
        $cArray['use_config_manage_stock'] = 1;
        $cArray['use_config_qty_increments'] = 1;
        $cArray['qty_increments'] = 1;
        $cArray['use_config_enable_qty_inc'] = 1;
        $cArray['enable_qty_increments'] = 0;
        $cArray['is_decimal_divided'] = 0;
        $cArray['website_id'] = 1;
        $cArray['related_skus'] = '';
        $cArray['related_position'] = '';
        $cArray['crosssell_skus'] = '';
        $cArray['crosssell_position'] = '';
        $cArray['upsell_skus'] = '';
        $cArray['upsell_position'] = '';
        $cArray['additional_images'] = $additionalImages;
        $cArray['additional_image_labels'] = '';
        $cArray['hide_from_product_page'] = '';
        $cArray['bundle_price_type'] = '';
        $cArray['bundle_sku_type'] = '';
        $cArray['bundle_price_view'] = '';
        $cArray['bundle_weight_type'] = '';
        $cArray['bundle_values'] = '';
        $cArray['bundle_shipment_type'] = '';

        // $configurableVariations = array_column($details[$k], 'Category');

        $configurableVariations = array();
        foreach ($details[$k] as $detail) 
        {
            $attributeDetails = '';
            $configurableVariationLabels = '';
            // if(isset($detail['Alt Part#']) && !empty($detail['Alt Part#']) && $detail['Alt Part#'] != ' ')
            // {
            //     $attributeDetails = 'sku='.$detail['Alt Part#'];
            //     $associatedProductSku[] = $detail['Alt Part#'];
            // }
            if(isset($detail['UPC']) && !empty($detail['UPC']) && $detail['UPC'] != ' ')
            {
                $attributeDetails = 'sku='.$detail['UPC'];
                $associatedProductSku[] = $detail['UPC'];
            }
            if(isset($detail['Color']) && !empty($detail['Color']) && $detail['Color'] != ' ')
            {
                $configurableVariationLabels .= 'color=Color,';
                $attributeDetails .= ',color='.$detail['Color'];
            }
            if(isset($detail['Size']) && !empty($detail['Size']) && $detail['Size'] != ' ')
            {
                $configurableVariationLabels .= 'size=Size';
                $attributeDetails .= ',size='.$detail['Size'];
            }

            $configurableVariations[] = $attributeDetails;
        }

        $configurableVariations = implode('|', $configurableVariations);

        $cArray['configurable_variations'] = $configurableVariations;
        $cArray['configurable_variation_labels'] = trim($configurableVariationLabels);
        $cArray['associated_skus'] = '';            
        $configurableArray[] = $cArray;
    }    
}

//Convert helmet house csv array into magento2 compatible array for product CSV
$data = array();
foreach ($array as $key=>$value) {  
    $sku = '';
    if(isset($value['Model']) && !empty($value['Model']) && $value['Model'] != ' ')
    {
        $sku  = $value['Model'];
    }
    // if(isset($value['Alt Part#']) && !empty($value['Alt Part#']) && $value['Alt Part#'] != ' ')
    // {
    //     $sku  .= '-'.$value['Alt Part#'];
    // }
    if(isset($value['UPC']) && !empty($value['UPC']) && $value['UPC'] != ' ')
    {
        $sku  .= '-'.$value['UPC'];
    }
    // if(isset($value['Color']) && !empty($value['Color']) && $value['Color'] != ' ')
    // {
    //     $sku .= '-'.$value['Color'];
    // }
    // if(isset($value['Size']) && !empty($value['Size']) && $value['Size'] != ' ')
    // {
    //     $sku .= '-'.$value['Size'];
    // }

   // $data[$key]['sku'] = (isset($value['Alt Part#']) && !empty($value['Alt Part#']) && $value['Alt Part#']!=' ')?$value['Alt Part#']:'';
    $data[$key]['sku'] = (isset($value['UPC']) && !empty($value['UPC']) && $value['UPC']!=' ')?trim($value['UPC']):'';
    $data[$key]['store_view_code'] = '';
    $data[$key]['attribute_set_code'] = 'Default';

   // if(in_array($value['Alt Part#'], $associatedProductSku))
    if(in_array($value['UPC'], $associatedProductSku))
    {
        $data[$key]['product_type'] = 'virtual';
    }
    else
    {
        $data[$key]['product_type'] = 'simple';
    }

    $data[$key]['categories'] = (isset($value['Category']) && !empty($value['Category']) && $value['Category']!=' ')?'Default Category/'.$value['Category']:'';

    $data[$key]['product_websites'] = 'base';

    $data[$key]['name'] = str_replace('/', '-', $sku);  

    $data[$key]['description'] = (isset($value['Long Description']) && !empty($value['Long Description']) && $value['Long Description'] != ' ')?$value['Long Description']:'';

    $data[$key]['short_description'] = (isset($value['Description']) && !empty($value['Description']) && $value['Description'] != ' ')?$value['Description']:'';

    $data[$key]['weight'] = (isset($value['Weight']) && !empty($value['Weight']) && $value['Weight'] != ' ')?$value['Weight']:'';

    $data[$key]['product_online'] = 1;
    $data[$key]['tax_class_name'] = 'Taxable Goods';


   // if(in_array($value['Alt Part#'], $associatedProductSku))
    if(in_array($value['UPC'], $associatedProductSku))
    {
        $data[$key]['visibility'] = 'Not Visible Individually';
    }
    else
    {
        $data[$key]['visibility'] = 'Catalog, Search';
    }


    $price =  (isset($value['MAPP Price']) && !empty($value['MAPP Price']) && $value['MAPP Price'] != ' ')?$value['MAPP Price']:0;
    if(!isset($price) || empty($price) || $price ==' ')
    {
        $price =  (isset($value['Retail']) && !empty($value['Retail']) && $value['Retail'] != ' ')?$value['Retail']:0;
    }
    if(!isset($price) || empty($price) || $price ==' ')
    {
        $price =  (isset($value['Dealer']) && !empty($value['Dealer']) && $value['Dealer'] != ' ')?$value['Dealer']:0;
    }

    $data[$key]['price'] = $price; 


    $data[$key]['special_price'] = '';
    $data[$key]['special_price_from_date'] = '';
    $data[$key]['special_price_to_date'] = '';

    $data[$key]['url_key'] = preg_replace('#[^0-9a-z]+#i', '-', $sku); 

    $data[$key]['meta_title'] = (isset($value['Model']) && !empty($value['Model']) && $value['Model'] != ' ')?$value['Model']:''; 
    $data[$key]['meta_keywords'] = (isset($value['Model']) && !empty($value['Model']) && $value['Model'] != ' ')?$value['Model']:''; 
    $data[$key]['meta_description'] = (isset($value['Model']) && !empty($value['Model']) && $value['Model'] != ' ')?$value['Model']:''; 

    $photo = (isset($value['Photo']) && !empty($value['Photo']) && $value['Photo'] != ' ')?$value['Photo']:''; 
    $label = (isset($value['Alt Photos']) && !empty($value['Alt Photos']) && $value['Alt Photos'] != ' ')?$value['Alt Photos']:''; 
   // $photo = getImage($photo);
    $data[$key]['base_image'] = $photo;
    $data[$key]['base_image_label'] = $label;
    $data[$key]['small_image'] = $photo; 
    $data[$key]['small_image_label'] = $label;
    $data[$key]['thumbnail_image'] = $photo; //need to check image size with image import
    $data[$key]['thumbnail_image_label'] = $label;
    $data[$key]['swatch_image'] = $photo;  //need to check image size with image import
    $data[$key]['swatch_image_label'] = $label;

    $data[$key]['created_at'] = date('m/d/y, h:i A');
    $data[$key]['updated_at'] = '';
    $data[$key]['new_from_date'] = '';
    $data[$key]['new_to_date'] = '';
    $data[$key]['display_product_options_in'] = 'Block after Info Column';

    $data[$key]['map_price'] = (isset($value['MAPP Price']) && !empty($value['MAPP Price']) && $value['MAPP Price'] != ' ')?$value['MAPP Price']:'';

    $data[$key]['msrp_price'] = '';
    $data[$key]['map_enabled'] = '';

   // if(in_array($value['Alt Part#'], $associatedProductSku))
    if(in_array($value['UPC'], $associatedProductSku))
    {
        $data[$key]['gift_message_available'] = 'No';
    }
    else
    {   
        $data[$key]['gift_message_available'] = 'Use config';
    }
    $data[$key]['custom_design'] = '';
    $data[$key]['custom_design_from'] = '';
    $data[$key]['custom_design_to'] = '';
    $data[$key]['custom_layout_update'] = '';
    $data[$key]['page_layout'] = '';   
    $data[$key]['product_options_container'] = '';  
    $data[$key]['msrp_display_actual_price_type'] = '';

    $origin =  (isset($value['Origin']) && !empty($value['Origin']) && $value['Origin'] != ' ')?trim($value['Origin']):''; 
    $country_of_manufacture = countryCodeToCountry($origin);
    $data[$key]['country_of_manufacture'] = $country_of_manufacture; 

    $brand = (isset($value['Brand']) && !empty($value['Brand']) && $value['Brand'] != ' ')?$value['Brand']:'';
    $data[$key]['brand'] = $brand;

    $data[$key]['catalog_page'] = (isset($value['Catalog Page']) && !empty($value['Catalog Page']) && $value['Catalog Page'] != ' ')?$value['Catalog Page']:'';


    $color = (isset($value['Color']) && !empty($value['Color']) && $value['Color'] != ' ')?$value['Color']:'';
    $colorAdd = (!empty($color))?createAttributeValue(93,'color',$color,$obj):false;
    $data[$key]['color'] = $color;

    $data[$key]['cost'] = '';

    $data[$key]['dealer'] = (isset($value['Dealer']) && !empty($value['Dealer']) && $value['Dealer'] != ' ')?$value['Dealer']:'';

    $data[$key]['depth'] = (isset($value['Depth']) && !empty($value['Depth']) && $value['Depth'] != ' ')?$value['Depth']:'';

    $data[$key]['east'] = (isset($value['East']) && !empty($value['East']) && $value['East'] != ' ')?$value['East']:'';

    $data[$key]['feature_text_file'] = (isset($value['Feature Text File']) && !empty($value['Feature Text File']) && $value['Feature Text File'] != ' ')?$value['Feature Text File']:'';

    $data[$key]['length'] = (isset($value['Length']) && !empty($value['Length']) && $value['Length'] != ' ')?$value['Length']:'';

    $data[$key]['mapp_y_n'] = (isset($value['MAPP Y/N']) && !empty($value['MAPP Y/N']) && $value['MAPP Y/N'] != ' ')?$value['MAPP Y/N']:'';

    $data[$key]['origin'] = (isset($value['Origin']) && !empty($value['Origin']) && $value['Origin'] != ' ')?$value['Origin']:'';

    $data[$key]['retail'] = (isset($value['Retail']) && !empty($value['Retail']) && $value['Retail'] != ' ')?$value['Retail']:'';


    $size = (isset($value['Size']) && !empty($value['Size']) && $value['Size'] != ' ')?trim($value['Size']):'';
    $sizeAdd = (!empty($size))?createAttributeValue(155,'size',$size,$obj):false; 
    $data[$key]['size'] = $size;

    $data[$key]['ts_dimensions_height'] = '';
    $data[$key]['ts_dimensions_length'] = '';
    $data[$key]['ts_dimensions_width'] = '';

    $data[$key]['upc'] = (isset($value['UPC']) && !empty($value['UPC']) && $value['UPC'] != ' ')?$value['UPC']:'';

    $data[$key]['west'] = (isset($value['West']) && !empty($value['West']) && $value['West'] != ' ')?$value['West']:'';

    $data[$key]['width'] = (isset($value['Width']) && !empty($value['Width']) && $value['Width'] != ' ')?$value['Width']:'';

    $part_number = (isset($value['Part Number']) && !empty($value['Part Number']) && $value['Part Number'] != ' ')?$value['Part Number']:'';
    $class = (isset($value['Class']) && !empty($value['Class']) && $value['Class'] != ' ')?$value['Class']:'';
    $product_size = (isset($value['Size']) && !empty($value['Size']) && $value['Size'] != ' ')?$value['Size']:'';
    $vendor_inventory_status = (isset($value['Status']) && !empty($value['Status']) && $value['Status'] != ' ')?$value['Status']:'';
    
    //Method for creating brand if not exists.
    $return = createBrand($brand,$obj);

    $additional_attributes = '';

    if(!empty($part_number))
    {
        $additional_attributes .= 'part_number='.$part_number.',';
    }
     if(!empty($class))
    {
        $additional_attributes .= 'class='.$class.',';
    }
     if(!empty($product_size))
    {
        $additional_attributes .= 'product_size='.$product_size.',';
    }
    if(!empty($brand))
    {
        $additional_attributes .= 'product_brand='.$brand.',';
    }
    if(!empty($vendor_inventory_status))
    {
        $additional_attributes .= 'vendor_inventory_status='.$vendor_inventory_status.',';
    }

    $additional_attributes = substr($additional_attributes,0,-1);
    $data[$key]['additional_attributes'] = trim($additional_attributes); //need to work for Size, color if not available than need to add 

    $data[$key]['qty'] = (isset($value['TTL Qty']) && !empty($value['TTL Qty']) && $value['TTL Qty'] != ' ')?$value['TTL Qty']:'';

    $data[$key]['out_of_stock_qty'] = 0;
    $data[$key]['use_config_min_qty'] = 1;
    $data[$key]['is_qty_decimal'] = 0;
    $data[$key]['allow_backorders'] = 0;
    $data[$key]['use_config_backorders'] = 1;
    $data[$key]['min_cart_qty'] = 1;
    $data[$key]['use_config_min_sale_qty'] = 1;
    $data[$key]['max_cart_qty'] = 10000;
    $data[$key]['use_config_max_sale_qty'] = 1;

    $is_in_stock = 0;
    if($value['Status'] == 'OK' || $value['Status'] == 'On Sale')
    {
        $is_in_stock = 1;
    }

    $data[$key]['is_in_stock'] = $is_in_stock;

    $data[$key]['notify_on_stock_below'] = 1;
    $data[$key]['use_config_notify_stock_qty'] = 1;
    $data[$key]['manage_stock'] = 1;
    $data[$key]['use_config_manage_stock'] = 1;
    $data[$key]['use_config_qty_increments'] = 1;
    $data[$key]['qty_increments'] = 1;
    $data[$key]['use_config_enable_qty_inc'] = 1;
    $data[$key]['enable_qty_increments'] = 0;
    $data[$key]['is_decimal_divided'] = 0;
    $data[$key]['website_id'] = 1;
    $data[$key]['related_skus'] = '';
    $data[$key]['related_position'] = '';
    $data[$key]['crosssell_skus'] = '';
    $data[$key]['crosssell_position'] = '';
    $data[$key]['upsell_skus'] = '';
    $data[$key]['upsell_position'] = '';
    $data[$key]['additional_images'] = $photo;
    $data[$key]['additional_image_labels'] = '';
    $data[$key]['hide_from_product_page'] = '';
    $data[$key]['bundle_price_type'] = '';
    $data[$key]['bundle_sku_type'] = '';
    $data[$key]['bundle_price_view'] = '';
    $data[$key]['bundle_weight_type'] = '';
    $data[$key]['bundle_values'] = '';
    $data[$key]['bundle_shipment_type'] = '';
    $data[$key]['configurable_variations'] = '';
    $data[$key]['configurable_variation_labels'] = '';
    $data[$key]['associated_skus'] = '';
}

$finalData = array_merge($data,$configurableArray);



//Code for creating CSV for Magento2 products
$filePath = 'pub/media/csv/m2_hh.csv';
$fh1 = @fopen($filePath, 'w');
$i=0;
foreach ( $finalData as $data1 ) {
    if($i == 0)
    {
        $dataH = array_keys($data1);       
        fputcsv($fh1, $dataH);
        $i++;
    }
    // Put the data into the stream
    fputcsv($fh1, $data1);
}
// Close the file
fclose($fh1);
// Make sure nothing else is sent, our file is done
echo "Magento2 compatible CSV file have been generated on path : ".$filePath;