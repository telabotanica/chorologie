<?php
/**
 * Gestion des Zones Géographiques
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
class ZonesGeo extends ChorologieDAO {

	/**
	 * Appelle le toileservice pour obtenir la sous-partie de la liste des zones
	 * géographiques disponibles, commençant à $depart et contenant $nbParPage
	 * résultats; si $masque est spécifié, ne considère que les résultats dont
	 * le nom commence par "masqueNom"
	 */
	public function listeZones($depart, $nbParPage, $masqueNom=null, $tri = null, $ordre = null) {
		$squeletteUrl = $this->conteneur->getParametre('tpl_url_service_zones');
		if ($masqueNom != null) {
			$masqueNom = $masqueNom . '%';
		}
		$url = sprintf($squeletteUrl, $depart, $nbParPage, $masqueNom, $tri, $ordre);
		echo "URL: $url<br/>";
		$donnees = $this->chargerDonnees($url);
		return $donnees;
	}
}
?>