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
	protected $urlLegende;
	protected $couleur;

	protected function init() {
		 $this->urlCarte = $this->conteneur->getParametre('tpl_url_service_carte');
		 $this->urlLegende = $this->conteneur->getParametre('tpl_url_service_legende');
		 $this->couleur = $this->conteneur->getParametre('couleur_carte');
	}

	public function getCarte($largeur, $taxon=null) {
		$url = sprintf($this->urlCarte, $largeur, $taxon, $this->couleur);
	}

	public function getLegende($taxon=null) {
		$url = sprintf($this->urlCarte, $taxon, $this->couleur);
	}
}	
?>