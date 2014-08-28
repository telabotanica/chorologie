<?php
/**
 * Affiche la carte de l'ensemble de la dition, en colorant les zones selon le
 * nombre de taxons présents
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
class CarteTaxon extends ModuleControleur {

	/** API "Carte" */
	protected $api;

	protected $taxon;
	protected $largeurCarte;
	protected $nomSci;

	protected function init() {
		$this->api = new Cartes($this->conteneur);
		$this->apiTaxon = new Taxons($this->conteneur);
		$this->largeurCarte = $this->conteneur->getParametre('largeur_carte');
		$this->taxon = $this->capturerParam('taxon');
		$this->nomSci = $this->capturerParam('nom-sci');
	}

	public function executer() {
		$donnees = array();

		$donnees['url_base'] = $this->obtenirUrlBase();
		$donnees['url_base_liste_taxons'] = $donnees['url_base'] . "?module=liste-taxons";

		$donnees['titre_carte'] = sprintf($this->conteneur->getParametre('titre_carte_taxon'), $this->nomSci);

		if ($this->taxon != null) {
			$donnees['num_nom'] =  $this->taxon;
			$this->taxon = 'nn:' . $this->taxon;
			$donnees['carte'] = $this->api->getCarteTaxon($this->taxon, 600);
			$donnees['legende'] = $this->api->getLegendeTaxon($this->taxon);
			$donnees['infos_taxon'] = $this->apiTaxon->getInfosTaxon($this->taxon);
			$donnees['type_zone'] = Config::get('type_zone');
			$donnees['nb_zones_total'] = Config::get('nb_zones_total');
		}

		$this->setSortie(self::RENDU_CORPS, $this->getVue('carte-taxon', $donnees));
	}
}