<?php

// Autoload PSR-4
spl_autoload_register();

// Imports 
use \Classes\Webforce3\Config\Config;
use \Classes\Webforce3\DB\Session;
use \Classes\Webforce3\DB\Location;
use \Classes\Webforce3\DB\Training;
use \Classes\Webforce3\Helpers\SelectHelper;


// Get the config object
$conf = Config::getInstance();

$sessId = isset($_GET['ses_id']) ? intval($_GET['ses_id']) : 0;
$sessionObject = new Session();
$trainingObject = new Training();
$locationObject = new Location();

// Récupère la liste complète des sessions en DB
$sessionList = Session::getAllForSelect();

// Récupère la liste complète des Locations en DB
$locationList = Location::getAllForSelect();

// Récupère la liste complète des Traning en DB
$trainingList = Training::getAllForSelect();

// Si modification d'une ville, on charge les données pour le formulaire
if ($sessId > 0) {
	$sessionObject = Session::get($sessId);
        //print_r($sessionObject);
}

// Si lien suppression
if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
	if (City::deleteById(intval($_GET['delete']))) {
		header('Location: session.php?success='.urlencode('Suppression effectuée'));
		exit;
	}
}

// Formulaire soumis
if(!empty($_POST)) {
    //print_r($_POST);
}

$selectSessions = new SelectHelper($sessionList, $sessionObject->getId(), array(
	'name' => 'ses_id',
	'id' => 'ses_id',
	'class' => 'form-control',
));

$selectLocations = new SelectHelper($locationList, $locationObject->getId(), array(
	'name' => 'loc_id',
	'id' => 'loc_id',
	'class' => 'form-control',
));

$selectTrainings = new SelectHelper($trainingList, $trainingObject->getId(), array(
	'name' => 'loc_id',
	'id' => 'loc_id',
	'class' => 'form-control',
));


// Views - toutes les variables seront automatiquement disponibles dans les vues
require $conf->getViewsDir().'header.php';
require $conf->getViewsDir().'session.php';
require $conf->getViewsDir().'footer.php';