<?php

/**
 * Controller por defecto si no se usa el routes
 * 
 */
class IndexController extends AppController
{

    public function index(){
        
    	$config = Config::read("config");
        $this->config = $config;
    	if (!$config['application']['production']) {
    		$this->configurer = new DatabaseConfigurer();
	    	$this->db_configuracion = $this->configurer->getDbConfig();
	    	if (Input::haspost("development","production")) {
	    		if($this->configurer->escribir($_POST)){
	    			Flash::valid("Cambios Realizados con éxito!");
	    			Router::toAction("");
	    		}
	    	}
    	}
    }
    public function control_usuario($db = "development"){
    	$o = new ControlDeUsuarios($db);
    	try {
    		if($o->crear($db)){
    			Flash::valid("Tablas Creadas! <br> Nombre de Usuario : admin <br> Contraseña: admin");
                Router::redirect("usuario/login");
	    	}else{
	    		Flash::valid("Error Creando Tabla");
	    	}
    	} catch (Exception $e) {
    		Flash::error($e->getMessage());
    	}
    }

}
