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


$array = $fields = array(); $i = 0;
$handle = @fopen("pub/media/csv/Sullivans_Web_Price_List_New.csv", "r");
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

$model = array_column($array,'ITEM','PART NO'); //part no need to change with UPC code
$arrayCount = array_count_values($model);
$details = unique_multidim_array($array,'ITEM',$arrayCount); 

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

        $categories = array_column($details[$k], 'STYLE');
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

        $prices = array_column($details[$k], 'QTY $'); 
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
            $prices = array_column($details[$k], 'RTL $');         
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
            $prices = array_column($details[$k], 'DLR $');
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
       // $cArray['url_key'] = $sku; 
        $cArray['meta_title'] = $name; 
        $cArray['meta_keywords'] = $name; 
        $cArray['meta_description'] = $name; 

        $images = array_column($details[$k], 'IMAGE NAME A');
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

        $labels = array_column($details[$k], 'IMAGE NAME A');
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

        $countryOfManufacture = array_column($details[$k], 'ORIGIN');
        $uniqueCountryOfManufacture = array_unique($countryOfManufacture);
        $origin =  (isset($uniqueCountryOfManufacture[0]) && !empty($uniqueCountryOfManufacture[0]) && $uniqueCountryOfManufacture[0] != ' ')?trim($uniqueCountryOfManufacture[0]):''; 
        $country_of_manufacture = countryCodeToCountry($origin);
        $cArray['country_of_manufacture'] = $country_of_manufacture; 

        $brands = array_column($details[$k], 'VENDOR');
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
            // if(isset($detail['PART NO']) && !empty($detail['PART NO']) && $detail['PART NO'] != ' ')
            // {
            //     $attributeDetails = 'sku='.$detail['PART NO'];
            //     $associatedProductSku[] = $detail['PART NO'];
            // }
            if(isset($detail['UPC CODE']) && !empty($detail['UPC CODE']) && $detail['UPC CODE'] != ' ')
            {
                $attributeDetails = 'sku='.trim($detail['UPC CODE']);
                $associatedProductSku[] = trim($detail['UPC CODE']);
            }
            if(isset($detail['COLOR']) && !empty($detail['COLOR']) && $detail['COLOR'] != ' ')
            {
                $configurableVariationLabels .= 'color=Color,';
                $attributeDetails .= ',color='.$detail['COLOR'];
            }
            if(isset($detail['SIZE']) && !empty($detail['SIZE']) && $detail['SIZE'] != ' ')
            {
                $configurableVariationLabels .= 'size=Size';
                $attributeDetails .= ',size='.$detail['SIZE'];
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


// echo "configurableArray : <pre>";
// print_r($configurableArray);

// echo "associatedProductSku : <pre>";
// print_r($associatedProductSku);




//Convert sullivans csv array into magento2 compatible array for product CSV
$data = array();
foreach ($array as $key=>$value) {  

    // $data[$key]['sku'] = (isset($value['PART NO']) && !empty($value['PART NO']) && $value['PART NO']!=' ')?$value['PART NO']:'';
    $data[$key]['sku'] = (isset($value['UPC CODE']) && !empty($value['UPC CODE']) && $value['UPC CODE']!=' ')?trim($value['UPC CODE']):'';
    $data[$key]['store_view_code'] = '';
    $data[$key]['attribute_set_code'] = 'Default';

  //  if(in_array($value['PART NO'], $associatedProductSku))
    if(in_array(trim($value['UPC CODE']), $associatedProductSku))
    {
        $data[$key]['product_type'] = 'virtual';
    }
    else
    {
        $data[$key]['product_type'] = 'simple';
    }

    $data[$key]['categories'] = (isset($value['STYLE']) && !empty($value['STYLE']) && $value['STYLE']!=' ')?'Default Category/'.$value['STYLE']:'';

    $data[$key]['product_websites'] = 'base';

    $name = '';
    if(isset($value['ITEM']) && !empty($value['ITEM']) && $value['ITEM'] != ' ')
    {
        $name  = $value['ITEM'];
    }
    if(isset($value['UPC CODE']) && !empty($value['UPC CODE']) && $value['UPC CODE'] != ' ')
    {
        $name  .= '-'.trim($value['UPC CODE']);
    }

    $data[$key]['name'] = str_replace('/', '-', $name);  

    $data[$key]['description'] = (isset($value['DESCRIPTION']) && !empty($value['DESCRIPTION']) && $value['DESCRIPTION'] != ' ')?$value['DESCRIPTION']:'';

    $data[$key]['short_description'] = (isset($value['DESCRIPTION']) && !empty($value['DESCRIPTION']) && $value['DESCRIPTION'] != ' ')?$value['DESCRIPTION']:'';

    $data[$key]['weight'] = '';

    $data[$key]['product_online'] = 1;
    $data[$key]['tax_class_name'] = 'Taxable Goods';


    //if(in_array($value['PART NO'], $associatedProductSku))
    if(in_array($value['UPC CODE'], $associatedProductSku))
    {
        $data[$key]['visibility'] = 'Not Visible Individually';
    }
    else
    {
        $data[$key]['visibility'] = 'Catalog, Search';
    }


    $price =  (isset($value['QTY $']) && !empty($value['QTY $']) && $value['QTY $'] != ' ')?$value['QTY $']:0;
    if(!isset($price) || empty($price) || $price ==' ')
    {
        $price =  (isset($value['RTL $']) && !empty($value['RTL $']) && $value['RTL $'] != ' ')?$value['RTL $']:0;
    }
    if(!isset($price) || empty($price) || $price ==' ')
    {
        $price =  (isset($value['DLR $']) && !empty($value['DLR $']) && $value['DLR $'] != ' ')?$value['DLR $']:0;
    }

    $data[$key]['price'] = $price; 


    $data[$key]['special_price'] = '';
    $data[$key]['special_price_from_date'] = '';
    $data[$key]['special_price_to_date'] = '';

   // $data[$key]['url_key'] = preg_replace('#[^0-9a-z]+#i', '-', $sku); 

    $data[$key]['meta_title'] = $name;
    $data[$key]['meta_keywords'] = $name;; 
    $data[$key]['meta_description'] = $name; 

    $photo = (isset($value['IMAGE NAME A']) && !empty($value['IMAGE NAME A']) && $value['IMAGE NAME A'] != ' ')?$value['IMAGE NAME A']:''; 
    $label = (isset($value['IMAGE NAME A']) && !empty($value['IMAGE NAME A']) && $value['IMAGE NAME A'] != ' ')?$value['IMAGE NAME A']:''; 
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

    $data[$key]['map_price'] = '';

    $data[$key]['msrp_price'] = '';
    $data[$key]['map_enabled'] = '';

   // if(in_array($value['PART NO'], $associatedProductSku))
    if(in_array($value['UPC CODE'], $associatedProductSku))
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

    $origin =  (isset($value['ORIGIN']) && !empty($value['ORIGIN']) && $value['ORIGIN'] != ' ')?trim($value['ORIGIN']):''; 
    $country_of_manufacture = countryCodeToCountry($origin);
    $data[$key]['country_of_manufacture'] = $country_of_manufacture; 

    $brand = (isset($value['VENDOR']) && !empty($value['VENDOR']) && $value['VENDOR'] != ' ')?$value['VENDOR']:'';
    $data[$key]['brand'] = $brand;

    $data[$key]['catalog_page'] = '';


    $color = (isset($value['COLOR']) && !empty($value['COLOR']) && $value['COLOR'] != ' ')?$value['COLOR']:'';
    $colorAdd = (!empty($color))?createAttributeValue(93,'color',$color,$obj):false;
    $data[$key]['color'] = $color;

    $data[$key]['cost'] = '';

    $data[$key]['dealer'] = (isset($value['DLR $']) && !empty($value['DLR $']) && $value['DLR $'] != ' ')?$value['DLR $']:'';

    $data[$key]['depth'] = '';

    $data[$key]['east'] = '';

    $data[$key]['feature_text_file'] = '';

    $data[$key]['length'] = '';

    $data[$key]['mapp_y_n'] = '';

    $data[$key]['origin'] = (isset($value['ORIGIN']) && !empty($value['ORIGIN']) && $value['ORIGIN'] != ' ')?$value['ORIGIN']:'';

    $data[$key]['retail'] = (isset($value['RTL $']) && !empty($value['RTL $']) && $value['RTL $'] != ' ')?$value['RTL $']:'';


    $size = (isset($value['SIZE']) && !empty($value['SIZE']) && $value['SIZE'] != ' ')?trim($value['SIZE']):'';
    $sizeAdd = (!empty($size))?createAttributeValue(155,'size',$size,$obj):false; 
    $data[$key]['size'] = $size;

    $data[$key]['ts_dimensions_height'] = '';
    $data[$key]['ts_dimensions_length'] = '';
    $data[$key]['ts_dimensions_width'] = '';

    $data[$key]['upc'] = (isset($value['UPC CODE']) && !empty($value['UPC CODE']) && $value['UPC CODE'] != ' ')?trim($value['UPC CODE']):'';

    $data[$key]['west'] = '';

    $data[$key]['width'] = '';

    $part_number = (isset($value['PART NO']) && !empty($value['PART NO']) && $value['PART NO'] != ' ')?$value['PART NO']:'';
    $vendor_code = (isset($value['VENDOR CODE']) && !empty($value['VENDOR CODE']) && $value['VENDOR CODE'] != ' ')?$value['VENDOR CODE']:'';
    $gender = (isset($value['GENDER']) && !empty($value['GENDER']) && $value['GENDER'] != ' ')?$value['GENDER']:'';
    $al_qty = (isset($value['AL - QTY']) && !empty($value['AL - QTY']) && $value['AL - QTY'] != ' ')?$value['AL - QTY']:0;
    $ma_qty = (isset($value['MA - QTY']) && !empty($value['MA - QTY']) && $value['MA - QTY'] != ' ')?$value['MA - QTY']:0;
    $nv_qty = (isset($value['NV - QTY']) && !empty($value['NV - QTY']) && $value['NV - QTY'] != ' ')?$value['NV - QTY']:0;
    $lbs = (isset($value['LBS']) && !empty($value['LBS']) && $value['LBS'] != ' ')?$value['LBS']:0;
    $kgm = (isset($value['KGM']) && !empty($value['KGM']) && $value['KGM'] != ' ')?$value['KGM']:0;
    $product_size = (isset($value['SIZE']) && !empty($value['SIZE']) && $value['SIZE'] != ' ')?$value['SIZE']:'';
    $vendor_inventory_status = (isset($value['STATUS']) && !empty($value['STATUS']) && $value['STATUS'] != ' ')?$value['STATUS']:'';
    
    //Method for creating brand if not exists.
    $return = createBrand($brand,$obj);

    $additional_attributes = '';

    if(!empty($part_number))
    {
        $additional_attributes .= 'part_number='.$part_number.',';
    }
    if(!empty($vendor_code))
    {
        $additional_attributes .= 'vendor_code='.$vendor_code.',';
    }
    if(!empty($gender))
    {
        $additional_attributes .= 'gender='.$gender.',';
    }
    if(!empty($al_qty))
    {
        $additional_attributes .= 'al_qty='.$al_qty.',';
    }
    if(!empty($ma_qty))
    {
        $additional_attributes .= 'ma_qty='.$ma_qty.',';
    }
    if(!empty($nv_qty))
    {
        $additional_attributes .= 'nv_qty='.$nv_qty.',';
    }
    if(!empty($lbs))
    {
        $additional_attributes .= 'lbs='.$lbs.',';
    }
    if(!empty($kgm))
    {
        $additional_attributes .= 'kgm='.$kgm.',';
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

    $data[$key]['qty'] = (isset($value['TOTAL QTY']) && !empty($value['TOTAL QTY']) && $value['TOTAL QTY'] != ' ')?$value['TOTAL QTY']:'';

    $data[$key]['out_of_stock_qty'] = 0;
    $data[$key]['use_config_min_qty'] = 1;
    $data[$key]['is_qty_decimal'] = 0;
    $data[$key]['allow_backorders'] = 0;
    $data[$key]['use_config_backorders'] = 1;
    $data[$key]['min_cart_qty'] = 1;
    $data[$key]['use_config_min_sale_qty'] = 1;
    $data[$key]['max_cart_qty'] = 10000;
    $data[$key]['use_config_max_sale_qty'] = 1;

    // $is_in_stock = 0;
    // if($value['Status'] == 'OK' || $value['Status'] == 'On Sale') //need to work
    // {
        $is_in_stock = 1;
    // }

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
$filePath = 'pub/media/csv/m2_ss.csv';
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