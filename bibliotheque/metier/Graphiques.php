<?php
// declare(encoding='UTF-8');
/**
 * Classe gérant les graphiques et leurs légendes.
 *
 * @category	PHP 5.2
 * @package		eflore-consultation
 * @author		Mathilde SALTHUN-LASSALLE <mathilde@tela-botanica.org>
 * @copyright	2011 Tela-Botanica
 * @license		http://www.gnu.org/licenses/gpl.html Licence GNU-GPL-v3
 * @license		http://www.cecill.info/licences/Licence_CeCILL_V2-fr.txt Licence CECILL-v2
 * @version		$Id$
 */
class Graphiques extends Eflore {

	private $bdnt;
	private $num_nom;
	private $type_graph;
	private $classe;
	private $code;
	// pour chaque code, [0] est le min (départ de recherche des valeurs)
	// et [1] est le max (fin)
	static $codes = array ("VEL" => array(1,9),
						   "VET" => array(1,9),
						   "VEHA" => array(1,9),
						   "VEC" => array(1,9),
						   "VER" => array(1,9),
						   "VETX" => array(1,9),
						   "VEN" => array(1,9),
						   "VEMO" => array(1,9),
						   "VEHE" => array(1,12),
						   "VES" => array(0,9) );

	public function setType_graph($tg) {
		$this->type_graph = $tg;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}
	
	public function setClasse($classe) {
		$this->classe = $classe;
	}
	
	public function setBdnt($bdnt){
		$this->bdnt = $bdnt;
	}
	
	public function setNum_nom($nn){
		$this->num_nom = $nn;
	}

	// TODO: array_map() // XXX: PHP-5.3
	static function _build_range() {
		$ret = array();
		foreach (self::$codes as $classe => $val) {
			foreach(range($val[0], $val[1]) as $i) {
				$ret[] = $classe . ':' . $i;
			}
		}
		return implode(',', $ret);
	}

	// TODO: array_map() // XXX: PHP-5.3
	static function _split_data($tab) {
		$ret = array();
		foreach ($tab as $k => $v) {
			list($new_k, $sub_k) = explode(':', $k);
			$ret[$new_k][$sub_k] = $v;
		}
		return $ret;
	}

	public function getLegendeGraphique() {
		$legende = array();
		// eg: VEL:1,VEL:2,VEL:3,...VER:9,VETX:1,...
		$ressources = self::_build_range();
		$url = Eflore::s_formaterUrl(Config::get('legendeGraphiqueTpl'),
									 $this->ajouterParametreParDefaut(array('params' => $ressources)),
									 FALSE);
		$data = $this->chargerDonnees($url);
		return self::_split_data($data);
	}
	
	public function getGraphique() {
		$url = $this->getUrlInformation();
		return $this->chargerDonnees($url);
	}
	
	public function getUrlGraphique() {
		$tpl = Config::get('graphiqueTpl');
		$params = array( 'bdnt' => $this->bdnt, 'num_nom' => $this->num_nom , 'type_graph' => $this->type_graph);
		$url = $this->formaterUrl($tpl, $params);
		return $url;
	}
	
}
?>
