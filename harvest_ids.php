<?php

// Get list of all ids in Zendo group using OAI

require_once (dirname(__FILE__) . '/class_oai.php');
require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');

$ids = array();

class MetaOai extends OaiHarvester
{

	function Process()
	{
		global $couch;
		global $ids;
	
		$this->xml = str_replace("\n", '', $this->xml);
		$this->xml = preg_replace('/<OAI-PMH(.*)>/Uu', '<OAI-PMH>', $this->xml);
		
		//echo $this->xml;
		//exit();
		
		$dom = new DOMDocument;
		$dom->loadXML($this->xml);
		$xpath = new DOMXPath($dom);
		
		$xpath->registerNamespace("dc", "http://purl.org/dc/elements/1.1/");	
		$xpath->registerNamespace("oaidc", "http://www.openarchives.org/OAI/2.0/oai_dc/");	
		$xpath->registerNamespace("xsi", "http://www.w3.org/2001/XMLSchema-instance");	
		
		$count = 1;
				
		$identifiers = $xpath->query ('//ListIdentifiers/header/identifier');
		foreach($identifiers as $identifier)
		{
			$oai_id = $identifier->firstChild->nodeValue;
			
			echo $oai_id . "\n";
		}
		
			
		// Give server a break every 10 items
		if (($count++ % 10) == 0)
		{
			$rand = rand(1000000, 3000000);
			usleep($rand);
		}			
		
	}
	
	

}


$zenodo = new MetaOai('http://zenodo.org/oai2d', 'oai_dc', 'user-biosyslit');

$zenodo->harvest();


?>