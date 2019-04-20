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
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$obj = $bootstrap->getObjectManager();

echo '<br>'.date('Y-m-d H:i:s').'-----------------------------------------\n';

$array = $fields = array(); $i = 0;

$fileName = 'Sullivans_Web_Price_List_New.csv';
$path = "/home/motorcyclewholes/public_html/pub/media/csv/";
// if(isset($_GET['file']) && !empty($_GET['file']))
// {
//     $path = "pub/media/csv/ss_split/";
//     $fileName = $_GET['file'];
// }


//$handle = @fopen("pub/media/csv/Sullivans_Web_Price_List_New.csv", "r");
$handle = @fopen($path.$fileName, "r");
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
                $attributeDetails = 'sku='.$detail['UPC CODE'];
                $associatedProductSku[] = $detail['UPC CODE'];
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
    $data[$key]['sku'] = (isset($value['UPC CODE']) && !empty($value['UPC CODE']) && $value['UPC CODE']!=' ')?$value['UPC CODE']:'';
    $data[$key]['store_view_code'] = '';
    $data[$key]['attribute_set_code'] = 'Default';

  //  if(in_array($value['PART NO'], $associatedProductSku))
    if(in_array($value['UPC CODE'], $associatedProductSku))
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

// echo "data : <pre>";
// print_r($data);

// echo "configurableArray : <pre>";
// print_r($configurableArray);
// die;

$finalData = array_merge($data,$configurableArray);

$app_state = $obj->get('\Magento\Framework\App\State');  
$app_state->setAreaCode('frontend');

$missingRecords = array();
foreach ($finalData as $fdata) 
{ 
    $sku =  (isset($fdata['sku']) && !empty($fdata['sku']) && $fdata['sku'] != ' ')?trim($fdata['sku']):'';  
    if(isset($sku) && !empty($sku))
    {
        $productResponse[] = saveProducts($fdata,$obj);   
    }
    else
    {
        $name = (isset($fdata['name']) && !empty($fdata['name']) && $fdata['name'] != ' ')?trim($fdata['name']):''; 
       // echo "<br>SKU is missing for product :".$name;
        $missingRecords[] = $name;
    }
}
echo "<br>missingRecords : <pre>";
print_r($missingRecords);
die;

/* Code for associated virtual product entry in mgto_marketplace_assignproduct_associated_products table
$virtualProduct = array();
$virtualPriceQty = array();
foreach ($productResponse as $product) 
{
	if(!empty($product['configurableArray']))
	{
		foreach($product['configurableArray'] as $key=>$value)
		{
			foreach ($value as $key1 => $value1) 
			{
                //Assigning configurable product ids into virtual product Ids
				$virtualProduct[$value1] = $key ; 
			}
		}
	}

    if(!empty($product['virtualArray']))
    {
        foreach ($product['virtualArray'] as $key => $value) 
        {
            $virtualPriceQty[$key] = $value; 
        }
            
    }
}

foreach ($virtualProduct as $virtualIdKey => $configurableIdValue) 
{   
    $resources = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ResourceConnection');
    $connection= $resources->getConnection();

    $parent_id = '';
    $qty = $virtualPriceQty[$virtualIdKey]['qty'];
    $price = $virtualPriceQty[$virtualIdKey]['price'];
    $themeTable = $resources->getTableName('mgto_marketplace_assignproduct_associated_products');
    $sql = "INSERT INTO " . $themeTable . "(product_id, parent_id, parent_product_id,qty,price,options) VALUES ('".$virtualIdKey."','".$parent_id."','".$configurableIdValue."','".$qty."','".$price."','')";
    $response = $connection->query($sql);
}
die;
*/
function saveProducts($array,$obj)
{
    $product = $obj->create('\Magento\Catalog\Model\Product');  
    $sku =  (isset($array['sku']) && !empty($array['sku']) && $array['sku'] != ' ')?trim($array['sku']):'';  
    $product->setSku($sku); // Set your sku here
    $productId = $product->getIdBySku($sku);
    $associativeProduct = array(); 
    if(isset($productId) && !empty($productId))
    {
        $product->load($productId);    
        echo "<br>load product id : ".$productId;
        $resource = $obj->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        $tableName = $resource->getTableName('mgto_marketplace_product'); 
        $sql = "Select * FROM " . $tableName. " where mageproduct_id = $productId"; 
        $result = $connection->fetchRow($sql);

        
        if(is_array($result) && !empty($result))
        {
            $themeTable1 = $resource->getTableName('mgto_marketplace_assignproduct_items');
            $sql1 = "Select * FROM " . $themeTable1. " where product_id = $productId"; 
            $result1 = $connection->fetchRow($sql1);  
            $assign_id = 0;
            if(isset($result1) && is_array($result1) && !empty($result1))
            {
                $assign_id = $result1['id'];
            }          
            // echo '<br>assign_id : '. $assign_id;   
            if(!$assign_id) 
            {
                $entity_id = $result['entity_id'];
                $owner_id = $result['seller_id'];
                $seller_id = 8; //seller_id need to update on live
                // echo '<br>owner_id  : '.$owner_id ;
                if(isset($owner_id) && !empty($owner_id) && $owner_id != $seller_id)
                {                 
                    $qty = (isset($array['qty']) && !empty($array['qty']) && $array['qty'] != ' ')?$array['qty']:''; 
                    $price = (isset($array['price']) && !empty($array['price']) && $array['price'] != ' ')?$array['price']:''; 
                    $description = (isset($array['description']) && !empty($array['description']) && $array['description'] != ' ')?trim(str_replace("'","\"" , $array['description'])):''; 
                    $condition = 1;
                    $product_type = (isset($array['product_type']) && !empty($array['product_type']) && $array['product_type'] != ' ')?$array['product_type']:''; 
                    $created_at = date('Y-m-d H:i:s');
                    $status = 1;                   
                   // if($product_type != 'virtual')
                    //{
                        $themeTable2 = $resource->getTableName('mgto_marketplace_assignproduct_items');
                        $sql2 = "INSERT INTO " . $themeTable2 . "(`product_id`, `owner_id`, `seller_id`,`qty`,`price`,`description`,`options`,`image`,`mgto_marketplace_assignproduct_items`.`condition`,`type`,`created_at`,`status`) VALUES ('".$productId."','".$owner_id."','".$seller_id."','".$qty."','".$price."','".$description."','','','".$condition."','".$product_type."','".$created_at."','".$status."')";
                        $response2 = $connection->query($sql2); 
                        $lastInsertId2 = $connection->lastInsertId();

                        if(isset($lastInsertId2) && !empty($lastInsertId2))
                        {
                            $themeTable3 = $resource->getTableName('mgto_marketplace_assignproduct_data');
                            $sql3 = "Select * FROM " . $themeTable3. " where assign_id = $assign_id"; 
                            $result3 = $connection->fetchRow($sql3);  
                            $assign_id3 = 0;
                            if(isset($result3) && is_array($result3) && !empty($result3))
                            {
                                $assign_id3 = $result3['assign_id'];
                            }          
                            if(!$assign_id3)
                            {
                                if($product_type != 'virtual')
                                {
                                    $date = date('Y-m-d H:i:s');
                                    if(isset($description) && !empty($description))
                                    {
                                        $type = 2;
                                        $assign_id = $lastInsertId2;
                                        $value = $description;
                                        $is_default = 1;
                                        $status = 1;
                                        
                                        $store_view = 1;
                                        $themeTable4 = $resource->getTableName('mgto_marketplace_assignproduct_data');
                                        $sql4 = "INSERT INTO " . $themeTable4 . "(type, assign_id, value,date,is_default,status,store_view) VALUES ('".$type."','".$assign_id."','".$value."','".$date."','".$is_default."','".$status."','".$store_view."')"; 
                                        $response4 = $connection->query($sql4);
                                        $lastInsertId4 = $connection->lastInsertId();
                                    }                      
                                    $images =  array();
                                    if(isset($array['base_image']) && !empty($array['base_image']) && $array['base_image'] !='')   
                                    {
                                        $images[] = $array['base_image'];
                                    }
                                    if(isset($array['additional_images']) && !empty($array['additional_images']) && $array['additional_images'] !='')
                                    {                              
                                        if(strpos($array['additional_images'],','))
                                        {
                                            $images1 = explode(',',$array['additional_images']);
                                            foreach ($images1 as $image1) 
                                            {
                                                $images[] = $image1;
                                            }
                                        }
                                        else
                                        {
                                            $images[] = $array['additional_images'];
                                        }

                                    }
                                    $imgArray = array_unique($images);
                                    foreach($imgArray as $img)
                                    {
                                        if(!empty($img))
                                        {
                                            //$imagePathRoot =  $_SERVER['DOCUMENT_ROOT'].'/pub/media/marketplace/assignproduct/product/'.$img;
                                            $imagePathRoot =  '/home/motorcyclewholes/public_html/pub/media/marketplace/assignproduct/product/'.$img;

                                            if(!file_exists($imagePathRoot))
                                            {
                                                // if(!strpos($baseImage, 'img.helmethouse.com'))
                                                // { 
                                                    $remote_file = '/Sullivans/Images/All_Products/'.$img;
                                                     
                                                    /* FTP Account */
                                                    $ftp_host = 'ftp.sullivansusaftp.com'; /* host */
                                                    $ftp_user_name = 'sullivans'; /* username */
                                                    $ftp_user_pass = 'sullivans'; /* password */
                                                     
                                                     
                                                    /* New file name and path for this file */
                                                    $local_file = 'pub/media/marketplace/assignproduct/product/'.$img;
                                                     
                                                    /* Connect using basic FTP */
                                                    $connect_it = ftp_connect( $ftp_host );
                                                     
                                                    /* Login to FTP */
                                                    $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
                                                     
                                                    /* Download $remote_file and save to $local_file */
                                                    if ( ftp_get( $connect_it, $local_file, $remote_file, FTP_BINARY ) ) {
                                                        echo "WOOT! Successfully written to $local_file\n";                    
                                                    }
                                                    else {
                                                        echo "Doh! There was a problem\n";                     
                                                    }

                                                /* Close the connection */
                                                ftp_close( $connect_it );
                                                // }

                                            }

                                            if(file_exists($imagePathRoot))
                                            {
                                                try
                                                {
                                                    $product->addImageToMediaGallery($imagePathRoot, array('image', 'small_image', 'thumbnail','swatch'), false, false);
                                                }
                                                catch(Exception $e)
                                                {
                                                    echo $e->getMessage();
                                                }
                                            }
                                        }

                                        $type = 1;
                                        $assign_id = $lastInsertId2;
                                        $value = $img;
                                        $is_default = 1;
                                        $status = 1;
                                        $store_view = 1;
                                        $themeTable5 = $resource->getTableName('mgto_marketplace_assignproduct_data');
                                        $sql5 = "INSERT INTO " . $themeTable5 . "(type, assign_id, value,date,is_default,status,store_view) VALUES ('".$type."','".$assign_id."','".$value."','". $date."','".$is_default."','".$status."','".$store_view."')"; 
                                        $response5 = $connection->query($sql5);
                                        $lastInsertId5 = $connection->lastInsertId();
                                    }

                                }

                                if($product_type == 'configurable')
                                {
                                    $productTypeInstance = $product->getTypeInstance();
                                    $usedProducts = $productTypeInstance->getUsedProducts($product);
                                    foreach ($usedProducts  as $child) 
                                    {
                                        $associatedProductId = $child->getId();

                                       // echo '<br>associatedProductId : '.$associatedProductId;

                                        $parent_id = $lastInsertId2;
                                        $parent_product_id = $productId;  

                                        $themeTable51 = $resource->getTableName('mgto_marketplace_assignproduct_items');
                                        $sql51 = "Select * FROM " . $themeTable51. " where product_id = $associatedProductId"; 
                                        $result51 = $connection->fetchRow($sql51);                                
                                        if(isset($result51) && is_array($result51) && !empty($result51))
                                        {
                                            $qty1 = $result51['qty'];
                                            $price1 = $result51['price'];                                           
                                            $themeTable6 = $resource->getTableName('mgto_marketplace_assignproduct_associated_products');
                                            $sql6 = "INSERT INTO " . $themeTable6 . "(product_id, parent_id, parent_product_id,qty,price,options) VALUES ('".$associatedProductId."','".$parent_id."','".$parent_product_id."','".$qty1."','".$price1."','')"; 
                                            $response6 = $connection->query($sql6);
                                            $lastInsertId6 = $connection->lastInsertId();
                                            if(isset($lastInsertId6) && !empty($lastInsertId6))
                                            {
                                                $themeTable61 = $resource->getTableName('mgto_marketplace_assignproduct_items');
                                                $sql61 = "delete FROM " . $themeTable61. " where product_id = $associatedProductId"; 
                                                $response61 = $connection->query($sql61);
                                            }
                                        }                                                                                          
                                    }                    
                                }   
                               
                            } // $assign_id2 if close
                        } //$lastInsertId if close
                    //}//product Type != virual                   
                }// owner id if close 
            } // close if assign_id 
            return true;
        } // result if close
    }

    $product->setAttributeSetId(4); // Attribute set id

    $productType = (isset($array['product_type']) && !empty($array['product_type']) && $array['product_type'] != ' ')?$array['product_type']:'';              
    $product->setTypeId($productType); // type of product (simple/virtual/downloadable/configurable)


    $categoryIds = (isset($array['categories']) && !empty($array['categories']) && $array['categories'] != ' ')?$array['categories']:'';    
    $categoryNameArray = explode(',', $categoryIds);
    $categoryIdsArray = array();    
    foreach ($categoryNameArray as $categoryName) 
    {
        $catName = explode('/', $categoryName);
        if(is_array($catName) && !empty($catName))
        {
            $catId = getCategoryIdByName($catName[1],$obj);
            if(!isset($catId) || empty($catId) || $catId = ' ')
            {
                $catId = createCategory($catName[1],$obj);
            }
            $categoryIdsArray[] = $catId;
        }
    }    

    $product->setCategoryIds($categoryIdsArray);  //need to check
    $name = (isset($array['name']) && !empty($array['name']) && $array['name'] != ' ')?trim($array['name']):'';  
    if($productType == 'virtual')
    {
        $name = $name.'-'.$sku;  
    }
    
    $product->setName($name); // Name of Product

    // $url_key = (isset($array['url_key']) && !empty($array['url_key']) && $array['url_key'] != ' ')?$array['url_key']:'';
    // $product->setUrlKey($url_key);

    $pdescription =  (isset($array['description']) && !empty($array['description']) && $array['description'] != ' ')?trim($array['description']):'';      
    $product->setDescription($pdescription);

    $pshortdescription =  (isset($array['short_description']) && !empty($array['short_description']) && $array['short_description'] != ' ')?trim($array['short_description']):''; 
    $product->setShortDescription($pshortdescription);

    $weight = (isset($array['weight']) && !empty($array['weight']) && $array['weight'] != ' ')?$array['weight']:'';  
    $product->setWeight($weight); // weight of product    

    //product_online method is missing 
    $taxClassName = (isset($array['tax_class_name']) && !empty($array['tax_class_name']) && $array['tax_class_name'] != ' ')?$array['tax_class_name']:'';         
    $taxClassId = 2;  
    switch ($taxClassName) {
            case 'None':
                $taxClassId = 0;     
                break;
            case 'Taxable Goods':
                $taxClassId = 2;     
                break;
            case 'Refund Adjustments':
                $taxClassId = 4;     
                break;
            case 'Gift Options':
                $taxClassId = 5;     
                break;
            case 'Order Gift Wrapping':
                $taxClassId = 6;     
                break;
            case 'Item Gift Wrapping':
                $taxClassId = 7;     
                break;        
            case 'Printed Gift Card':
                $taxClassId = 8;     
                break; 
            case 'Reward Points':
                $taxClassId = 9;     
                break;       
        }          
    $product->setTaxClassId($taxClassId); // Tax class id    

    $visibilty = (isset($array['visibility']) && !empty($array['visibility']) && $array['visibility'] != ' ')?$array['visibility']:'';
    $visibilityCode = 4;  
    switch ($visibilty) {
            case 'Not Visible Individually':
                $visibilityCode = 1;     
                break;
            case 'Catalog':
                $visibilityCode = 2;     
                break;
            case 'Search':
                $visibilityCode = 3;     
                break;
            case 'Catalog, Search':
                $visibilityCode = 4;     
                break;
        }        
    $product->setVisibility($visibilityCode); // visibilty of product (catalog / search / catalog, search / Not visible individually)

    $price = (isset($array['price']) && !empty($array['price']) && $array['price'] != ' ')?$array['price']:''; 
    $product->setPrice($price); // price of product


    $metatitle = (isset($array['meta_title']) && !empty($array['meta_title']) && $array['meta_title'] != ' ')?$array['meta_title']:'';      
    $product->setMetaTitle($metatitle);

    $metakey = (isset($array['meta_keywords']) && !empty($array['meta_keywords']) && $array['meta_keywords'] != ' ')?$array['meta_keywords']:''; 
    $product->setMetaKeyword($metakey);

    $metadesc = (isset($array['meta_description']) && !empty($array['meta_description']) && $array['meta_description'] != ' ')?$array['meta_description']:''; 
    $product->setMetaDescription($metadesc);


    //map_price , gift_message_available methods missing
    $countryOfManufacture = (isset($array['origin']) && !empty($array['origin']) && $array['origin'] != ' ')?trim(strtoupper($array['origin'])):''; 
    $product->setCountryOfManufacture($countryOfManufacture);

    // $countryOfManufacture = (isset($array['country_of_manufacture']) && !empty($array['country_of_manufacture']) && $array['country_of_manufacture'] != ' ')?$array['country_of_manufacture']:''; 
    // //$product->setCountryOfManufacture($countryOfManufacture);
    // $product->setData('country_of_manufacture',$countryOfManufacture);

    $product->setStatus(1); // Status on product enabled/ disabled 1/0
    
    $catalogPage = (isset($array['catalog_page']) && !empty($array['catalog_page']) && $array['catalog_page'] != ' ')?$array['catalog_page']:''; 
   // $product->setCustomAttribute(149,$catalogPage);
    $product->setData('catalog_page',$catalogPage);

    $color = (isset($array['color']) && !empty($array['color']) && $array['color'] != ' ')?$array['color']:''; 
    $attr = $product->getResource()->getAttribute('color');
    $avid = $attr->getSource()->getOptionId($color);
    $product->setData('color',$avid);


    $dealer = (isset($array['dealer']) && !empty($array['dealer']) && $array['dealer'] != ' ')?$array['dealer']:'';
    $product->setData('dealer',$dealer);  

    $feature_text_file = (isset($array['feature_text_file']) && !empty($array['feature_text_file']) && $array['feature_text_file'] != ' ')?$array['feature_text_file']:''; 
    $product->setData('feature_text_file',$feature_text_file);  
    
    $map_price = (isset($array['map_price']) && !empty($array['map_price']) && $array['map_price'] != ' ')?$array['map_price']:''; 
    $product->setData('map_price',$map_price);  

    $gift_message_available = (isset($array['gift_message_available']) && !empty($array['gift_message_available']) && $array['gift_message_available'] != ' ')?$array['gift_message_available']:''; 
    $product->setData('gift_message_available',$gift_message_available);  

    $brand = (isset($array['brand']) && !empty($array['brand']) && $array['brand'] != ' ')?$array['brand']:''; 
    $product->setData('brand',$brand);  

    $upc = (isset($array['upc']) && !empty($array['upc']) && $array['upc'] != ' ')?$array['upc']:''; 
    $product->setData('upc',$upc);

    $depth = (isset($array['depth']) && !empty($array['depth']) && $array['depth'] != ' ')?$array['depth']:''; 
    $product->setData('depth',$depth); 

    $east = (isset($array['east']) && !empty($array['east']) && $array['east'] != ' ')?$array['east']:''; 
    $product->setData('east',$east); 

    $length = (isset($array['length']) && !empty($array['length']) && $array['length'] != ' ')?$array['length']:''; 
    $product->setData('length',$length); 

    $mapp_y_n = (isset($array['mapp_y_n']) && !empty($array['mapp_y_n']) && $array['mapp_y_n'] != ' ')?$array['mapp_y_n']:''; 
    $product->setData('mapp_y_n',$mapp_y_n); 

    $origin = (isset($array['origin']) && !empty($array['origin']) && $array['origin'] != ' ')?$array['origin']:''; 
    $product->setData('origin',$origin); 

    $retail = (isset($array['retail']) && !empty($array['retail']) && $array['retail'] != ' ')?$array['retail']:''; 
    $product->setData('retail',$retail); 

    $size = (isset($array['size']) && !empty($array['size']) && $array['size'] != ' ')?$array['size']:''; 
    $attrsize = $product->getResource()->getAttribute('size');
    $avidsize = $attrsize->getSource()->getOptionId($size);
    $product->setData('size',$avidsize); 

    $west = (isset($array['west']) && !empty($array['west']) && $array['west'] != ' ')?$array['west']:''; 
    $product->setData('west',$west);

    $width = (isset($array['width']) && !empty($array['width']) && $array['width'] != ' ')?$array['width']:''; 
    $product->setData('width',$width);

    $website_id = (isset($array['website_id']) && !empty($array['website_id']) && $array['website_id'] != ' ')?$array['website_id']:''; 
    $product->setData('website_id',$website_id); //need to work


    $additionalAttributes = (isset($array['additional_attributes']) && !empty($array['additional_attributes']) && $array['additional_attributes'] != ' ')?$array['additional_attributes']:''; 
    // $product->setData('additional_attributes',$additionalAttributes);

    $additionalAttributesArray = explode(',', $additionalAttributes);
    foreach ($additionalAttributesArray as $additionalAttribute) 
    {
        $additionalAttributeArray = explode('=',$additionalAttribute);
        if($additionalAttributeArray[0] == 'part_number')
        {
            $product->setData('part_number',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'vendor_code')
        {
            $product->setData('vendor_code',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'gender')
        {
            $product->setData('gender',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'al_qty')
        {
            $product->setData('al_qty',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'ma_qty')
        {
            $product->setData('ma_qty',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'nv_qty')
        {
            $product->setData('nv_qty',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'lbs')
        {
            $product->setData('lbs',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'kgm')
        {
            $product->setData('kgm',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'product_size')
        {
             $product->setData('product_size',$additionalAttributeArray[1]);
        }
        if($additionalAttributeArray[0] == 'product_brand')
        {
            $attrProductBrand = $product->getResource()->getAttribute('product_brand');
            $avidProductBrand = $attrProductBrand->getSource()->getOptionId($additionalAttributeArray[1]);
            $product->setData('product_brand',$avidProductBrand);
        } 
        if($additionalAttributeArray[0] == 'vendor_inventory_status')
        {
            $vendor_inventory_status = 'Active';
            switch ($additionalAttributeArray[1]) {
                case 'A':
                    $vendor_inventory_status = 'Active';
                    break;
                case 'C':
                    $vendor_inventory_status = 'Closeout';
                    break;
                case 'D':
                    $vendor_inventory_status = 'Discontinued';
                    break;    
                case 'S':
                    $vendor_inventory_status = 'Sale';
                    break;    
                case 'N':
                    $vendor_inventory_status = 'New';
                    break;  
                case 'O':
                    $vendor_inventory_status = 'Off Map';
                    break;
                case 'T':
                    $vendor_inventory_status = 'Terminated';
                    break;
                case 'Z':
                    $vendor_inventory_status = 'Snow';
                    break;
            }
            $product->setData('vendor_inventory_status',$vendor_inventory_status);
        }
        // if(!empty($additionalAttributeArray[1]))
        // {
        //  $product->setData($additionalAttributeArray[0],$additionalAttributeArray[1]);
        // }
    }  
    $qty =  (isset($array['qty']) && !empty($array['qty']) && $array['qty'] != ' ')?$array['qty']:0;    
    $product->setStockData(
                            array(
                                'use_config_manage_stock' => $array['use_config_manage_stock'],
                                'use_config_max_sale_qty' => $array['use_config_max_sale_qty'],
                                'manage_stock' => $array['manage_stock'],
                                'min_sale_qty' => $array['min_cart_qty'],
                                'max_sale_qty' => $array['max_cart_qty'],
                                'is_in_stock' => $array['is_in_stock'],
                                'notify_on_stock_below'=> $array['notify_on_stock_below'],
                                'qty_increments' => $array['qty_increments'],
                                'qty' => $qty
                            )
                        );

    $product->setWebsiteIds(array(1));  
    $product->setData('website_ids', array(1));
    
     
    if(empty($productId))  // need to work
    {
        // Adding Image to product
        $baseImage = (isset($array['base_image']) && !empty($array['base_image']) && $array['base_image'] != ' ')?$array['base_image']:'';  
        if(!empty($baseImage))
        {
            // $imagePathRoot =  $_SERVER['DOCUMENT_ROOT'].'/pub/media/catalog/product/'.$baseImage;
            $imagePathRoot =  '/home/motorcyclewholes/public_html/pub/media/catalog/product/'.$baseImage;

            if(!file_exists($imagePathRoot))
            {
                // if(!strpos($baseImage, 'img.helmethouse.com'))
                // { 
                    $remote_file = '/Sullivans/Images/All_Products/'.$baseImage;
                     
                    /* FTP Account */
                    $ftp_host = 'ftp.sullivansusaftp.com'; /* host */
                    $ftp_user_name = 'sullivans'; /* username */
                    $ftp_user_pass = 'sullivans'; /* password */
                     
                     
                    /* New file name and path for this file */
                    $local_file = 'pub/media/catalog/product/'.$baseImage;
                     
                    /* Connect using basic FTP */
                    $connect_it = ftp_connect( $ftp_host );
                     
                    /* Login to FTP */
                    $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
                     
                    /* Download $remote_file and save to $local_file */
                    if ( ftp_get( $connect_it, $local_file, $remote_file, FTP_BINARY ) ) {
                        echo "WOOT! Successfully written to $local_file\n";                    
                    }
                    else {
                        echo "Doh! There was a problem\n";                     
                    }

                /* Close the connection */
                ftp_close( $connect_it );
                // }

            }

            if(file_exists($imagePathRoot))
            {
                try
                {
                    $product->addImageToMediaGallery($imagePathRoot, array('image', 'small_image', 'thumbnail','swatch'), false, false);
                }
                catch(Exception $e)
                {
                    echo $e->getMessage();
                }
               
                // if($product->save())
                // {
                //     $return = true;
                // }
            }
        }

        $additionalImages = (isset($array['additional_images']) && !empty($array['additional_images']) && $array['additional_images'] != ' ')?$array['additional_images']:''; 
        if(!empty($additionalImages))
            {
                $additionalImagesArray = explode(',', $additionalImages);
                if(sizeof($additionalImagesArray) > 1) {
                    /* Assign additional images to existing products */
                   // $product = $_objectManager->create('Magento\Catalog\Model\Product')->load($newProdId);
                    // $productRepository = $obj->create('Magento\Catalog\Api\ProductRepositoryInterface');
                    // $productRepository->save($product);

                    for ( $i=1; $i<sizeof($additionalImagesArray); $i++ ) {
                        $prdbasepath  ='/home/motorcyclewholes/public_html/pub/media/'; //change path URL on live
                        echo '<br>Add Images :' . $prdbasepath.basename(trim($additionalImagesArray[$i])) . PHP_EOL;
                        $image_directory = $prdbasepath.'data'.'/'.basename(trim($additionalImagesArray[$i]));
                        echo '<br>image_directory : '.$image_directory; 
                        if (file_exists($image_directory) && getimagesize($image_directory)) {

                            echo '<br>File exists'.PHP_EOL; 
                            $product->addImageToMediaGallery($image_directory, array('image', 'small_image', 'thumbnail'), false, false);
                           // $product->save();
                            $product->getResource()->save($product);
                        }
                    }

                }
            }

    }

    //if($product->save())
    if($product->getResource()->save($product))
    {
        $return = true;
    }

    $productId = $product->getId();
    echo '<br>productId : '.$productId;
    $configurableArray = array();
    if($productType == 'configurable') //need to work
    {

        // $configProduct = $obj->create('Magento\Catalog\Model\Product')->load($productId);
        // $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);

        $position = 0;
        $configurableVariations = (isset($array['configurable_variations']) && !empty($array['configurable_variations']) && $array['configurable_variations'] != ' ')?$array['configurable_variations']:'';        
        $configurableVariationsArray = explode('|',$configurableVariations);
        $allSkuIds = array();
        $attributeCodes = array();
        foreach ($configurableVariationsArray as $configurableVariation) 
        {
            $configurableVariationArray1 = explode(',', $configurableVariation);
            foreach ($configurableVariationArray1 as $CVA) 
            {
                $CVArray = explode('=', $CVA);
                if($CVArray[0] == 'sku')
                {
                    $allSkuIds[] = $product->getIdBySku($CVArray[1]);
                } 
                if($CVArray[0] == 'color')
                {
                    $attributeCodes[] = 93;
                }   
                if($CVArray[0] == 'size')
                {
                   $attributeCodes[] = 155;
                }            
            }
        }
        $productId = $product->getId();
        $attributes = $attributeCodes; // Super Attribute Ids Used To Create Configurable Product
        $attributes = array_unique($attributes);
        $associatedProductIds = $allSkuIds; //Product Ids Of Associated Products

        // $attributeModel = $obj->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
        // foreach($attributes as $attributeId) 
        // {
        //     if($attributeModel->load($attributeId))
        //     {}
        //     else
        //     {
        //         $data = array('attribute_id' => $attributeId, 'product_id' => $productId, 'position' => $position);
        //         $position++;
        //         $attributeModel->setData($data)->save();
        //     }           
        // }


        $configurableProduct = $product;

        // $colorAttrId = $configurableProduct->getResource()->getAttribute('color')->getId();
        $configurableProduct->getTypeInstance()->setUsedProductAttributeIds($attributes, $configurableProduct); //attribute ID of attribute 'size_general' in my store
        $configurableAttributesData = $configurableProduct->getTypeInstance()->getConfigurableAttributesAsArray($configurableProduct);
        $configurableProduct->setCanSaveConfigurableAttributes(true);
        $configurableProduct->setConfigurableAttributesData($configurableAttributesData);
        $configurableProduct->save();
        $configurableProductId = $configurableProduct->getId();


        $product->setAffectConfigurableProductAttributes(4);
        $obj->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($attributes, $product);
        $product->setNewVariationsAttributeSetId(4); // Setting Attribute Set Id
        $product->setAssociatedProductIds($associatedProductIds);// Setting Associated Products
        $product->setUsedProductAttributeIds($associatedProductIds);
        $product->setConfigurableProductLinks($associatedProductIds);
        $product->setCanSaveConfigurableAttributes(true);
       // if($product->save())
        if($product->getResource()->save($product))
        {
            echo "<br>configurable product save"; 
            $configurableArray[$productId] = $associatedProductIds;
        }
    }

    //if($productId && ($productType == 'simple'|| $productType == 'configurable' ))
    if($productId)
    {
    	   $resources = \Magento\Framework\App\ObjectManager::getInstance()
			->get('Magento\Framework\App\ResourceConnection');
			$connection= $resources->getConnection();

            $tableName = $resources->getTableName('mgto_marketplace_product'); //gives table name with prefix
            //Select Data from table
            $sql = "Select * FROM " . $tableName. " where mageproduct_id = $productId"; 
            $result = $connection->fetchRow($sql);  
            $mageproduct_id = 0;
            if(isset($result) && is_array($result) && !empty($result))
            {
                $mageproduct_id = $result['mageproduct_id'];
            }          
           // echo '<br>mageproduct_id : '. $mageproduct_id;   
            if(!$mageproduct_id)
            {
    			$created_at = $updated_at = date('Y-m-d H:i:s');
    			$seller_id = 8; //seller_id need to update on live
    			$themeTable = $resources->getTableName('mgto_marketplace_product');
    			$sql = "INSERT INTO " . $themeTable . "(mageproduct_id, adminassign, seller_id,store_id,status,created_at,updated_at,seller_pending_notification,admin_pending_notification,is_approved) VALUES ('".$productId."',0,'".$seller_id."',0,1,'".$created_at."','".$updated_at."',0,0,1)"; 
    			$response = $connection->query($sql);
                $mpLastInsertId = $connection->lastInsertId(); 

              //  echo "mpLastInsertId : ".$mpLastInsertId;
            }
    }

    // $virtualArray = array();
    // if($productId && $productType == 'virtual')
    // {
    //     $array = array();
    //     $array['price'] = $price;
    //     $array['qty'] = $qty; 
    //     $virtualArray[$productId] = $array;
    // }

   // $response = array('response'=>$return, 'configurableArray'=>$configurableArray,'virtualArray'=>$virtualArray);
    $response = array('response'=>$return, 'configurableArray'=>$configurableArray);
    return $response;     
}