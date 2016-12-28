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
	/** API "Zones Géographiques" */
	protected $apiZonesGeo;

	protected function init() {
		$this->api = new Taxons($this->conteneur);
		$this->apiZonesGeo = new ZonesGeo($this->conteneur);
		$this->proteges = $this->proteges || ($this->capturerParam('proteges') === '1');
	}

	public function executer() {
		$donnees = array();

		$parametresUtilises = $this->capturerParams(array(
			'idxPage' => 1,
			'nbParPage' => 20,
			'lettre' => null,
			'zone-geo' => null,
			'tri' => "nom_sci",
			'ordre'	=> "ASC"			
		));
		
		$params_base = array();
		$donnees['nom_zone_geo'] = null;
		if($parametresUtilises['zone-geo'] != null) {
			$params_base['zone-geo'] = $parametresUtilises['zone-geo'];
			$infosZone = $this->apiZonesGeo->infosZone($params_base['zone-geo']);
			if(isset($infosZone['resultat']) && count($infosZone['resultat']) > 0) {
				$zone = array_pop($infosZone['resultat']);
				$donnees['nom_zone_geo'] = $zone['nom'];
			}
		}

		// Transmisison des paramètres au squelette
		$donnees = array_merge($donnees, $parametresUtilises);
		$donnees['module'] = $this->obtenirNomModule();

		// URLs de base
		// attention à l'ordre des appels de cette chierie
		$donnees['url_base'] = $this->obtenirUrlBase();
		$donnees['url_module'] = $this->obtenirUrlModule($params_base);
		
		// Ajouts des urls de pagination et de tri
		$donnees = array_merge($donnees, $this->obtenirUrlsBasePaginationEtColonnesTriables($parametresUtilises));
		
		// URL pour les liens eFlore
		$donnees['url_base_eflore_bdtfx'] = $this->conteneur->getParametre('url_base_eflore_bdtfx');
		$donnees['url_logo_tb'] = $this->conteneur->getParametre('url_logo_tb');

		// Récupération de la liste des taxons
		$depart = ($parametresUtilises['idxPage'] - 1) * $parametresUtilises['nbParPage'];
		$taxons = $this->api->listeTaxons(
			$depart,
			$parametresUtilises['nbParPage'],
			$parametresUtilises['lettre'],
			$parametresUtilises['zone-geo'],
			$parametresUtilises['tri'],
			$parametresUtilises['ordre'],
			$this->proteges
		);
		
		// Pagination
		$donnees['nombre'] = min($taxons['entete']['limite'], $taxons['entete']['total']);
		$donnees['nombre_total'] = $taxons['entete']['total'];
		$donnees['debut'] = $taxons['entete']['depart'];
		$donnees['fin'] = $donnees['debut'] + $donnees['nombre'];

		$nbPages = Pagination::nombreDePagesSelonResultats($donnees['nombre_total'], $taxons['entete']['limite']);
		$donnees['pages'] = Pagination::tableauPages($donnees['idxPage'], $nbPages, 5);
		$donnees['page_max'] = $nbPages;
		$donnees['paginateur'] = $this->getVueCommune('pagination', $donnees);

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

		// Extraction du premier nom commun et groupement de status de protection
		$donnees['protection_statuts'] = array();
		$protections = array();
		foreach ($donnees['taxons'] as $k => $taxon) {
			if ($taxon['noms_vernaculaires'] != null) {
				$nomCommun = explode(',', $taxon['noms_vernaculaires']);
				$nomCommun = $nomCommun[0];
				$donnees['taxons'][$k]['nom_commun'] = $nomCommun;
			}
			if ($taxon['statuts_protection'] != null) {
				$statutsProt = explode(',', $taxon['statuts_protection']);
				$protections = array_merge($protections, array_map('trim', $statutsProt));
			}
		}
		$protections = array_unique($protections);

		// extraction des abréviations des statuts et textes de protection
		// mentionnés dans la liste en cours, pour la légende
		$donnees['protection_statuts'] = array();
		$donnees['protection_textes'] = array();
		$abr_statuts = $this->conteneur->getParametre('protection_abr_statuts');
		$abr_textes = $this->conteneur->getParametre('protection_abr_textes');
		$tpl_url_service_statuts = $this->conteneur->getParametre('tpl_url_service_statuts');
		$tpl_url_service_textes = $this->conteneur->getParametre('tpl_url_service_textes');
		foreach ($protections as $prot) {
			// statuts
			foreach ($abr_statuts as $abr => $exp) {
				if (preg_match($exp, $prot)) {
					if (! array_key_exists($prot, $donnees['protection_statuts'])) {
						// récupération du statut à l'aide du service
						$url = sprintf($tpl_url_service_statuts, $abr);
						$statut = $this->conteneur->getRestClient()->consulter($url);
						$statut = json_decode($statut, true);
						$description = '';
						$intitule = '';
						if (! empty($statut['resultat'])) {
							if (! empty($statut['resultat']['intitule'])) {
								$intitule = $statut['resultat']['intitule'];
								$description .= $intitule;
							}
							if ($description != '') $description .= '. ';
							if (! empty($statut['resultat']['description'])) {
								$description .= $statut['resultat']['description'];
							}
						}
						$donnees['protection_statuts'][$prot] = array(
							'abr' =>  $prot . " (" . $abr . ")",
							'desc' => $description,
							'intitule' => $intitule
						);
					}
				}
			}
			// textes
			foreach ($abr_textes as $abr => $exp) {
				if (preg_match($exp, $prot)) {
					if (! array_key_exists($prot, $donnees['protection_textes'])) {
						// récupération du texte à l'aide du service
						$url = sprintf($tpl_url_service_textes, $abr);
						$texte = $this->conteneur->getRestClient()->consulter($url);
						$texte = json_decode($texte, true);
						$description = '';
						if (! empty($texte['resultat'])) {
							if (! empty($texte['resultat']['intitule'])) {
								$description .= $texte['resultat']['intitule'];
							}
							if ($description != '') $description .= '. ';
							if (! empty($texte['resultat']['url'])) {
								$description .= '<a target="_blank" href="' . $texte['resultat']['url'] . '">' . $texte['resultat']['url'] . '</a>';
							}
						}
						$donnees['protection_textes'][$prot] = array(
							'abr' => $prot . " (" . $abr . ")",
							'desc' => $description
						);
					}
				}
			}
		}
		ksort($donnees['protection_statuts']);
		ksort($donnees['protection_textes']);

		$donnees['proteges'] = $this->proteges;

		// de A à Z
		$donnees['lettres'] = array();
		for ($i=65; $i<=90; $i++) {
			$donnees['lettres'][] = chr($i);
		}

		// mini correction pour extract qui ne tolere pas les variables avec des "-"
		// tout comme php en général
		$donnees['zone_geo'] = $donnees['zone-geo'];

		// possibilités de tailles de page
		$donnees['tailles_page'] = array(10, 20, 50, 100);
		$this->setSortie(self::RENDU_CORPS, $this->getVue('liste-taxons', $donnees));
	}
}