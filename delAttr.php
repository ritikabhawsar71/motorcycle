<?php 
namespace app\code\Ves\Brand\Controller\Adminhtml\Brand;


use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';
$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$obj = $bootstrap->getObjectManager();

function createAttributeValue($attributeId,$attributeName,$attributeValue='',$obj)
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
     /*@var \Magento\Eav\Model\Config $attribute */
    $attribute = $eavAttribute->getAttribute('catalog_product', $attributeName);
    $options = $attribute->getSource()->getAllOptions();
    $columns = array_column($options, 'label');
    $search = array_search($attributeValue,$columns);

    //For deleting color oprions
    foreach ($options as $option) {
            $options['delete'][$option['value']] = true; 
            $options['value'][$option['value']] = true;
    //        $return = true;
        }
    $setupObject = $obj->create('Magento\Eav\Setup\EavSetup');
    $addAttributeOption = $setupObject->addAttributeOption($options); //for deleting options
    /*if(!$search)
    {
        $setupObject = $obj->create('Magento\Eav\Setup\EavSetup');
        $addAttributeOption = $setupObject->addAttributeOption($newOptions);   
        // $addAttributeOption = $setupObject->addAttributeOption($options); //for deleting options
        $return = true;
    }
    */
    return true;
    
}
createAttributeValue(93,'color','',$obj);
createAttributeValue(155,'size','',$obj);
//deleteStoreCategories($obj);
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
