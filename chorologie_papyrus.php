<?php
// declare(encoding='UTF-8');
/**
 * Mon exemple d'application.
 * Fichier contenant les fonctions nécessaire pour l'insertion de l'application dans Papyrus.
 * 
 * @category	PHP 5.2
 * @package		eflore-consultation
 * @author		Jean-Pascal MILCENT <jpm@tela-botanica.org>
 * @author		Delphine CAUQUIL <delphine@tela-botanica.org>
 * @copyright	2011 Tela-Botanica
 * @license		http://www.gnu.org/licenses/gpl.html Licence GNU-GPL-v3
 * @license		http://www.cecill.info/licences/Licence_CeCILL_V2-fr.txt Licence CECILL-v2
 * @version		$Id$
 */

// Gestion des paramêtres passés par Papyrus
if (isset($GLOBALS['_GEN_commun']['info_application']->referentiel)) {
	$_GET['referentiel'] = $GLOBALS['_GEN_commun']['info_application']->referentiel;
}

if (isset($GLOBALS['_GEN_commun']['info_application']->module) && !isset($_GET['module'])) {
		$_GET['module'] = $GLOBALS['_GEN_commun']['info_application']->module;
}

/** Inclusion du fichier principal de l'application*/
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'chorologie.php';

$chemin = '/chorologie/';
$chemin_commun = 'http://www.tela-botanica.org/commun/';
// Définition des feuilles de style de l'application pour Papyrus
// TODO: il y a surement mieux à faire pour obtenir l'url des fichiers
//GEN_stockerStyleExterne('jquery-ui', $chemin_commun.'jquery/jquery-ui/1.10.2/css/ui-lightness/jquery-ui-1.10.2.custom.css');
//GEN_stockerStyleExterne('fancybox', $chemin_commun.'jquery/fancybox/1.3.4/jquery.fancybox-1.3.4.css');
GEN_stockerStyleExterne('eflore', $chemin.'/presentations/css/chorologie.css');

// Définition des fichiers JS de l'application pour Papyrus
//GEN_stockerFichierScript('jquery-1.7', $chemin_commun.'jquery/1.7.1'.DS.'jquery-1.7.1.js');
//GEN_stockerFichierScript('jquery-ui-1.10.2', $chemin_commun.'jquery/jquery-ui/1.10.2/js'.DS.'jquery-ui-1.10.2.custom.min.js');
//GEN_stockerFichierScript('fancybox', $chemin_commun.'/jquery/fancybox/1.3.4'.DS.'jquery.fancybox-1.3.4.pack.js');
//GEN_stockerFichierScript('cookie', $chemin_commun.'jquery/cookie/1.0/jquery.cookie.min.js');
GEN_stockerFichierScript('chorologie-general', $chemin.'presentations/js'.DS.'chorologie.js');
//GEN_stockerFichierScript('autocompletion', $chemin.'presentations/scripts'.DS.'recherche.js');
//GEN_stockerFichierScript('eflore-synthese', $chemin.'presentations/scripts'.DS.'fiche-synthese.js');

// +--------------------------------------------------------------------------------------------------+
// Remplacement de méta tags fournit par Papyrus par ceux créés dans l'appli
if ($appControleur->getMetaTitre() != '') {
	$GLOBALS['_PAPYRUS_']['rendu']['TITRE_PAGE'] = $appControleur->getMetaTitre();
}
if ($appControleur->getMetaDescription() != '') {
	GEN_modifierMetaName('description', $appControleur->getMetaDescription());
}
if ($appControleur->getMetaTags() != '') {
	GEN_modifierMetaName('keywords', $appControleur->getMetaTags());
}

// +--------------------------------------------------------------------------------------------------+
// Fonctions d'affichage dans Papyrus
/**
 * Fonction d'affichage de Papyrus, pour le corps de page.
 */
function afficherContenuCorps() {
	return $appControleur->getContenuCorps();
}

function afficherContenuTete() {
	return $appControleur->getContenuTete();
}

function afficherContenuPied() {
	return $appControleur->getContenuPied();
}

function afficherContenuNavigation() {
	return $appControleur->getContenuNavigation();
}

function afficherContenuMenu() {
	//Retour du menu vide dans le cas de l'affichage dans Papyrus
	// $appControleur->getContenuMenu()
	return '';
}
?>