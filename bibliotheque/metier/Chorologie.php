<?php
/**
 * Classe mère de l'API Chorologie.
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
abstract class Chorologie {

	private $restClient;
	protected $conteneur;

	/**
	 * Accepte éventuellement un conteneur, pour choper le client REST entre
	 * autres; sinon, prendra les éléments du Framework
	 * 
	 * @param Conteneur $conteneur
	 */
	public function __construct(Conteneur $conteneur = null) {
		$this->conteneur = $conteneur;
		$this->restClient = $this->getRestClient();
	}

	/**
	 * Formate une url à partir d'un template contenant des paramètres à remplacer sous la forme {monParametre}.
	 * Le tableau associatif de paramètres doit contenir en clé le paramêtre (monParametre) sans les accolades,
	 * la valeur correspondante sera la valeur de remplacement.
	 * Par défaut, les parametres suivant sont pris en compte par cette méthode :
	 *  - {projet} : le code du référentiel courrant ou définit dans le constructeur de l'objet métier.
	 *
	 * @param String $tpl le squelette d'url à formater.
	 * @param Array $parametres le tableau de parametres (sans accolades pour les clés).*/
	/*public function formaterUrl($tpl, Array $parametres) {
		$parametres = $this->ajouterParametreParDefaut($parametres);
		foreach($parametres as $key=> $value) {
			if(is_array($value)) {
				$value = implode(',', $value);
			}
			$tpl = str_replace('{'.$key.'}',rawurlencode($value),$tpl);
		}
		return $tpl;
	}*/

	/**
	 * Consulte une URL et retourne le résultat, ou déclenche une erreur
	 *
	 * @param $url l'URL du service
	 */
	protected function chargerDonnees($url) {
		$resultat = false;
		$json = $this->restClient->consulter($url);
		$entete = $this->restClient->getReponseEntetes();

		// Si le service meta-donnees fonctionne correctement, l'entete comprend la clé wrapper_data
		if (isset($entete['wrapper_data'])) {
			$resultat = json_decode($json, true);
			$this->entete = (isset($resultat['entete'])) ? $resultat['entete'] : null;
		} else {
			$m = "L'url <a href=\"$url\">$url</a> lancée via RestClient renvoie une erreur";
			trigger_error($m, E_USER_WARNING);
		}
		return $resultat;
	}

	/**
	 * Charge récursivement toutes les pages d'un service retournant des
	 * résultats paginés
	 *
	 * @param $url l'url du service à appeler pour charger les données.
	 */
	protected function chargerDonneesRecursivement($url) {
		$resultat = $this->chargerDonnees($url);
		if (isset($resultat['entete']['href.suivant'])) {
			$resultatSuivant = $this->chargerDonneesRecursivement($resultat['entete']['href.suivant']);
			if ($resultatSuivant) {
				// utilisation de + obligatoire pour ne pas casser l'indexation par des ids
				// numériques (au lieu de array_merge)
				$resultat['resultat'] = $resultat['resultat'] + $resultatSuivant['resultat'];
			}
		}
		return $resultat;
	}

	/**
	 * Tente de récupérer un client REST depuis le conteneur si celui-ci est
	 * défini, sinon depuis le Framework
	 * 
	 * @return RestClient le client REST
	 */
	private function getRestClient() {
		if ($this->restClient == null) {
			if ($this->conteneur != null) {
				$this->restClient = $this->conteneur->getRestClient();
			} else {
				$this->restClient = new RestClient();
			}
		}
		return $this->restClient;
	}

	/*function formaterUrl($tpl, Array $parametres, $enc = TRUE) {
		foreach($parametres as $key => $value) {
			if(is_array($value)) {
				$value = implode(',', $value);
			}
			$tpl = str_replace('{'.$key.'}',$enc ? rawurlencode($value) : $value, $tpl);
		}
		return $tpl;
	}*/
}