<?php
/**
 * Classe dont chaque controleur de l'application doit hériter.
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
class ModuleControleur extends Controleur {

	const META_TITRE = 'titre';
	const META_DESCRIPTION = 'description';
	const META_TAGS = 'tags';
	const ENTETE = 'entete';
	const RENDU_TETE = 'tete';
	const RENDU_CORPS = 'corps';
	const RENDU_PIED = 'pied';
	const RENDU_NAVIGATION = 'navigation';
	const RENDU_MENU = 'menu';

	private $sortie = array();
	protected $urlBase;
	protected $urlCourante;
	protected $conteneur;
	protected $nom;
	/** si true, ne montre que les taxons protégés; sinon montre tout */
	protected $proteges;
	/** si spécifiée, sera chargée en plus (écrasement) de la config de base */
	protected $secondeConfig;
	
	public function __construct(Conteneur $conteneur) {
		parent::__construct();
		$this->conteneur = $conteneur;

		// Chargement d'une double config. Utilisé avec Papyrus pour permettre
		// une config différente pour certaines entrées de menu
		if (isset($_GET['config'])) {
			$this->secondeConfig = $_GET['config'];
		}
		if ($this->secondeConfig != null) {
			Config::chargerFichierContexte($this->secondeConfig);
		}

		// mode "taxons protégés uniquement" ?
		$this->proteges = ($this->conteneur->getParametre('proteges') === '1');

		// Pour charger les squelettes depuis le même répertoire que le contrôleur
		$cheminSquelettesLocaux = $this->conteneur->getParametre('chemin_modules').
			AppControleur::getNomDossierDepuisClasse(get_class($this)).DS.
			$this->conteneur->getParametre('dossier_squelettes').DS;
		$this->base_chemin_squelette = $cheminSquelettesLocaux;

		$this->urlBase = Registre::get('chorologie.urlBase');
		$this->urlCourante = Registre::get('chorologie.urlCourante');
		$this->urlRedirection = Registre::get('chorologie.urlRedirection');

		$this->init();
	}

	/**
	 * Ajustements post-constructeur
	 */
	protected function init() {}

	/**
	 * Tente de récupérer un paramètre nommé $cle;
	 * cherche dans $_GET, puis dans $_POST, et en dernier recours affecte la
	 * valeur $defaut
	 * 
	 * @param string $cle le nom du paramètre a récupérer
	 */
	protected function capturerParam($cle, $defaut = null) {
		if (isset($_GET[$cle])) {
			$param = $_GET[$cle];
		} else {
			if (isset($_POST[$cle])) {
				$param = $_POST[$cle];
			} else {
				$param = $defaut;
			}
		}
		return $param;
	}

	/**
	 * Parcourt un tableau associatif, pour chaque paire $cle / $valeur, tente
	 * de récupérer le paramètre nommé $cle, ou lui affecte $valeur en cas d'échec
	 * 
	 * @param array $clesValeurs
	 * @return un tableau contenant en clefs les noms des paramètres et en valeurs
	 * des pointeurs vers les attributs de la classes ayant reçu les paramètres
	 */
	protected function capturerParams(array $clesValeurs) {
		$tableauRetour = array();
		foreach ($clesValeurs as $cle => $valeur) {
			$param = $this->capturerParam($cle, $valeur);
			$tableauRetour[$cle] = $param;
		}
		return $tableauRetour;
	}

	/**
 	* Attribue une position de sortie à un contenu.
 	*/
	protected function setSortie($position, $contenu, $fusionner = false) {
		if ($this->verifierExistenceTypeSortie($position)) {
			if ($fusionner) {
				if (isset($this->sortie[$position])) {
					$this->sortie[$position] .= $contenu;
				} else {
					$this->sortie[$position] = $contenu;
				}
			} else {
				$this->sortie[$position] = $contenu;
			}
		}
	}

	/**
	 * Vérifie l'existence du type de sortie indiqué pour son utilisation dans le tableau de sortie.
	 * @param string le type de sortie à tester.
	 * @return bool true si le type de sortie est valide, sinon false.
	 */
	private function verifierExistenceTypeSortie($type) {
		$existe = true;
		if ($type != self::RENDU_TETE &&
			$type != self::RENDU_CORPS &&
			$type != self::RENDU_PIED &&
			$type != self::RENDU_NAVIGATION &&
			$type != self::RENDU_MENU &&
			$type != self::ENTETE &&
			$type != self::META_TITRE &&
			$type != self::META_DESCRIPTION &&
			$type != self::META_TAGS) {
			trigger_error("Le type de sortie '$type' n'est pas une valeur prédéfinie.", E_USER_WARNING);
			$existe = false;
		}
		return $existe;
	}

	/**
	 * Retourne le tableau de sortie à utiliser dans le controleur principal de l'application.
	 */
	public function getSortie() {
		return $this->sortie;
	}

	/**
	 * Fusionne un tableau de sortie par défaut avec le tableau passé en paramêtre.
	 * @param array le tableau à fusionner
	 */
	private function fusionnerSortie($nouvelleSortie) {
		$sortieActuelle = $this->getSortie();
		foreach ($nouvelleSortie as $position => $nouveauContenu) {
			if ($nouveauContenu != '') {
				$contenuPrecedent = isset($sortieActuelle[$position]) ? $sortieActuelle[$position] : null;
				if ($nouveauContenu != $contenuPrecedent) {
					$this->setSortie($position, $nouveauContenu, true);
				}
			}
		}
	}
	
	/**
	* Vide toutes les sorties (utile en cas de classe statique et de module ajax)
	*/
	protected function viderSorties() {
		$this->setSortie(self::RENDU_TETE, '');
		$this->setSortie(self::RENDU_CORPS, '');
		$this->setSortie(self::RENDU_PIED, '');
		$this->setSortie(self::RENDU_NAVIGATION, '');
		$this->setSortie(self::RENDU_MENU, '');
	}

	/**
	 * Execute l'action d'un module donnée et fusionne le résultat avec le tableau de sortie.
	 * Permet à une action d'un module donnée de charger le résultat d'une autre action de module.
	 */
	protected function executerAction($ClasseModule, $action, $parametres = array()) {
		$module = new $ClasseModule();
		$sortiePrecedente = $this->getSortie();
		// Lancement de l'action demandée du module chargé
		if (method_exists($module, $action)) {
			if (isset($parametres)) {
				$module->$action($parametres);
			} else {
				$module->$action();
			}
			$nouvelleSortie = $module->getSortie();
			$this->fusionnerSortie($nouvelleSortie);
		} else {
			$m = "La méthode '$action' du controleur '$ClasseModule' est introuvable.";
			trigger_error($m, E_USER_ERROR);
		}
	}

	/**
	 * Charge un squelette de vue depuis l'emplacement commune.
	 * @param String $tpl le nom du squelette à charger sans l'extenssion de fichier.
	 * @param Array $donnees les données à passer au squelette PHP.
	 */
	protected function getVueCommune($tpl, $donnees=array()) {
		$cheminOrigine = $this->getCheminSquelette();
		$this->setCheminSquelette($this->conteneur->getParametre('chemin_squelettes'));
		$vue = $this->getVue($tpl, $donnees);
		$this->setCheminSquelette($cheminOrigine);
		return $vue;
	}

	/**
	 * Pour mutualiser la création de l'entête de l'application, sa gestion est faite dans cette classe.
	 */
	public function chargerEnteteGeneral() {
		$this->setSortie(self::RENDU_TETE, $this->getVueCommune('entete_page'));
	}

	/**
	 * Pour mutualiser la création du menu de l'application, sa gestion est gérée dans cette classe.
	 */
	public function chargerMenuGeneral() {
		$donnees = array();
		$donnees['base_url_application'] = $this->conteneur->getParametre("base_url_application");
		$this->setSortie(self::RENDU_MENU, $this->getVueCommune('menu', $donnees));
	}

	/**
	 * Pour mutualiser la création du pied de page de l'application, sa gestion est gérée dans cette classe.
	 */
	public function chargerPiedGeneral() {
		$donnees['appli'] = Framework::getInfoAppli();
		$donnees['courrielContact'] = $this->conteneur->getParametre('courriel_contact');

		$this->setSortie(self::RENDU_PIED, $this->getVueCommune('pied_page', $donnees));
	}

	//+------------------------------------------------------------------------------------------------------+
	// GESTION du CHARGEMENT des CLASSES MÉTIERS
	protected function getEfloreNoms() {
		if (! isset($this->EfloreNoms)) {
			$this->EfloreNoms = new Noms();
		}
		return $this->EfloreNoms;
	}

	protected function getEfloreTaxons() {
		if (! isset($this->EfloreTaxons)) {
			$this->EfloreTaxons = new Taxons();
		}
		return $this->EfloreTaxons;
	}

	public function obtenirUrlBase() {
		$url = $this->urlBase->getURL();
		return $url;
	}

	/**
	 * Retourne l'URL de base du module en cours, c'est à dire l'URL de base de
	 * l'application suivie de "&module=nom-du-module"
	 * @param array $parametres les paramètres à ajouter à l'URL (optionnel)
	 * @return string URL
	 */
	public function obtenirUrlModule(array $parametres=array()) {
		$nomModule = $this->obtenirNomModule();
		$parametres = array_merge(array('module' => $nomModule), $parametres);
		$this->urlBase->setRequete($parametres);
		$url = $this->urlBase->getURL();
		return $url;
	}

	/**
	 * Retourne le nom du module dans un format "miniscule et tirets", tel qu'il
	 * peut être passé comme paramètre de l'URL (?module=)
	 * @return string le nom du module
	 */
	public function obtenirNomModule() {
		$classe = get_class($this);
		$nomModule = AppControleur::getNomModuleDepuisNomDossier(AppControleur::getNomDossierDepuisClasse($classe));
		return $nomModule;
	}

	public function redirigerVers($url) {
		$url = str_replace('&amp;', '&', $url);
		header("Location: $url");
	}

	public function getParametresUrlListe() {
		$parametres = array(
			'referentiel' => Registre::get('parametres.referentiel'),
			'module' => 'liste',
			'action' => 'liste',
			'rang' => $this->rang,
			'lettre' => $this->lettre
		);
		return $parametres;
	}

	public function obtenirUrlListeZonesGeo() {
		$parametres = $this->getParametresUrlListe();
		$parametres['rang'] = 'F';
		$this->urlBase->setRequete($parametres);
		$url = $this->urlBase->getURL();
		return $url;
	}

	public function obtenirUrlListeGenre() {
		$parametres = $this->getParametresUrlListe();
		$parametres['rang'] = 'G';
		$this->urlBase->setRequete($parametres);
		$url = $this->urlBase->getURL();
		return $url;
	}



	public function getParametresUrlResultat() {
		$parametres = array(
			'referentiel' => Registre::get('parametres.referentiel'),
			'module' => 'recherche',
			'action' => Registre::get('parametres.action'),
			'submit' => 'Rechercher',
			'type_nom' => isset($_GET['type_nom']) ? $_GET['type_nom'] : 'nom_scientifique',
			'nom' => isset($_GET['nom']) ? $_GET['nom'] : ''
		);
		return $parametres;
	}

	public function obtenirUrlResultatDetermination() {
		$parametres = $this->getParametresUrlResultat();
		$parametres['resultat'] = 'determination';
		$this->urlBase->setRequete($parametres);
		$url = $this->urlBase->getURL();
		return $url;
	}

	public function obtenirUrlResultatAlphab() {
		$parametres = $this->getParametresUrlResultat();
		$parametres['resultat'] = 'alphab';
		$this->urlBase->setRequete($parametres);
		$url = $this->urlBase->getURL();
		return $url;
	}

	public function obtenirUrlResultatRetenu() {
		$parametres = $this->getParametresUrlResultat();
		$parametres['resultat'] = 'retenu';
		$this->urlBase->setRequete($parametres);
		$url = $this->urlBase->getURL();
		return $url;
	}

	public function obtenirUrlResultatDecompo() {
		$parametres = $this->getParametresUrlResultat();
		$parametres['resultat'] = 'decompo';
		$this->urlBase->setRequete($parametres);
		$url = $this->urlBase->getURL();
		return $url;
	}
	
	public function obtenirUrlBasePagination($parametresUtilises) {
		$params_pagination = $parametresUtilises;
		unset($params_pagination['page']);
		unset($params_pagination['nbParPage']);
		$url_base_pagination = $this->obtenirUrlModule(array_filter($params_pagination));
		
		return $url_base_pagination;
	}
	
	public function obtenirUrlBaseColonnesTriables($parametresUtilises) {
		$urls = array();
		// Urls de base pour les colonnes triables
		$params_sans_tri = $parametresUtilises;
		unset($params_sans_tri['ordre']);
		unset($params_sans_tri['tri']);
		$urls['url_module_sans_tri'] = $this->obtenirUrlModule(array_filter($params_sans_tri));
		$urls['ordre_tri_inverse'] = ($parametresUtilises['ordre'] == "ASC") ? "DESC" : "ASC";
		
		return $urls;
	}
	
	public function obtenirUrlsBasePaginationEtColonnesTriables($parametresUtilises) {
		$urls = array();
		
		// Url de base de la pagination
		$urls['url_base_pagination'] = $this->obtenirUrlBasePagination($parametresUtilises);
		
		// Urls de base pour les colonnes triables
		$urls_tri = $this->obtenirUrlBaseColonnesTriables($parametresUtilises);
		$urls['url_module_sans_tri'] = $urls_tri['url_module_sans_tri'];
		$urls['ordre_tri_inverse'] = $urls_tri['ordre_tri_inverse'];
		
		return $urls;
	}
	
}
?>