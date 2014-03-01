<?php 
Load::lib("formularios");
class FormulariosController extends AppController{
	public function index(){
		
		$frm = new Formularios();
		$this->tablas = $frm->getTablas();

		if (Input::isAjax()) {
			View::select(null,'json');
			$definition = $frm->create_table_definition($_POST);
			if (isset($definition['mensaje'])) {
				$this->data = $definition;
				exit();
			}else{
				if ($frm->getDb()->factoria->create_table($definition[0],$definition[1])) {
					if(!Util::crearArchivoPhp("w+",APP_PATH."models/",$definition[0],$definition[2],"php")){
						$this->data = array("mensaje"=>"Tabla {$definition[0]} Creada con exito!","bol"=>true,
						"describe"=>$frm->getDb()->factoria->describe_table($definition[0]),"php"=>"Error Creando Clase $definition[0]");
					}else{
						$this->data = array("mensaje"=>"Tabla {$definition[0]} Creada con exito!","bol"=>true,
						"describe"=>$frm->getDb()->factoria->describe_table($definition[0]));
					}
				}else{
					$this->data = array("mensaje"=>"No se pudo crear la tabla {$definition[0]}","bol"=>false);
				}
			}
		}
	}
	public function describe($tabla){
		if (Input::isAjax()) {
			View::select(null,"json");
			$frm = new Formularios();
			if (!file_exists(APP_PATH."models".DIRECTORY_SEPARATOR.$tabla.".php")) {
				Util::crearArchivoPhp("w+",APP_PATH."models/",$tabla,$frm->getStringPhp(ucfirst($tabla)),"php");
			}
			$this->data = array("header"=>$frm->getDb()->factoria->describe_table($tabla),"contenido"=>Load::model($tabla)->find());
			
		}
	}
	public function crear_scaffold_controller($modelo){
		$m_modelo = ucfirst($modelo);
		$controller='
<?php
class '.$m_modelo.'Controller extends scaffoldController{
	public $model = "'.$modelo.'";
}
?>';
		$model='
<?php
class '.$m_modelo.' extends ActiveRecord{
	
}
?>		';

		if(!Util::crearArchivoPhp("w+",APP_PATH."controllers/",$modelo."_controller",$controller,"php")){
			Flash::error("Error creando el controllador de $modelo");
			View::select("index");
			return false;
		}
		if(!Util::crearArchivoPhp("w+",APP_PATH."models/",$modelo,$model,"php")){
			Flash::error("Error creando el modelo de $modelo");
			View::select("index");
			return false;
		}
		View::select(null,"json");
		$this->data = true;
	}

}
 ?>