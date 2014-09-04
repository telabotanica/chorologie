<?php
/**
 * Génère des cartes de chorologie à partir d'un modèle SVG et de données de présence
 *
 * @package		chorologie
 * @author		Tela Botanica <equipe-dev@tela-botanica.org>
 * @copyright	2014 Tela-Botanica
 * @license		http://www.gnu.org/licenses/gpl.html Licence GNU-GPL-v3
 * @license		http://www.cecill.info/licences/Licence_CeCILL_V2-fr.txt Licence CECILL-v2
 * @version		$Id$
 */
class Cartes extends ChorologieDAO {

	protected $urlCarte;
	protected $urlCarteLegende;
	protected $urlCarteTaxon;
	protected $urlCarteTaxonLegende;

	protected function init() {
		 $this->urlCarte = $this->conteneur->getParametre('tpl_url_service_carte');
		 $this->urlCarteLegende = $this->conteneur->getParametre('tpl_url_service_carte_legende');
		 $this->urlCarteTaxon = $this->conteneur->getParametre('tpl_url_service_carte_taxon');
		 $this->urlCarteTaxonLegende = $this->conteneur->getParametre('tpl_url_service_carte_taxon_legende');
	}
	
	protected function getUrlCarte($largeur, $protegesSeulement=false) {
		$url = sprintf($this->urlCarte, $largeur, $protegesSeulement);
		return $url;
	}

	public function getCarte($largeur, $protegesSeulement=false) {
		$url = $this->getUrlCarte($largeur, $protegesSeulement);
		$donnees = $this->chargerDonneesBrutes($url);
		return $donnees;
	}

	public function getLegende($protegesSeulement=false) {
		$url = sprintf($this->urlCarteLegende, $protegesSeulement);
		$donnees = $this->chargerDonnees($url);
		return $donnees;
	}

	protected function getUrlCarteTaxon($taxon, $largeur) {
		$url = sprintf($this->urlCarteTaxon, $taxon, $largeur);
		return $url;
	}

	public function getCarteTaxon($taxon, $largeur) {
		$url = $this->getUrlCarteTaxon($taxon, $largeur);
		$donnees = $this->chargerDonneesBrutes($url);
		return $donnees;
	}

	public function getLegendeTaxon($taxon) {
		$url = sprintf($this->urlCarteTaxonLegende, $taxon);
		$donnees = $this->chargerDonnees($url);
		return $donnees;
	}
}	
?>