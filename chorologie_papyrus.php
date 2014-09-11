<?php
// declare(encoding='UTF-8');
/**
 * Mon exemple d'application.
 * Fichier contenant les fonctions nécessaire pour l'insertion de l'application dans Papyrus.
 * 
 * @category	PHP 5.2
 * @package		chorologie
 * @author		Mathias CHOUET <mathias@tela-botanica.org>
 * @author		Aurelien PERONNET <aurelien@tela-botanica.org>
 * @copyright	2011 Tela-Botanica
 * @license		http://www.gnu.org/licenses/gpl.html Licence GNU-GPL-v3
 * @license		http://www.cecill.info/licences/Licence_CeCILL_V2-fr.txt Licence CECILL-v2
 * @version		$Id$
 */

// Gestion des paramêtres passés par Papyrus
if (isset($GLOBALS['_GEN_commun']['info_application']->module) && !isset($_GET['module'])) {
		$_GET['module'] = $GLOBALS['_GEN_commun']['info_application']->module;
}

/** Inclusion du fichier principal de l'application*/
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'chorologie.php';

$chemin = '/eflore-test/chorologie/';
$chemin_commun = 'http://www.tela-botanica.org/commun/';
$url_css_bootstrap = Config::get('url_css_bootstrap');
$url_jquery = Config::get('url_jquery');
$css_specifique = Config::get('source_donnees');

// Définition des feuilles de style de l'application pour Papyrus
// TODO: il y a surement mieux à faire pour obtenir l'url des fichiers
GEN_stockerStyleExterne('bootstrap', $url_css_bootstrap);
GEN_stockerStyleExterne('chorologie', $chemin.'/presentations/css/chorologie.css');
GEN_stockerStyleExterne('chorologie_specifique', $chemin.'/presentations/css/'.$css_specifique.'.css');

// Définition des fichiers JS de l'application pour Papyrus
GEN_stockerFichierScript('jquery', $url_jquery);
GEN_stockerFichierScript('chorologie-general', $chemin.'presentations/js'.DS.'chorologie.js');

// +--------------------------------------------------------------------------------------------------+
// Remplacement de méta tags fournit par Papyrus par ceux créés dans l'appli
if (PapControleur::$appControleur->getMetaTitre() != '') {
	$GLOBALS['_PAPYRUS_']['rendu']['TITRE_PAGE'] = PapControleur::$appControleur->getMetaTitre();
}
if (PapControleur::$appControleur->getMetaDescription() != '') {
	GEN_modifierMetaName('description', PapControleur::$appControleur->getMetaDescription());
}
if (PapControleur::$appControleur->getMetaTags() != '') {
	GEN_modifierMetaName('keywords', PapControleur::$appControleur->getMetaTags());
}

// +--------------------------------------------------------------------------------------------------+
// Fonctions d'affichage dans Papyrus
/**
 * Fonction d'affichage de Papyrus, pour le corps de page.
 */
function afficherContenuCorps() {
	// nous avons besoin de la div bsh car le le style bootstrap est limité à sa portée
	return '<div id="bsh">'.PapControleur::$appControleur->getContenuCorps().'</div>';
}

function afficherContenuTete() {
	return PapControleur::$appControleur->getContenuTete();
}

function afficherContenuPied() {
	return PapControleur::$appControleur->getContenuPied();
}

function afficherContenuNavigation() {
	return PapControleur::$appControleur->getContenuNavigation();
}

function afficherContenuMenu() {
	//Retour du menu vide dans le cas de l'affichage dans Papyrus
	// PapControleur::$appControleur->getContenuMenu()
	return '';
}
?>