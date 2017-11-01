<?php

// include settings from cmd argument
include($argv[1]);

// include phpbahn class
include(SETTING_PHPBAHN_CLASS);

//Vorbereitung des Abrufs
$bahn = new phpbahn(SETTING_APIKEY);
$bhf = $bahn->getStation(SETTING_BAHNHOF) ;
reset($bhf);
$ibnr = key($bhf);
$bhf = array_shift($bhf);

$zuege = $bahn->getTimetable($ibnr, time() );

if (count($zuege)){

	foreach ($zuege as $zug) {

		$zugname = $zug['zug']['klasse'].$zug['zug']['nummer'];
		
		if (in_array($zugname, SETTING_LINES) AND isset($zug['ankunft']) ){
			
			$zeitGeplant = $zug['ankunft']['zeitGeplant'];

			if(!isset($zug['ankunft']['zeitAktuell'])){
				$verspaetung = 0;
				$zeitAktuell = $zeitGeplant;
			} else {
				$zeitAktuell = $zug['ankunft']['zeitAktuell'];
				$verspaetung = $bahn->dateToTimestamp($zeitAktuell)-$bahn->dateToTimestamp($zeitGeplant);
			}
			
			// add result to output file
			file_put_contents(SETTING_OUTPUT_FILE, date("Ymd").",".time().",".$zugname.",".$zeitAktuell.",".$zeitGeplant.",".$verspaetung."\n", FILE_APPEND);

		}
	}
}
?>