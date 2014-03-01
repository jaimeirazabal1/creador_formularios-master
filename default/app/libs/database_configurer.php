<?php 
/**
 * clase para configurar el archivo databases.ini
 * desde interfaz grafica.
 * Esta clase se puede usar para escribir otro .ini
 * simplemente especificandole el path y el array de datos
 */
class DatabaseConfigurer extends Db{

	private $config;
	private $ini_path;
	private $ini_string;

	public function __construct(){

	}
	/**
	 * obtiene las configuraciones de las bases de datos
	 * expresadas en databases.ini
	 * @return array
	 */
	public function getDbConfig(){
		if (!$this->config) {
			$this->setDbConfig();
		}
		return $this->config;
	}
	/**
	 * lee la configuracion escrita en databases.ini
	 */
	public function setDbConfig(){

		$this->config = Config::read("databases");
	}
	/**
	 * setea el path del archivo databases.ini
	 * @param [string] $path
	 */
	public function setIniPath($path){
		$this->ini_path = $path;
	}
	/**
	 * obtiene el path del archivo punto ini que configura la conexion a la db
	 * @return [string]
	 */
	public function getIniPath(){
		if (!$this->ini_path) {
			if (file_exists(APP_PATH."config".DIRECTORY_SEPARATOR."databases.ini")) {
				$this->setIniPath(APP_PATH."config".DIRECTORY_SEPARATOR."databases.ini");
			}
		}
		return $this->ini_path;
	}
	/**
	 * obtiene el string que escribira en el .ini
	 * @return [type]
	 */
	public function getIni_string(){
		return $this->ini_string;
	}
	/**
	 * escribe el string del .ini con los parametro del array $this->config
	 * @param  array  $datos
	 * @param  string $option
	 * @return [type]
	 */
	public function escribir($datos = array(),$option = "w+"){
		$this->ini_string = '';
		if (!empty($datos)) {
			foreach ($datos as $key => $value) {
				$this->ini_string.="[".$key."]\n";
				foreach ($value as $k => $v) {
					$this->ini_string.=$k."=".$v."\n";
				}
				$this->ini_string.="\n\n";
			}
			if(!$this->escribirPuntoIni($option)):return false;endif;

			return true;
		}
	}
	/**
	 * termina de escribir el .ini
	 * @param  [string] $option option de la function fopen
	 * @return [type]
	 */
	private function escribirPuntoIni($option){
		if (is_writable($this->getIniPath())) {
			$fp = fopen($this->getIniPath(),$option);
			fwrite($fp, $this->ini_string);
			fclose($fp);
			return true;
		}else{
			Flash::error("No se pudo escribir en ".$this->getIniPath());
			return false;
		}
	}
}
 
