<?php

// Read list of ids and add to CouchDB

require_once (dirname(__FILE__) . '/class_oai.php');
require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');


$filename = 'ids.txt';
$filename = 'ids-subset.txt';
//$filename = 'test.txt';
$file_handle = fopen($filename, "r");

$count = 1;

while (!feof($file_handle)) 
{
   $id = trim(fgets($file_handle));
      
   $go = true;
   
   // do we have this already?
   if ($couch->exists($id))
   {
   		echo "$id exists\n";
   		$go = false;
   }
   
   if ($go)
   {

		// fetch JSON
		$url = 'https://zenodo.org/api/records/' .  str_replace('oai:zenodo.org:', '', $id);
	
		$json = get($url);
		//echo $json;
		
		if ($json != '')
		{
	
			$doc = json_decode($json);
			$doc->_id = $id;
	
			print_r($doc);
			//exit();
	
			// store
			$couch->add_update_or_delete_document($doc, $doc->_id, 'add');
		}

		// Give server a break every 10 items
		if (($count++ % 10) == 0)
		{
			$rand = rand(1000000, 3000000);
			echo "\n...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
			usleep($rand);
		}
	}			
}	




?>