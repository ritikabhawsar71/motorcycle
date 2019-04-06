<?php
$inputFile = 'pub/media/csv/m2_ss.csv';
$outputFile = 'pub/media/csv/ss_split/m2_ss_split';

$splitSize = 200;
$in = fopen($inputFile, 'r');

$rowCount = 0;
$fileCount = 1;
$newfile = 0;
while (!feof($in)) {
    if (($rowCount % $splitSize) == 0) {
        if ($rowCount > 0) {
            fclose($out);
        }
        echo '<br>Created file '.$outputFile . $fileCount . '.csv';
        if($out = fopen($outputFile . $fileCount++ . '.csv', 'w'))
        {
        	$newfile = 1;
        }        
    }
    $data = fgetcsv($in);

    if($rowCount == 0)
    	$header = $data;

    if($newfile == 1)
    {
    	fputcsv($out, $header);   
    }
    $newfile = 0;
    if($data && $rowCount != 0)
    {  	
        fputcsv($out, $data);   
    }
    
    $rowCount++;
}

fclose($out);