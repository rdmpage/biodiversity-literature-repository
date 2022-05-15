<?php

// Read list of ids and add to CouchDB

require_once (dirname(__FILE__) . '/class_oai.php');
require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');


$filename = 'ids.txt';
$file_handle = fopen($filename, "r");

$count = 1;

$replicate = array();

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
			
			$replicate[] = $doc->_id;
		}

		// Give server a break every 10 items
		if (($count++ % 10) == 0)
		{
			$rand = rand(1000000, 3000000);
			echo "\n...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
			usleep($rand);
		}
		
		// replicate to cloud
		//if (count($replicate) >= 10)
		if (0)
		{
			$doc = new stdclass;

			$doc->source = "zenodo";
			$doc->target = "https://4c577ff8-0f3d-4292-9624-41c1693c433b-bluemix:6727bfccd5ac5213a9a05f87e5161c153131af6b2c0f3355fe1aa0fe2f97a35f@4c577ff8-0f3d-4292-9624-41c1693c433b-bluemix.cloudant.com/zenodo";
			$doc->doc_ids = $replicate;

			print_r($doc);


			$command = "curl http://localhost:5984/_replicate -H 'Content-Type: application/json' -d '" . json_encode($doc) . "'";

			echo $command . "\n";
			system($command);
			
			$replicate = array();

		}				
		
	}			
}	

//if (count($replicate) > 0)
if (0)
{
	$doc = new stdclass;

	$doc->source = "zenodo";
	$doc->target = "https://4c577ff8-0f3d-4292-9624-41c1693c433b-bluemix:6727bfccd5ac5213a9a05f87e5161c153131af6b2c0f3355fe1aa0fe2f97a35f@4c577ff8-0f3d-4292-9624-41c1693c433b-bluemix.cloudant.com/zenodo";
	$doc->doc_ids = $replicate;

	print_r($doc);


	$command = "curl http://localhost:5984/_replicate -H 'Content-Type: application/json' -d '" . json_encode($doc) . "'";

	echo $command . "\n";
	system($command);
	
	$replicate = array();

}				





?>