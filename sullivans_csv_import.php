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

//Code for reading helmet house master.csv and converting it into associative array
$array = $fields = array(); $i = 0;
$fileName = 'm2_ss.csv';
// if(isset($_GET['file']) && !empty($_GET['file']))
// {
//     $fileName = $_GET['file'];
// }
$app_state = $obj->get('\Magento\Framework\App\State');  
$app_state->setAreaCode('frontend');

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

$missingRecords = array();
foreach ($array as $fdata) 
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
                $seller_id = 9;
                // echo '<br>owner_id  : '.$owner_id ;
                if(isset($owner_id) && !empty($owner_id) && $owner_id != $seller_id)
                {
                     //seller_id need to update on live
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
                                            $imagePathRoot =  $_SERVER['DOCUMENT_ROOT'].'/motorcsvimport/pub/media/marketplace/assignproduct/product/'.$img;

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

                                        echo '<br>associatedProductId : '.$associatedProductId;

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
        $catId = getCategoryIdByName($catName[1],$obj);
        if(!isset($catId) || empty($catId) || $catId = ' ')
        {
            $catId = createCategory($catName[1],$obj);
        }
        $categoryIdsArray[] = $catId;
    }    

    $product->setCategoryIds($categoryIdsArray);  //need to check

    $name = (isset($array['name']) && !empty($array['name']) && $array['name'] != ' ')?trim($array['name']):'';  
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
            $imagePathRoot =  $_SERVER['DOCUMENT_ROOT'].'/motorcsvimport/pub/media/catalog/product/'.$baseImage;

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
                        $prdbasepath  ='/home/motorcyclewholes/public_html/motorcsvimport/pub/media/'; //change path URL on live
                        echo '<br>Add Images :' . $prdbasepath.basename(trim($additionalImagesArray[$i])) . PHP_EOL;
                        $image_directory = $prdbasepath.'data'.'/'.basename(trim($additionalImagesArray[$i]));
                        echo '<br>image_directory : '.$image_directory; 
                        if (file_exists($image_directory) && getimagesize($image_directory)) {

                            echo '<br>File exists'.PHP_EOL; 
                            $product->addImageToMediaGallery($image_directory, array('image', 'small_image', 'thumbnail'), false, false);
                            $product->save();
                        }
                    }

                }
            }

    }

    if($product->save())
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
        if($product->save())
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
                $seller_id = 9; //seller_id need to update on live
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