<?php

// Fetch some records 

require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');

$ids = array(322659, 439633, 439634, 439635);



$count = 1;
foreach ($ids as $id)
{
	if (!preg_match('/^oai:zenodo.org:/', $id))
	{
		$id = 'oai:zenodo.org:' . $id;
	}


	// fetch JSON
	$url = 'https://zenodo.org/api/records/' .  str_replace('oai:zenodo.org:', '', $id);
	
	$json = get($url);
	echo $json;
	
	$doc = json_decode($json);
	$doc->_id = $id;
	
	print_r($doc);
	//exit();
	
	// store
	$couch->add_update_or_delete_document($doc, $doc->_id, 'add');

	// Give server a break every 10 items
	if (($count++ % 10) == 0)
	{
		$rand = rand(1000000, 3000000);
		echo "\n...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
		usleep($rand);
	}			
}	




?>