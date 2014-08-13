<?php
/**
 * 
 * classe gérant des informations (descriptives, écologiques )
 * 
 * @author mathilde SALTHUN-LASSALLE <mathilde@tela-botanica.org>
 *
 */
class Informations extends Eflore {
	
	private $bdnt;
	private $num_nom;
	private $limite;
	private $depart;
	private $catminat;
	
	
	public function setDepart($depart){
		$this->depart = $depart;
	}
	
	public function setLimite($limite){
		$this->limite = $limite;
	}	
		
	public function setBdnt($bdnt){
		$this->bdnt = $bdnt;
	}
	
	public function setNum_nom($nn){
		$this->num_nom = $nn;
	}
	
	public function setCatminat($catminat){
		$this->catminat = $catminat;
	}
	
	public function getInformations() {	
		$url = $this->getUrlInformation();
		return $this->chargerDonnees($url);
	}
	

	
	public function getInformationsEcologie() {
		$url = $this->getUrlInformation();
		$url .= '?categorie=ecologie';
		return $this->chargerDonnees($url);
	}
	
	public function getInformationsDescription() {
		$url = $this->getUrlInformation();
		$url .= '?categorie=description';
		return $this->chargerDonnees($url);
	}
		
	public function getInformationsRelationCatminat() {
		$url = $this->getUrlInformationsRelationCatminat();
		return $this->chargerDonnees($url);
	}
	
	public function getInformationsMasqueCatminat() {
		$url = $this->getUrlInformationsMasqueCatminat();
		return $this->chargerDonnees($url);
	}
	
	
	
	public function getUrlInformation() {
		$tpl = Config::get('informationTpl');
		$params = array( 'bdnt' => $this->bdnt, 'num_nom' => $this->num_nom );
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlInformationsRelationCatminat() {
		$tpl = Config::get('informationsRelationCatminat');
		$params = array( 'bdnt' => $this->bdnt, 'num_nom' => $this->num_nom, 'limite' => $this->limite, 'depart' => $this->depart  );
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
	public function getUrlInformationsMasqueCatminat() {
		$tpl = Config::get('informationsCatminat');
		$params = array( 'catminat' => $this->catminat, 'limite' => $this->limite, 'depart' => $this->depart  );
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	

}