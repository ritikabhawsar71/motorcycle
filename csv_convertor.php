<?php
/* Purpose : Script for converting helmet house CSV into Magento2 compatible product CSV.
   Author : Ritika Bhawsar
   Email : ritika.infowind@gmail.com
   Copyright : Infowind Technologies
   DateTime : 19.Dec.2018 10.36 AM 
*/
namespace app\code\Ves\Brand\Controller\Adminhtml\Brand;
error_reporting(1);
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$obj = $bootstrap->getObjectManager();

/*  Purpose : Method for Creating attribute Size and Color Values
    Params : $attributeId It stores id of attributeCode 
             $attributeName It stores name of attributeCode like 'size', 'color'   
             $attributeValue It stores value of attribute 
             $obj it is object of object manager class
    Return : bool It returns true/false
    DateTime : 22.Dec.2018 01.00 PM
*/
function createAttributeValue($attributeId,$attributeName,$attributeValue,$obj)
{
    $return = false;
    //attributeId 155 for size , 93 for color    
    $newOptions = [
            'values' => [
            '0' => $attributeValue,
        ],
        'attribute_id' => $attributeId,
    ];

    $eavAttribute = $obj->create('Magento\Eav\Model\Config');
    /** @var \Magento\Eav\Model\Config $attribute */
    $attribute = $eavAttribute->getAttribute('catalog_product', $attributeName);
    $options = $attribute->getSource()->getAllOptions();
    $columns = array_column($options, 'label');
    $search = array_search($attributeValue,$columns);

    //For deleting color oprions
    // foreach ($options as $option) {
    //         $options['delete'][$option['value']] = true; 
    //         $options['value'][$option['value']] = true;
    //     }

    if(!$search)
    {
        $setupObject = $obj->create('Magento\Eav\Setup\EavSetup');
        $addAttributeOption = $setupObject->addAttributeOption($newOptions);   
        // $addAttributeOption = $setupObject->addAttributeOption($options); //for deleting options
        $return = true;
    }

    return $return;
}


function getImage($image)
{
    if(!empty($image))
    {
        $imagePathRoot =  $_SERVER['DOCUMENT_ROOT'].'/pub/media/catalog/product/'.$image;
        if(!file_exists($imagePathRoot))
        {
            $image = 'http://img.helmethouse.com/'.$image;
        }
    }
    return $image;
}


/*  Purpose : Method for getting country name by country code
    Params : $code It stores value of country code 
    Return : string It returns name of country
    DateTime : 20.Dec.2018 4.00 PM
*/
function countryCodeToCountry($code) {
    $code = strtoupper($code);
    if ($code == 'AF') return 'Afghanistan';
    if ($code == 'AX') return 'Åland Islands';
    if ($code == 'AL') return 'Albania';
    if ($code == 'DZ') return 'Algeria';
    if ($code == 'AS') return 'American Samoa';
    if ($code == 'AD') return 'Andorra';
    if ($code == 'AO') return 'Angola';
    if ($code == 'AI') return 'Anguilla';
    if ($code == 'AQ') return 'Antarctica';
    if ($code == 'AG') return 'Antigua and Barbuda';
    if ($code == 'AR') return 'Argentina';
    if ($code == 'AM') return 'Armenia';
    if ($code == 'AW') return 'Aruba';
    if ($code == 'AU') return 'Australia';
    if ($code == 'AT') return 'Austria';
    if ($code == 'AZ') return 'Azerbaijan';
    if ($code == 'BS') return 'Bahamas';
    if ($code == 'BH') return 'Bahrain';
    if ($code == 'BD') return 'Bangladesh';
    if ($code == 'BB') return 'Barbados';
    if ($code == 'BY') return 'Belarus';
    if ($code == 'BE') return 'Belgium';
    if ($code == 'BZ') return 'Belize';
    if ($code == 'BJ') return 'Benin';
    if ($code == 'BM') return 'Bermuda';
    if ($code == 'BT') return 'Bhutan';
    if ($code == 'BO') return 'Bolivia';
    if ($code == 'BA') return 'Bosnia and Herzegovina';
    if ($code == 'BW') return 'Botswana';
    if ($code == 'BV') return 'Bouvet Island';
    if ($code == 'BR') return 'Brazil';
    if ($code == 'IO') return 'British Indian Ocean Territory';
    if ($code == 'VG') return 'British Virgin Islands';
    if ($code == 'BN') return 'Brunei';
    if ($code == 'BG') return 'Bulgaria';
    if ($code == 'BF') return 'Burkina Faso';
    if ($code == 'BI') return 'Burundi';
    if ($code == 'KH') return 'Cambodia';
    if ($code == 'CM') return 'Cameroon';
    if ($code == 'CA') return 'Canada';
    if ($code == 'CV') return 'Cape Verde';
    if ($code == 'KY') return 'Cayman Islands';
    if ($code == 'CF') return 'Central African Republic';
    if ($code == 'TD') return 'Chad';
    if ($code == 'CL') return 'Chile';
    if ($code == 'CN') return 'China';
    if ($code == 'CX') return 'Christmas Island';
    if ($code == 'CC') return 'Cocos [Keeling] Islands';
    if ($code == 'CO') return 'Colombia';
    if ($code == 'KM') return 'Comoros';
    if ($code == 'CG') return 'Congo - Brazzaville';
    if ($code == 'CD') return 'Congo - Kinshasa';
    if ($code == 'CK') return 'Cook Islands';
    if ($code == 'CR') return 'Costa Rica';
    if ($code == 'CI') return 'Côte d’Ivoire';
    if ($code == 'HR') return 'Croatia';
    if ($code == 'CU') return 'Cuba';
    if ($code == 'CY') return 'Cyprus';
    if ($code == 'CZ') return 'Czech Republic';
    if ($code == 'DK') return 'Denmark';
    if ($code == 'DJ') return 'Djibouti';
    if ($code == 'DM') return 'Dominica';
    if ($code == 'DO') return 'Dominican Republic';
    if ($code == 'EC') return 'Ecuador';
    if ($code == 'EG') return 'Egypt';
    if ($code == 'SV') return 'El Salvador';
    if ($code == 'GQ') return 'Equatorial Guinea';
    if ($code == 'ER') return 'Eritrea';
    if ($code == 'EE') return 'Estonia';
    if ($code == 'ET') return 'Ethiopia';
    if ($code == 'FK') return 'Falkland Islands';
    if ($code == 'FO') return 'Faroe Islands';
    if ($code == 'FJ') return 'Fiji';
    if ($code == 'FI') return 'Finland';
    if ($code == 'FR') return 'France';
    if ($code == 'GF') return 'French Guiana';
    if ($code == 'PF') return 'French Polynesia';
    if ($code == 'TF') return 'French Southern Territories';
    if ($code == 'GA') return 'Gabon';
    if ($code == 'GM') return 'Gambia';
    if ($code == 'GE') return 'Georgia';
    if ($code == 'DE') return 'Germany';
    if ($code == 'GH') return 'Ghana';
    if ($code == 'GI') return 'Gibraltar';
    if ($code == 'GR') return 'Greece';
    if ($code == 'GL') return 'Greenland';
    if ($code == 'GD') return 'Grenada';
    if ($code == 'GP') return 'Guadeloupe';
    if ($code == 'GU') return 'Guam';
    if ($code == 'GT') return 'Guatemala';
    if ($code == 'GG') return 'Guernsey';
    if ($code == 'GN') return 'Guinea';
    if ($code == 'GW') return 'Guinea-Bissau';
    if ($code == 'GY') return 'Guyana';
    if ($code == 'HT') return 'Haiti';
    if ($code == 'HM') return 'Heard Island and McDonald Islands';
    if ($code == 'HN') return 'Honduras';
    if ($code == 'HK') return 'Hong Kong SAR China';
    if ($code == 'HU') return 'Hungary';
    if ($code == 'IS') return 'Iceland';
    if ($code == 'IN') return 'India';
    if ($code == 'ID') return 'Indonesia';
    if ($code == 'IR') return 'Iran';
    if ($code == 'IQ') return 'Iraq';
    if ($code == 'IE') return 'Ireland';
    if ($code == 'IM') return 'Isle of Man';
    if ($code == 'IL') return 'Israel';
    if ($code == 'IT') return 'Italy';
    if ($code == 'JM') return 'Jamaica';
    if ($code == 'JP') return 'Japan';
    if ($code == 'JE') return 'Jersey';
    if ($code == 'JO') return 'Jordan';
    if ($code == 'KZ') return 'Kazakhstan';
    if ($code == 'KE') return 'Kenya';
    if ($code == 'KI') return 'Kiribati';
    if ($code == 'KW') return 'Kuwait';
    if ($code == 'KG') return 'Kyrgyzstan';
    if ($code == 'LA') return 'Laos';
    if ($code == 'LV') return 'Latvia';
    if ($code == 'LB') return 'Lebanon';
    if ($code == 'LS') return 'Lesotho';
    if ($code == 'LR') return 'Liberia';
    if ($code == 'LY') return 'Libya';
    if ($code == 'LI') return 'Liechtenstein';
    if ($code == 'LT') return 'Lithuania';
    if ($code == 'LU') return 'Luxembourg';
    if ($code == 'MO') return 'Macau SAR China';
    if ($code == 'MK') return 'Macedonia';
    if ($code == 'MG') return 'Madagascar';
    if ($code == 'MW') return 'Malawi';
    if ($code == 'MY') return 'Malaysia';
    if ($code == 'MV') return 'Maldives';
    if ($code == 'ML') return 'Mali';
    if ($code == 'MT') return 'Malta';
    if ($code == 'MH') return 'Marshall Islands';
    if ($code == 'MQ') return 'Martinique';
    if ($code == 'MR') return 'Mauritania';
    if ($code == 'MU') return 'Mauritius';
    if ($code == 'YT') return 'Mayotte';
    if ($code == 'MX') return 'Mexico';
    if ($code == 'FM') return 'Micronesia';
    if ($code == 'MD') return 'Moldova';
    if ($code == 'MC') return 'Monaco';
    if ($code == 'MN') return 'Mongolia';
    if ($code == 'ME') return 'Montenegro';
    if ($code == 'MS') return 'Montserrat';
    if ($code == 'MA') return 'Morocco';
    if ($code == 'MZ') return 'Mozambique';
    if ($code == 'MM') return 'Myanmar [Burma]';
    if ($code == 'NA') return 'Namibia';
    if ($code == 'NR') return 'Nauru';
    if ($code == 'NP') return 'Nepal';
    if ($code == 'NL') return 'Netherlands';
    if ($code == 'AN') return 'Netherlands Antilles';
    if ($code == 'NC') return 'New Caledonia';
    if ($code == 'NZ') return 'New Zealand';
    if ($code == 'NI') return 'Nicaragua';
    if ($code == 'NE') return 'Niger';
    if ($code == 'NG') return 'Nigeria';
    if ($code == 'NU') return 'Niue';
    if ($code == 'NF') return 'Norfolk Island';
    if ($code == 'MP') return 'Northern Mariana Islands';
    if ($code == 'KP') return 'North Korea';
    if ($code == 'NO') return 'Norway';
    if ($code == 'OM') return 'Oman';
    if ($code == 'PK') return 'Pakistan';
    if ($code == 'PW') return 'Palau';
    if ($code == 'PS') return 'Palestinian Territories';
    if ($code == 'PA') return 'Panama';
    if ($code == 'PG') return 'Papua New Guinea';
    if ($code == 'PY') return 'Paraguay';
    if ($code == 'PE') return 'Peru';
    if ($code == 'PH') return 'Philippines';
    if ($code == 'PN') return 'Pitcairn Islands';
    if ($code == 'PL') return 'Poland';
    if ($code == 'PT') return 'Portugal';
    if ($code == 'PR') return 'Puerto Rico'; //missing in country of manufaturer list 
    if ($code == 'QA') return 'Qatar';
    if ($code == 'RE') return 'Réunion';
    if ($code == 'RO') return 'Romania';
    if ($code == 'RU') return 'Russia';
    if ($code == 'RW') return 'Rwanda';
    if ($code == 'BL') return 'Saint Barthélemy';
    if ($code == 'SH') return 'Saint Helena';
    if ($code == 'KN') return 'Saint Kitts and Nevis';
    if ($code == 'LC') return 'Saint Lucia';
    if ($code == 'MF') return 'Saint Martin';
    if ($code == 'PM') return 'Saint Pierre and Miquelon';
    if ($code == 'VC') return 'Saint Vincent and the Grenadines';
    if ($code == 'WS') return 'Samoa';
    if ($code == 'SM') return 'San Marino';
    if ($code == 'ST') return 'São Tomé and Príncipe';
    if ($code == 'SA') return 'Saudi Arabia';
    if ($code == 'SN') return 'Senegal';
    if ($code == 'RS') return 'Serbia';
    if ($code == 'SC') return 'Seychelles';
    if ($code == 'SL') return 'Sierra Leone';
    if ($code == 'SG') return 'Singapore';
    if ($code == 'SK') return 'Slovakia';
    if ($code == 'SI') return 'Slovenia';
    if ($code == 'SB') return 'Solomon Islands';
    if ($code == 'SO') return 'Somalia';
    if ($code == 'ZA') return 'South Africa';
    if ($code == 'GS') return 'South Georgia and the South Sandwich Islands';
    if ($code == 'KR') return 'South Korea';
    if ($code == 'ES') return 'Spain';
    if ($code == 'LK') return 'Sri Lanka';
    if ($code == 'SD') return 'Sudan';
    if ($code == 'SR') return 'Suriname';
    if ($code == 'SJ') return 'Svalbard and Jan Mayen';
    if ($code == 'SZ') return 'Swaziland';
    if ($code == 'SE') return 'Sweden';
    if ($code == 'CH') return 'Switzerland';
    if ($code == 'SY') return 'Syria';
    if ($code == 'TW') return 'Taiwan';
    if ($code == 'TJ') return 'Tajikistan';
    if ($code == 'TZ') return 'Tanzania';
    if ($code == 'TH') return 'Thailand';
    if ($code == 'TL') return 'Timor-Leste';
    if ($code == 'TG') return 'Togo';
    if ($code == 'TK') return 'Tokelau';
    if ($code == 'TO') return 'Tonga';
    if ($code == 'TT') return 'Trinidad and Tobago';
    if ($code == 'TN') return 'Tunisia';
    if ($code == 'TR') return 'Turkey';
    if ($code == 'TM') return 'Turkmenistan';
    if ($code == 'TC') return 'Turks and Caicos Islands';
    if ($code == 'TV') return 'Tuvalu';
    if ($code == 'UG') return 'Uganda';
    if ($code == 'UA') return 'Ukraine';
    if ($code == 'AE') return 'United Arab Emirates';
    if ($code == 'GB') return 'United Kingdom';
    if ($code == 'US') return 'United States';
    if ($code == 'UY') return 'Uruguay'; 
    if ($code == 'UM') return 'U.S. Outlying Islands';
    if ($code == 'VI') return 'U.S. Virgin Islands';
    if ($code == 'UZ') return 'Uzbekistan';
    if ($code == 'VU') return 'Vanuatu';
    if ($code == 'VA') return 'Vatican City';
    if ($code == 'VE') return 'Venezuela';
    if ($code == 'VN') return 'Vietnam';
    if ($code == 'WF') return 'Wallis and Futuna';
    if ($code == 'EH') return 'Western Sahara';
    if ($code == 'YE') return 'Yemen';
    if ($code == 'XK') return 'Kosovo'; //missing in country of manufaturer list
    if ($code == 'ZM') return 'Zambia';
    if ($code == 'ZW') return 'Zimbabwe';
    return '';
}  


/*  Purpose : Method for creating brands
    Params : $name It stores brand name
            $obj It stores object of ObjectManager Class    
    Return : Boolean True/False
    DateTime : 20.Dec.2018 3.00 PM
*/
function createBrand($name,$obj)
{
    $brandData = array();
    $brandData['form_key'] = 'kgjRZptqEw69co8q';
    $brandData['name'] = $name;
    $brandData['url_key'] = $brandData['name'];
    $url_key = $obj->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($brandData['url_key']);
    $brandData['url_key'] = $url_key;
    $brandData['group_id'] = 1;
    $brandData['description'] = $brandData['name'];
    $brandData['image'] = 'ves/brand/client-logo-02.png';
    $brandData['thumbnail'] = 'ves/brand/client-logo-02.png';
    $brandData['stores'][0] = 0;
    $brandData['position'] = '';
    $brandData['status'] = 1;
    $brandData['limit'] = 20;
    $brandData['page'] = 1;
    $brandData['in_products'] = '';
    $brandData['entity_id'] = '';
    $brandData['product_name'] = '';
    $brandData['product_type'] = '';
    $brandData['set_name'] = '';
    $brandData['product_status'] = '';
    $brandData['product_visibility'] = '';
    $brandData['product_sku'] = '';
    $brandData['product_price']['from'] = '';
    $brandData['product_price']['to'] = '';
    $brandData['product_position']['from'] = '';
    $brandData['product_position']['to'] = '';
    $brandData['links']['related'] = '';
    $brandData['page_layout'] = 'empty';
    $brandData['layout_update_xml'] = '';
    $brandData['page_title'] = '';
    $brandData['meta_keywords'] = '';
    $brandData['meta_description'] = '';

    $model = $obj->create('Ves\Brand\Model\Brand');
    $model->setData($brandData);
    try
    {
        $model->save();
        $return = true;
    }
    catch (\Magento\Framework\Exception\LocalizedException $e) 
    {
       //echo 'catch1'.$e->getMessage();
       $return = false;
    } catch (\RuntimeException $e) 
    {
       //echo 'catch2'.$e->getMessage();
       $return = false;
    } catch (\Exception $e) 
    {
       //echo 'Something went wrong while saving the brand.';
       $return = false;
    }
    return $return;        
}

//Code for reading helmet house master.csv and converting it into associative array
$array = $fields = array(); $i = 0;
$handle = @fopen("pub/media/csv/master.csv", "r");
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


//Creating configurableproducts array
function unique_multidim_array($array, $key, $arrayCount) 
{ 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array();  
    foreach($array as $val) 
    { 
        if ($arrayCount[$val[$key]] > 1) 
        {
            $key_array[$i] = $val[$key]; 
            $temp_array[$val[$key]][$i] = $val; 
        }
        $i++; 
    } 
    return $temp_array; 
}


function array_addstuff($a, $i) {
    foreach ($a as &$e)
        $e = $i . $e;
    return $a;
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
                $uniqueImagesArray[] = getImage($uniqueImage);
            }
        }
        $additionalImages = implode(',', $uniqueImagesArray);

        $labels = array_column($details[$k], 'Alt Photos');
        $uniqueLabels = array_unique($labels);
        
        $image = (isset($uniqueImages[0]) && !empty($uniqueImages[0]) && $uniqueImages[0] != ' ')?$uniqueImages[0]:'';
        $label = (isset($uniqueLabels[0]) && !empty($uniqueLabels[0]) && $uniqueLabels[0] != ' ')?$uniqueLabels[0]:'';
        $image = getImage($image);
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
        	if(isset($detail['Alt Part#']) && !empty($detail['Alt Part#']) && $detail['Alt Part#'] != ' ')
        	{
        		$attributeDetails = 'sku='.$detail['Alt Part#'];
        		$associatedProductSku[] = $detail['Alt Part#'];
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
    if(isset($value['Alt Part#']) && !empty($value['Alt Part#']) && $value['Alt Part#'] != ' ')
    {
        $sku  .= '-'.$value['Alt Part#'];
    }
    // if(isset($value['Color']) && !empty($value['Color']) && $value['Color'] != ' ')
    // {
    //     $sku .= '-'.$value['Color'];
    // }
    // if(isset($value['Size']) && !empty($value['Size']) && $value['Size'] != ' ')
    // {
    //     $sku .= '-'.$value['Size'];
    // }

    $data[$key]['sku'] = (isset($value['Alt Part#']) && !empty($value['Alt Part#']) && $value['Alt Part#']!=' ')?$value['Alt Part#']:'';
    $data[$key]['store_view_code'] = '';
    $data[$key]['attribute_set_code'] = 'Default';

    if(in_array($value['Alt Part#'], $associatedProductSku))
    {
    	$data[$key]['product_type'] = 'virtual';
    }
    else
    {
    	$data[$key]['product_type'] = 'simple';
    }

    $data[$key]['categories'] = (isset($value['Category']) && !empty($value['Category']) && $value['Category']!=' ')?'Default Category/'.$value['Category']:'';

    $data[$key]['product_websites'] = 'base';

    $data[$key]['name'] = $sku;  

    $data[$key]['description'] = (isset($value['Long Description']) && !empty($value['Long Description']) && $value['Long Description'] != ' ')?$value['Long Description']:'';

    $data[$key]['short_description'] = (isset($value['Description']) && !empty($value['Description']) && $value['Description'] != ' ')?$value['Description']:'';

    $data[$key]['weight'] = (isset($value['Weight']) && !empty($value['Weight']) && $value['Weight'] != ' ')?$value['Weight']:'';

    $data[$key]['product_online'] = 1;
    $data[$key]['tax_class_name'] = 'Taxable Goods';


    if(in_array($value['Alt Part#'], $associatedProductSku))
    {
    	$data[$key]['visibility'] = 'Not Visible Individually';
    }
    else
    {
    	$data[$key]['visibility'] = 'Catalog, Search';
    }


    $price =  (isset($value['MAPP Price']) && !empty($value['MAPP Price']) && $value['MAPP Price'] != ' ')?$value['MAPP Price']:0;
    if(empty($price))
    {
    	$price =  (isset($value['Retail']) && !empty($value['Retail']) && $value['Retail'] != ' ')?$value['Retail']:0;
    }
    if(empty($price))
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
    $photo = getImage($photo);
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

    if(in_array($value['Alt Part#'], $associatedProductSku))
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
$fileName_1 = 'magento2_products.csv';
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$fileName_1}");
header("Expires: 0");
header("Pragma: public");
$fh1 = @fopen( 'php://output', 'w' );
$headerDisplayed1 = false;
foreach ( $finalData as $data1 ) {
    // Add a header row if it hasn't been added yet
    if ( !$headerDisplayed1 ) {
        // Use the keys from $data as the titles
        fputcsv($fh1, array_keys($data1));
        $headerDisplayed1 = true;
    }
    // Put the data into the stream
    fputcsv($fh1, $data1);
}
// Close the file
fclose($fh1);
// Make sure nothing else is sent, our file is done
exit;