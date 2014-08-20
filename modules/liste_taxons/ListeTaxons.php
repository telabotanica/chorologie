<?php
/**
 * Affiche la liste paginée des taxons disponibles
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
class ListeTaxons extends ModuleControleur {

	/** API "Taxons" */
	protected $api;

	protected function init() {
		$this->api = new Taxons($this->conteneur);
	}

	public function executer() {
		$donnees = array();

		$parametresUtilises = $this->capturerParams(array(
			'page' => 1,
			'nbParPage' => 20,
			'lettre' => null,
			'zone-geo' => null,
			'tri'		=> "nom_sci",
			'ordre'	=> "ASC"			
		));

		// Transmisison des paramètres au squelette
		$donnees = array_merge($donnees, $parametresUtilises);
		$donnees['module'] = $this->obtenirNomModule();

		// URLs de base
		// attention à l'ordre des appels de cette chierie
		$donnees['url_base'] = $this->obtenirUrlBase();
		$donnees['url_module'] = $this->obtenirUrlModule();
		
		// Ajouts des urls de pagination et de tri
		$donnees = array_merge($donnees, $this->obtenirUrlsBasePaginationEtColonnesTriables($parametresUtilises));
		
		// URL pour les liens eFlore
		$donnees['url_base_eflore_bdtfx'] = $this->conteneur->getParametre('url_base_eflore_bdtfx');

		// Récupération de la liste des taxons
		$depart = ($parametresUtilises['page'] - 1) * $parametresUtilises['nbParPage'];
		$taxons = $this->api->listeTaxons($depart, $parametresUtilises['nbParPage'], 
											$parametresUtilises['lettre'], $parametresUtilises['zone-geo'],
											$parametresUtilises['tri'], $parametresUtilises['ordre']);

		// Pagination
		$donnees['nombre'] = min($taxons['entete']['limite'], $taxons['entete']['total']);
		$donnees['nombre_total'] = $taxons['entete']['total'];
		$donnees['debut'] = $taxons['entete']['depart'];
		$donnees['fin'] = $donnees['debut'] + $donnees['nombre'];

		$nbPages = Pagination::nombreDePagesSelonResultats($donnees['nombre_total'], $taxons['entete']['limite']);
		$donnees['pages'] = Pagination::tableauPages($this->page, $nbPages, 5);
		$donnees['page_max'] = $nbPages;
		$donnees['paginateur'] = $this->getVueCommune('pagination', $donnees);

		//echo "TAX : " . print_r($taxons, true) . " (" . count($taxons['resultat']) . ")<br/>";
		// préparation du tableau
		$donnees['entetes'] = array();
		$donnees['taxons'] = array();
		if (isset($taxons['resultat']) && count($taxons['resultat']) > 0) {
			$premiereClef = reset($taxons['resultat']);
			// certains résultats de ws sont associatifs (caca), d'autres non :-(
			if (is_array($premiereClef)) {
				$premierResultat = $premiereClef;
			} else {
				$premierResultat = $taxons['resultat'][$premiereClef];
			}
			$donnees['entetes'] = array_keys($premierResultat);
			$donnees['taxons'] = $taxons['resultat'];
		}
		//echo "DON TAX : " . print_r($donnees['taxons'], true) . "<br/>";

		// de A à Z
		$donnees['lettres'] = array();
		for ($i=65; $i<=90; $i++) {
			$donnees['lettres'][] = chr($i);
		}
		// possibilités de tailles de page
		$donnees['tailles_page'] = array(10, 20, 50, 100);
		$this->setSortie(self::RENDU_CORPS, $this->getVue('liste-taxons', $donnees));
	}
	
	private function genererUrlTri($url_module, $tri, $ordre) {
		$ordre = ($ordre == "ASC") ? "DESC" : "ASC";
		return $url_module."&tri=".$tri."&ordre=".$ordre;
	}
}