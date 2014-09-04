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

	protected $numNom;
	protected $largeurCarte;
	protected $nomSci;

	protected function init() {
		$this->api = new Cartes($this->conteneur);
		$this->apiTaxon = new Taxons($this->conteneur);
		$this->largeurCarte = $this->conteneur->getParametre('largeur_carte');
		$this->numNom = $this->capturerParam('taxon');
	}

	public function executer() {
		$donnees = array();

		$donnees['url_base'] = $this->obtenirUrlBase();
		$donnees['url_base_liste_taxons'] = $donnees['url_base'] . "?module=liste-taxons";

		$donnees['metadonnees_source'] = $this->conteneur->getParametre('metadonnees_source');

		if ($this->numNom != null) {
			$donnees['num_nom'] = $this->numNom;
			$this->numNom = 'nn:' . $this->numNom;
			$infosTaxon = $this->apiTaxon->getInfosTaxon($this->numNom);
			$this->nomSci = $infosTaxon['nom_sci'];
			$donnees['carte'] = $this->api->getCarteTaxon($this->numNom, 600);
			$donnees['legende'] = $this->api->getLegendeTaxon($this->numNom);
			$donnees['infos_taxon'] = $infosTaxon;
			$donnees['type_zone'] = Config::get('type_zone');
			$donnees['nb_zones_total'] = Config::get('nb_zones_total');
			$donnees['titre_carte'] = sprintf($this->conteneur->getParametre('titre_carte_taxon'), $this->nomSci);
		}

		$this->setSortie(self::RENDU_CORPS, $this->getVue('carte-taxon', $donnees));
	}
}