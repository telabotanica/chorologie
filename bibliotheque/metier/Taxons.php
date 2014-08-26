<?php
/**
 * Gestion des Taxons
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
class Taxons extends ChorologieDAO {

	/**
	 * Appelle le toileservice pour obtenir la sous-partie de la liste des taxons
	 * géographiques, commençant à $depart et contenant $nbParPage résultats; si
	 * $masque est spécifié, ne considère que les résultats dont le nom commence par "masqueNom"
	 */
	public function listeTaxons($depart, $nbParPage, $masqueNom=null, $zoneGeo=null, $tri="nom_sci", $ordre="ASC") {
		$squeletteUrl = $this->conteneur->getParametre('tpl_url_service_taxons');
		if ($masqueNom != null) {
			$masqueNom = $masqueNom . '%';
		}
		$url = sprintf($squeletteUrl, $depart, $nbParPage, $masqueNom, $zoneGeo, $tri, $ordre);
		$donnees = $this->chargerDonnees($url);
		return $donnees;
	}
	
	public function getInfosTaxon($numNom) {
		$squeletteUrl = $this->conteneur->getParametre('tpl_url_service_infos_taxons');
		$url = sprintf($squeletteUrl, $numNom);
		$donnees = $this->chargerDonnees($url);
		return $donnees;
	}
}
?>