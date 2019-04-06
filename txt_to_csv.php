<?php
$path = "pub/media/csv/Sullivans_Web_Price_List_New.txt";
if(file_exists($path))
{
    $handle = fopen($path, "r");
    $lines = [];
    if (($handle = fopen($path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 2000, "\t")) !== FALSE) {
            $lines[] = $data;
        }
        fclose($handle);
    }

    $dataArray = $lines;

    //Code for creating CSV for Magento2 products
    $filePath = 'pub/media/csv/Sullivans_Web_Price_List_New.csv';
    $fh1 = @fopen($filePath, 'w');
    foreach ( $dataArray as $data1 ) {
        // Put the data into the stream
        fputcsv($fh1, $data1);
    }
    // Close the file
    fclose($fh1);
    // Make sure nothing else is sent, our file is done
    echo "CSV file have been generated on path :".$filePath;
}
else
{
    echo "File -".$path ."doesn't exists!";
}
exit;