<?php 
class Formularios{
	private $db;

	public function __construct(){
		$this->db = New Db();
		$this->db->factoria = $this->db->factory();
	}
	public function getDb(){
		return $this->db;
	}
	public function create_table_definition($data_por_post){
		
		$nombre_modelo = $data_por_post['Nombre_modelo'];

		array_shift($data_por_post);
		
		if ($this->db->factoria->table_exists($nombre_modelo)) {
			return array("mensaje"=>"La tabla $nombre_modelo ya existe en la base de datos escogida","bol"=>false);
		}
		$table_Def = array("id"=>array("not_null"=>1,"primary"=>1,"auto"=>1,"type"=>"int"));
		
		$numeros = $this->obtenerNumerosDeFilas($data_por_post);
		
		for ($i=0; $i <count($numeros) ; $i++) { 
			$num = $numeros[$i];
			$nombre = $data_por_post[$num.'_nombre_campo'];

			$table_Def[$nombre] = array();

			if ($data_por_post[$num.'_type']=='fecha') {
				$table_Def[$nombre]['type'] = $data_por_post[$num.'_size'];
				$table_Def[$nombre]['size'] = '';
				//exit(Util::pre($table_Def));
			}
			if ($data_por_post[$num.'_type']=='varchar') {
				$table_Def[$nombre]['type'] = 'varchar';
				$table_Def[$nombre]['size'] = $data_por_post[$num.'_size'];
			}
			if ($data_por_post[$num.'_type']=='boolean') {
				$table_Def[$nombre]['type'] = 'boolean';
				$table_Def[$nombre]['size'] = '';
			}
			if ($data_por_post[$num.'_type']=='text') {
				$table_Def[$nombre]['type'] = 'text';
				$table_Def[$nombre]['size'] = '';
			}
			if ($data_por_post[$num.'_type']=='int') {
				if ($data_por_post[$num.'_size'] > 8) {
					$table_Def[$nombre]['type'] = 'bigint';
				}else{
					$table_Def[$nombre]['type'] = 'int';
				}
				$table_Def[$nombre]['size'] = '';
			}
			if (!isset($data_por_post[$num.'_not_null'])) {
				$table_Def[$nombre]['not_null'] = 1;
			}
			if ($data_por_post[$num.'_constrain_db'] != 'normal') {
				$table_Def[$nombre]['unique_index'] = 1;
			}
			if (!empty($data_por_post[$num.'_comentario_campo_db'])) {
				$table_Def[$nombre]['comment'] = $data_por_post[$num.'_comentario_campo_db'];
			}
			

		}
		
		return array($nombre_modelo,$table_Def,$this->getStringPhp(ucfirst($nombre_modelo)));
		
	}
	public function getStringPhp($class_name){
		return '
<?php
class '.$class_name.' extends ActiveRecord{
	
}
?>'
;
	}
	public function getNumeroFilaYKey($string){
		$v = explode("_", $string);
		$primer = array_shift($v);
		$lo_que_queda = implode("_",$v);
		return array($primer,$lo_que_queda);
	}
	public function extraerClavesValorDe($num,$array){
		$retorno = array();
		foreach ($array as $key => $value) {
			$k = $this->getNumeroFilaYKey($key);
			if ($k[0]==$num) {
				$retorno[$k[1]] = $value;
			}
		}
		return $retorno;
	}
	public function obtenerNumerosDeFilas($data){
		$numeros=array();
		foreach ($data as $key => $value) {
			$n = $this->getNumeroFilaYKey($key);
			if (!in_array($n[0], $numeros)) {
				$numeros[]=$n[0];
			}
		}
		return $numeros;

	}
	public function getTablas(){
		$tables = $this->db->factoria->list_tables();
		
		for ($i=0; $i <count($tables) ; $i++) { 
			foreach ($tables[$i] as $key => $value) {
				if ($key == "table") {
					$t[$i]=$value;
				}
			}
		}
		if (!empty($t)) {
			# code...
			array_unshift($t,'Tablas');
			return $t;
		}
		
	}
}
?>