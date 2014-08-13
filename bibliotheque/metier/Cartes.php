<?php
// declare(encoding='UTF-8');
/**
 * Classe gérant les images.
 *
 * @category	PHP 5.2
 * @package		eflore-consultation
 * @author		Jean-Pascal MILCENT <jpm@tela-botanica.org>
 * @author		Delphine CAUQUIL <delphine@tela-botanica.org>
 * @copyright	2011 Tela-Botanica
 * @license		http://www.gnu.org/licenses/gpl.html Licence GNU-GPL-v3
 * @license		http://www.cecill.info/licences/Licence_CeCILL_V2-fr.txt Licence CECILL-v2
 * @version		$Id$
 */
class Cartes extends Eflore {

	private $id;
	private $largeur;

	public function setId($id) {
		$this->id = $id;
	}

	public function setLargeur($largeur) {
		$this->largeur = $largeur;
	}
	
	public function setInfoNom($nom) {
		$this->nom = $nom;
	}
	
	public function setInfoReferentiel($referentiel) {
		$this->referentiel = $referentiel;
	}

	public function getUrlDataSvg() {
		$tpl = Config::get('carteTpl');
		$params = array('id' => $this->id, 'largeur' => $this->largeur, 'mime-type' => 'image/svg+xml');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}

	public function getUrlPng() {
		$tpl = Config::get('carteTpl');
		$params = array('id' => $this->id, 'largeur' => $this->largeur, 'mime-type' => 'image/png');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlMap() {
		$tpl = Config::get('efloreCarteTpl');
		$params = array('num_nom' => $this->nom->get('id'), 'num_tax' => $this->nom->get('num_taxonomique') ,
				'nom_sci' => $this->nom->get('nom_sci'), 'auteur' => $this->nom->get('auteur') ,
				'largeur' => $this->largeur, 'mime_type' => 'text/html');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlMapSvg() {
		$tpl = Config::get('carteMoissonnageTpl');
		$params = array('num_taxon' => $this->nom, 'referentiel' => $this->referentiel,
			'largeur' => $this->largeur, 'mime_type' => 'text/html', 'methode' => 'afficher');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlMapPng() {
		$tpl = Config::get('carteMoissonnageTpl');
		$params = array('num_taxon' => $this->nom, 'referentiel' => $this->referentiel,
				'largeur' => $this->largeur, 'mime_type' => 'image/png', 'methode' => 'afficher');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlTelechargementMapPng() {
		$tpl = Config::get('carteMoissonnageTpl');
		$params = array('num_taxon' => $this->nom, 'referentiel' => $this->referentiel,
					'largeur' => $this->largeur, 'mime_type' => 'image/png', 'methode' => 'telecharger');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlTelechargementMapHtml() {
		$tpl = Config::get('carteMoissonnageTpl');
		$params = array('num_taxon' => $this->nom, 'referentiel' => $this->referentiel,
			'largeur' => $this->largeur, 'mime_type' => 'text/html', 'methode' => 'telecharger');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlEflorePng() {
		$tpl = Config::get('efloreCarteTpl');
		$params = array('num_nom' => $this->nom->get('id'), 'num_tax' => $this->nom->get('num_taxonomique') ,
					'nom_sci' => $this->nom->get('nom_sci'), 'auteur' => $this->nom->get('auteur') ,
					'largeur' => $this->largeur, 'mime_type' => 'image/png');
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlFloreProbablePng() {
		$tpl = Config::get('carteFloreProbableTpl');
		$params = array('id' => $this->id, 
						'mime_type' => 'text/plain');
		$url = $this->formaterUrl($tpl, $params);
		$donnees = $this->chargerDonnees($url);
		$url_carte = null;
		// on demande l'url de la carte au web service
		// car la carte peut ne pas exister
		if(isset($donnees['binaire.href'])) {
			$url_carte = $donnees['binaire.href'];
		}
		return $url_carte;
	}

	public function getLegendeId() {
		$tpl = Config::get('legendeIdCarteTpl');
		$params = array('id' => $this->id);
		$url = $this->formaterUrl($tpl, $params);
		$donnees = $this->chargerDonnees($url);
		return $donnees;
	}
	
	public function getLegende() {
		$tpl = Config::get('legendeCarteTpl');
		$url = $this->formaterUrl($tpl, array());
		$donnees = $this->chargerDonnees($url);
		return $donnees;
	}

	// version statique de getUrlPng() ci-dessus
	static function getCarteUrlPng($projet, $id, $largeur) {
		return Eflore::s_formaterUrl(Config::get('carteTpl'),
									 array('projet' => $projet,
										   'id' => $id,
										   'largeur' => $largeur,
										   'mime-type' => 'image/png'));
	}

}
?>