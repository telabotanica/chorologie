<?php
/**
 * Affiche la liste paginée des zones géographiques disponibles
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
class ListeZonesGeo extends ModuleControleur {

	/** API "ZonesGeo" */
	protected $api;

	protected $page;
	protected $nbParPage;
	protected $lettre;

	protected function init() {
		$this->api = new ZonesGeo($this->conteneur);
	}

	public function executer() {
		$donnees = array();

		$parametresUtilises = $this->capturerParams(array(
			'page' => 1,
			'nbParPage' => 20,
			'lettre' => null
		));
		//echo "PUTIL: " . print_r($parametresUtilises, true);

		// Transmisison des paramètres au squelette
		$donnees = array_merge($donnees, $parametresUtilises);
		$donnees['module'] = $this->obtenirNomModule();

		// URLs de base
		// attention à l'ordre des appels de cette chierie
		$donnees['url_base'] = $this->obtenirUrlBase();
		$donnees['url_module'] = $this->obtenirUrlModule();

		// Récupération de la liste des zones géographiques
		$depart = ($this->page - 1) * $this->nbParPage;
		$zones = $this->api->listeZones($depart, $this->nbParPage, $this->lettre);
		//echo "ZONES : " . print_r($zones, true) . "<br/>";

		// Pagination
		$donnees['nombre'] = min($zones['entete']['limite'], $zones['entete']['total']);
		$donnees['nombre_total'] = $zones['entete']['total'];
		$donnees['zones'] = $this->formaterZones($zones);
		$donnees['debut'] = $zones['entete']['depart'];
		$donnees['fin'] = $donnees['debut'] + $donnees['nombre'];

		$nbPages = Pagination::nombreDePagesSelonResultats($donnees['nombre_total'], $zones['entete']['limite']);
		$donnees['pages'] = Pagination::tableauPages($this->page, $nbPages, 5);
		$donnees['page_max'] = $nbPages;
		$donnees['paginateur'] = $this->getVueCommune('pagination', $donnees);

		// préparation du tableau
		$donnees['entetes'] = array();
		$donnees['zones'] = array();
		if (isset($zones['resultat']) && count($zones['resultat']) > 0) {
			$clefsResultats = array_keys($zones['resultat']);
			$premierResultat = $clefsResultats[0];
			$donnees['entetes'] = array_keys($zones['resultat'][$premierResultat]);
			$donnees['zones'] = $zones['resultat'];
		}
		/*foreach ($zones['resultat'] as $k => $v) {
			$donnees['zones'][] = $v;
		}*/
		//echo "DON ZONES : " . print_r($donnees['zones'], true) . "<br/>";

		// de A à Z
		$donnees['lettres'] = array();
		for ($i=65; $i<=90; $i++) {
			$donnees['lettres'][] = chr($i);
		}
		// possibilités de tailles de page
		$donnees['tailles_page'] = array(10, 20, 50, 100);
		// Vocabulaire
		$donnees['type'] = $this->conteneur->getParametre("type_zone");

		$this->setSortie(self::RENDU_CORPS, $this->getVue('liste-zones-geo', $donnees));
	}

	protected function formaterZones($zones) {
		$zonesFormatees = array();
		return $zonesFormatees;
	}
}