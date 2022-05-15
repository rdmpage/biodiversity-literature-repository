<?php

// Read list of "parent" ids, fetch parent and parts (figures) and add to CouchDB

require_once (dirname(__FILE__) . '/class_oai.php');
require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');


$filename = 'parent_ids.txt';
$file_handle = fopen($filename, "r");

$count = 1;

$replicate = array();

// Initialise stack with list of parent ids
$stack = array();

while (!feof($file_handle)) 
{
   $id = trim(fgets($file_handle));

	$stack[] = $id;
}


// Process stack, adding any part ids to our list of things to harvest
while (count($stack) > 0)
{
	$id = array_pop($stack);


	// fetch JSON
	$url = 'https://zenodo.org/api/records/' .  str_replace('oai:zenodo.org:', '', $id);

	$json = get($url);
	//echo $json;
	
	if ($json != '')
	{

		$doc = json_decode($json);

		//print_r($doc);
		
		// parts
		if (isset($doc->metadata->related_identifiers))
		{
			foreach ($doc->metadata->related_identifiers as $related)
			{
				switch ($related->relation)
				{
					// [identifier] => http://zenodo.org/record/252172
					case 'hasPart':
						if (preg_match('/http:\/\/zenodo.org\/record\/(?<id>\d+)/', $related->identifier, $m))
						{
							echo "Add to stack " . $m['id'] . "\n";
							$stack[] = 'oai:zenodo.org:' . $m['id'];
						}
						break;
				
					default:
						break;
				}
			}
		}
		
		$doc->_id = $id;

		
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
	if (count($replicate) >= 10)
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

if (count($replicate) > 0)
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