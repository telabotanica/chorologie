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
class Carte extends ModuleControleur {

	/** API "Carte" */
	protected $api;

	protected $largeurCarte;

	protected function init() {
		$this->api = new Cartes($this->conteneur);
		$this->largeurCarte = $this->conteneur->getParametre('largeur_carte');
	}

	public function executer() {
		$donnees = array();

		$donnees['url_base'] = $this->obtenirUrlBase();

		$donnees['url_base_liste_taxons'] = $donnees['url_base'] . "?module=liste-taxons";
		$donnees['carte'] = $this->api->getCarte(600);
		$donnees['legende'] = $this->api->getLegende();

		$this->setSortie(self::RENDU_CORPS, $this->getVue('carte', $donnees));
	}
}