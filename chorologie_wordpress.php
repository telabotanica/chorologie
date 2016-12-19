<?php

/** Inclusion du fichier principal de l'application */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'chorologie.php';

// Annuler les fausses bonnes idées du JPFramework
restore_exception_handler();
restore_error_handler();

// Configuration des URL d'après l'URL actuelle de Wordpress
global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));
// @TODO
Config::set('base_url_application', $current_url);
Config::set('base_url_application_index', $current_url);

// Hook d'ajout des scripts
add_action('wp_enqueue_scripts', 'chorologie_scripts_et_styles');

// ajout des scripts - utiliser dans le hook ci-dessus uniquement
function chorologie_scripts_et_styles() {
	// Cette variable est définie dans le thème WP par template-chorologie.php,
	// d'après l'option "applis_externes_chemin_chorologie"
	global $chemin_chorologie_http;
	
	// Scripts
	// @TODO remplacer ce patch cracra par une mise à jour du code JS de Chorologie
	wp_enqueue_script('jquery-noconflict-compat', 'https://resources.tela-botanica.org/jquery/jquery-noconflict-compat.js');
	wp_enqueue_script('chorologie-general', $chemin_chorologie_http . '/presentations/js/chorologie.js');

	// Styles
	wp_enqueue_style('bootstrap-limited', 'https://resources.tela-botanica.org/bootstrap/3.2.0/css/bootstrap-limited.min.css');
	wp_enqueue_style('chorologie', $chemin_chorologie_http . '/presentations/css/chorologie.css');
	wp_enqueue_style('chorologie_specifique', $chemin_chorologie_http . '/presentations/css/eflore.css'); // @TODO adapter
}

// Initialisation de l'appli
chorologie_initialisation(); // défini dans chorologie.php

// Affichage du contenu de l'application
function eflore_get_contenu() {
	return AppControleur::getContenuCorps();
}

function chorologie_get_contenu() {
	// nous avons besoin de la div bsh car le le style bootstrap est limité à sa portée
	return '<div id="bsh">'.PapControleur::$appControleur->getContenuCorps().'</div>';
}

function chorologie_get_contenu_tete() {
	return PapControleur::$appControleur->getContenuTete();
}

function chorologie_get_contenu_pied() {
	return PapControleur::$appControleur->getContenuPied();
}

function chorologie_get_contenu_navigation() {
	return PapControleur::$appControleur->getContenuNavigation();
}
