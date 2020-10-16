<?php

namespace Model\Router;

use Model\Core\De AS de;

class Router {

	public $controller = 'Index';
	public $action = 'index';
	public $param = '';
	public $namespace = SUBPASTA.'\\Controller\\Index\\Index';
	public $url;
	public $language = '';

	public $file_controller;

	/**
	 * Router constructor.
	 */
	public function __construct()
	{

		if(isset($_SERVER['REQUEST_URI']) and !empty($_SERVER['REQUEST_URI'])){

			$temp = [];

			$url = $this->parseURL($_SERVER['REQUEST_URI']);

			// Atualiza a URL
			$this->setUrl($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
			$controller = ucwords(strtolower($url[1] ?? ''));
			$action = strtolower($url[2] ?? '');
			$param = strtolower($url[3] ?? '');
			$pathSiteProjeto = DIR . DS . $this->namespace;
			
			$this->file_controller = $pathSiteProjeto;
			
			// If controller !== ''
			if(!empty($controller)) {
				$controladores = scandir('../'.SUBPASTA.'/Controller');
				foreach($controladores as $controlador_str){
					similar_text($controlador_str, $controller, $math);
					
					if($math >= 85){
						$controller = $controlador_str;
					}

				}
				$this->setValues($controller);
				$fileController =  str_replace('\\', '/',DIR . DS . $this->namespace . '.php');
				
				// If not pathSiteProjeto Controller || not exists action/method = Erro404
				if(!class_exists($this->namespace) OR !is_file($fileController)){
					$this->set404();
				}
			}

			// If exists action
			if(isset($action) and !empty($action)){
				$this->setAction($action);
			}
			// If exists param
			if(isset($param) and !empty($param)){
				$this->setParam($param);
			}
		}
	}

	public function set404(){
		$this->setValues('Erro404');
		$this->setAction('index');
	}

	private function setValues($value){
		$this->setController($value);
		$this->setFileController($value);
		$this->setNamespace($value.'\\'.$value);
	}

	private function parseURL($url){

		$array = explode('/', $url);
		$temp = array();

		foreach ($array as $key => $value) {

			$temp[$key] = preg_replace('/\?.*$|\!.*$|#.*$|(?# \'.*$|)\@.*$|\$.*$|&.*$|\*.*$|\+.*$|\..*$/', '', $value);
		}

		return $temp;
	}


	/**
	 * @return mixed
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param mixed $namespace
	 * @return Router
	 */
	public function setNamespace($namespace)
	{  
		$pathSiteProjeto = str_replace('/', '\\',SUBPASTA . DS);
		$this->namespace = $pathSiteProjeto.'Controller\\'.$namespace;
		return $this;
	}
	/**
	 * @return mixed
	 */
	public function getFileController()
	{
		return $this->file_controller;
	}

	/**
	 * @param mixed $file_controller
	 * @return Router
	 */
	public function setFileController($file_controller)
	{
		$this->file_controller = CONTROLLER . DS . $file_controller . DS . $file_controller . '.php';
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param mixed $param
	 * @return Router
	 */
	public function setParam($param)
	{
		$this->param = $param;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getParam()
	{
		return $this->param;
	}

	/**
	 * @param mixed $action
	 * @return Router
	 */
	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @param mixed $controller
	 */
	public function setController($controller): void
	{
		$this->controller = $controller;
	}

}