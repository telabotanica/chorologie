<?php
/**
 * Classe mère des DAO de Chorologie.
 *
 * @package		chorologie
 * @author		Jean-Pascal MILCENT <jpm@tela-botanica.org>
 * @author		Delphine CAUQUIL <delphine@tela-botanica.org>
 * @author		Mathias Chouet <mathias@tela-botanica.org>
 * @copyright	2014 Tela-Botanica
 * @license		http://www.gnu.org/licenses/gpl.html Licence GNU-GPL-v3
 * @license		http://www.cecill.info/licences/Licence_CeCILL_V2-fr.txt Licence CECILL-v2
 * @version		$Id$
 */
abstract class ChorologieDAO {

	protected $restClient;
	protected $conteneur;

	/**
	 * Demande un conteneur, pour choper le client REST entre
	 * autres
	 * 
	 * @param Conteneur $conteneur
	 */
	public function __construct(Conteneur $conteneur) {
		$this->conteneur = $conteneur;
		$this->restClient = $conteneur->getRestClient();
		$this->init();
	}

	/**
	 * Ajustements post-constructeur
	 */
	protected function init() {
	}

	/**
	 * Consulte une URL et retourne le résultat (ou déclenche une erreur), en
	 * admettant qu'il soit au format JSON
	 *
	 * @param string $url l'URL du service
	 */
	protected function chargerDonnees($url, $decoderJSON = true) {
		$resultat = $this->restClient->consulter($url);
		$entete = $this->restClient->getReponseEntetes();

		// Si le service meta-donnees fonctionne correctement, l'entete comprend la clé wrapper_data
		if (isset($entete['wrapper_data'])) {
			if ($decoderJSON) {
				$resultat = json_decode($resultat, true);
				$this->entete = (isset($resultat['entete'])) ? $resultat['entete'] : null;
			}
		} else {
			$m = "L'url <a href=\"$url\">$url</a> lancée via RestClient renvoie une erreur";
			trigger_error($m, E_USER_WARNING);
		}
		return $resultat;
	}

	/**
	 * Consulte une URL et retourne le résultat sans chercher à décoder du JSON
	 * 
	 * @param string $url l'URL du service
	 */
	protected function chargerDonneesBrutes($url) {
		$donnees = $this->chargerDonnees($url, false);
		return $donnees;
	}
}