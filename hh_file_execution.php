<?php
$myfile = fopen("hh_file_execution_output.txt", "w") or die("Unable to open file!");
for($i=1;$i<=139;$i++)
{	
	// $xmlData = "Executing file : m2_hh_split$i.csv";
	// fwrite($myfile, $xmlData);
	$xmlData = file_get_contents("http://motorcyclewholesale.com/motorcsvimport/helmethouse_csv_import.php?file=m2_hh_split$i.csv");
	fwrite($myfile, $xmlData);
	echo "<pre>";
	print_r($xmlData);
}
fclose($myfile);
?>