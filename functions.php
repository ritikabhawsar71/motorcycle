<?php 
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
    $attributeValue = trim(ucwords(strtolower($attributeValue)));

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

function createCategory($categoryName,$objectManager) {

    $catId = getCategoryIdByName($categoryName,$objectManager);
        if(!$catId)
        {
            $category = $objectManager->get('\Magento\Catalog\Model\CategoryFactory')->create();

            $category->setName($categoryName);
            $category->setParentId(2); // 2: Default category.
            $category->setIsActive(true);
            $category->setCustomAttributes([
                    'description' => $categoryName,
            ]);

            if($objectManager->get('\Magento\Catalog\Api\CategoryRepositoryInterface')->save($category))
            {
                $catId = getCategoryIdByName($categoryName,$objectManager);
            }
        }
        return $catId;
}


function getCategoryIdByName($categoryName,$obj)
{
    $categoryFactory = $obj->get('\Magento\Catalog\Model\CategoryFactory'); 
    $collection = $categoryFactory->create()->getCollection()
                  ->addAttributeToFilter('name',$categoryName)->setPageSize(1);
    $categoryId = 0;
    if ($collection->getSize()) 
    {
        $categoryId = $collection->getFirstItem()->getId();
    }
    return $categoryId;
}