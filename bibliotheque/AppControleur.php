<?php
/**
 * AppControleur est le contrôleur principal de l'application.
 * Il répartit les demandes utilisateurs dans les différents modules, exécute les actions et redistribue le code
 * HTML dans les différentes fonctions d'affichage.
 * Ce n'est pas un singleton car il accepte un conteneur comme argument du constructeur,
 * (facilite le test) mais il est destiné à n'être instancié qu'une fois dans l'application.
 *
 * @category	PHP 5.2
 * @package		chorologie
 * @author		Jean-Pascal MILCENT <jpm@tela-botanica.org>
 * @author		Delphine CAUQUIL <delphine@tela-botanica.org>
 * @author		Mathias CHOUET <mathias@tela-botanica.org>
 * @copyright	2014 Tela-Botanica
 * @license		http://www.gnu.org/licenses/gpl.html Licence GNU-GPL-v3
 * @license		http://www.cecill.info/licences/Licence_CeCILL_V2-fr.txt Licence CECILL-v2
 * @version		$Id$
 */

class AppControleur {

	protected $parametres = array();
	protected $conteneur;
	protected $source;

	public function __construct(Conteneur $conteneur) {
		$this->conteneur = $conteneur;
		$sortie = array(
			'titre' => '', 'description' => '', 'tags' => '',
			'entete' => '', 'tags' => '',
			'corps' => '', 'tete' => '', 'pied' => '',
			'navigation' => '', 'menu' => '');
		$this->parametres = array(
			'module' => $this->conteneur->getParametre('module_defaut'),
			'action' => $this->conteneur->getParametre('action_defaut'),
			'sortie' => $sortie);
		$this->source = $this->conteneur->getParametre('source_donnees');
	}

	/**
	 * Exécution du controleur principal en fonction des paramêtres de l'URL
	 */
	public function executer() {
		$this->gererSession();

		$this->nettoyerGet();
		$this->capturerParametres();
		$this->initialiserRegistre();

		spl_autoload_register(array(get_class(), 'chargerClasse'));

		$this->executerModule();
	}

	protected function gererSession() {
		if ($this->conteneur->getParametre('session_demarrage')) {
			// Attribution d'un nom à la session
			session_name($this->conteneur->getParametre('session_nom'));
			// Démarrage de la session
			session_start();
		}
	}

	// @TODO regarder dans DeL services "Contexte"
	protected function nettoyerGet() {
		foreach ($_GET as $cle => $valeur) {
			$verifier = array('NULL', "\n", "\r", "\\", "'", '"', "\x00", "\x1a", ';');
			$_GET[$cle] = strip_tags(str_replace($verifier, '', $valeur));
		}
	}

	protected function capturerParametres() {
		if (isset($_GET['module'])) {
			$this->parametres['module'] = $_GET['module'];
		}
		if (isset($_GET['action'])) {
			$this->parametres['action'] = $_GET['action'];
		}
	}

	// "public" pour pouvoir être appelée distinctememnt de "initialiser" lors des tests PHPUnit
	public function initialiserRegistre() {
		Registre::set('chorologie.urlBase', new Url($this->conteneur->getParametre('base_url_application_index')));
		Registre::set('chorologie.urlBaseDossier', new Url($this->conteneur->getParametre('base_url_application')));
		Registre::set('chorologie.urlCourante', $this->getUrlCourante());
		Registre::set('chorologie.urlRedirection', $this->getUrlRedirection());

		Registre::set('parametres.module', $this->parametres['module']);
		Registre::set('parametres.action', $this->parametres['action']);
	}

	protected function getUrlCourante() {
		$url = false;
		if (isset($_SERVER['REQUEST_URI'])) {
			$url = $_SERVER['REQUEST_URI'];
		}
		return ($url) ? new Url($url) : $url;
	}

	protected function getUrlRedirection() {
		$url = false;
		if (isset($_SERVER['REDIRECT_URL']) && !empty($_SERVER['REDIRECT_URL'])) {
			if (isset($_SERVER['REDIRECT_QUERY_STRING']) && !empty($_SERVER['REDIRECT_QUERY_STRING'])) {
				$url = $_SERVER['REDIRECT_URL'].'?'.$_SERVER['REDIRECT_QUERY_STRING'];
			} else {
				$url = $_SERVER['REDIRECT_URL'];
			}
		}
		return ($url) ? new Url($url) : $url;
	}

	protected function chargerClasse($nom_classe) {
		$dossiers_classes = array(
			$this->conteneur->getParametre('chemin_modules') . $this->getNomDossierModuleCourant() . DS,
			$this->conteneur->getParametre('chemin_modules') . self::getNomDossierDepuisClasse($nom_classe) . DS,
			$this->conteneur->getParametre('chemin_modeles'));

		$dossiers_classes = array_unique($dossiers_classes);

		foreach ($dossiers_classes as $chemin) {
			$fichier_a_tester = $chemin.$nom_classe.'.php';
			if (file_exists($fichier_a_tester)) {
				include_once $fichier_a_tester;
				return null;
			}
		}
	}

	// @TODO devrait être déplacé dans une lib
	public static function getNomDossierDepuisParametre($parametre) {
		$dossier = str_replace('-', '_', strtolower($parametre));
		return $dossier;
	}

	// @TODO devrait être déplacé dans une lib
	public static function getNomModuleDepuisNomDossier($nomDossier) {
		$nomModule = str_replace('_', '-', strtolower($nomDossier));
		return $nomModule;
	}

	// @TODO devrait être déplacé dans une lib
	public static function getNomDossierDepuisClasse($nomClasse) {
		$dossier = str_replace(' ', '_', strtolower(preg_replace('/(?<!^)([A-Z])/',' $0', $nomClasse)));
		return $dossier;
	}

	// @TODO devrait être déplacé dans une lib
	protected function getNomDossierModuleCourant() {
		$dossier = self::getNomDossierDepuisParametre($this->parametres['module']);
		return $dossier;
	}

	protected function getNomClasseModule() {
		$dossier = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($this->parametres['module']))));
		return $dossier;
	}

	protected function getNomMethodeAction() {
		$methode = 'executer'.
			str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($this->parametres['action']))));
		return $methode;
	}

	protected function executerModule() {
		$classeModule = $this->getNomClasseModule();
		$action = $this->getNomMethodeAction();

		// Nous vérifions que le module existe
		if (class_exists($classeModule)) {
			$module = new $classeModule($this->conteneur, $this);

			// Chargement Entete et Pied de page par défaut
			$module->chargerEnteteGeneral();
			$module->chargerMenuGeneral();
			$module->chargerPiedGeneral();

			// Lancement de l'action demandée du module chargé
			if (method_exists($module, $action)) {
				$module->$action();
			} else {
				$m = "La méthode '$action' du controleur '$classeModule' est introuvable.";
				trigger_error($m, E_USER_ERROR);
			}

			$this->fusionnerSortie($module->getSortie());
		} else {
			$m = "La classe du controleur '$classeModule' est introuvable.";
			trigger_error($m, E_USER_ERROR);
		}
	}

	/**
	 * Fusionne un tableau de sortie par défaut avec le tableau renvoyé par l'action du module.
	 * @param array le tableau à fusionner
	 */
	protected function fusionnerSortie($sortie) {
		$this->parametres['sortie'] = array_merge($this->parametres['sortie'], $sortie);
	}

	/**
	 * Retourne le titre du contenu de l'application.
	 */
	public function getMetaTitre() {
		$contenu = $this->parametres['sortie']['titre'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne la description du contenu de l'application.
	 */
	public function getMetaDescription() {
		$contenu = $this->parametres['sortie']['description'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne les mots-clés (tags) du contenu de l'application.
	 */
	public function getMetaTags() {
		$contenu = $this->parametres['sortie']['tags'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne les informations à placer dans la balise HEAD du HTML.
	 */
	public function getEntete() {
		$contenu = $this->parametres['sortie']['entete'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne le contenu du corps de l'application.
	 */
	public function getContenuCorps() {
		$contenu = $this->parametres['sortie']['corps'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne le contenu de la tête de l'application.
	 */
	public function getContenuTete() {
		$contenu = $this->parametres['sortie']['tete'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne le contenu du pied de l'application.
	 */
	public function getContenuPied() {
		$contenu = $this->parametres['sortie']['pied'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne les éléments de navigation (onglet, fils d'ariane) de l'application.
	 */
	public function getContenuNavigation() {
		$contenu = $this->parametres['sortie']['navigation'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne les éléments du menu de l'application.
	 */
	public function getContenuMenu() {
		$contenu = $this->parametres['sortie']['menu'];
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Retourne les chronos pris dans l'appli
	 */
	public function getChrono() {
		$sortie = '';
		if ($this->conteneur->getParametre('benchmark_chrono')) {
			$chrono = Chronometre::afficherChrono();
			$sortie = $this->convertirEncodage($chrono);
		}
		return $sortie;
	}

	/**
	 * Retourne les messages d'exceptions et d'erreurs.
	 */
	public function getExceptions() {
		$contenu = ($this->conteneur->getParametre('debogage')) ? GestionnaireException::getExceptions() : '';
		$sortie = $this->convertirEncodage($contenu);
		return $sortie;
	}

	/**
	 * Convertion du contenu de l'application (voir fichier config.ini : appli_encodage),
	 * dans le format de sortie désiré (voir fichier config.ini : sortie_encodage).
	 * Cette convertion a lieu seulement si les formats sont différents.
	 */
	protected function convertirEncodage($contenu) {
		if ($this->conteneur->getParametre('sortie_encodage') != $this->conteneur->getParametre('appli_encodage')) {
			$contenu = mb_convert_encoding($contenu, $this->conteneur->getParametre('sortie_encodage'), $this->conteneur->getParametre('appli_encodage'));
		}
		return $contenu;
	}
}
?>