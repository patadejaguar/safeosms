<?php
include_once ("core.config.inc.php");
include_once ("entidad.datos.php");
include_once ("core.error.inc.php");
include_once ("core.common.inc.php");
include_once ("core.db.inc.php");

class cCache {
	private $mCacheEnable	= true;
	function __construct(){
		if(!function_exists("memcache_connect") ){
			$this->mCacheEnable	= false;
		}
		$this->mCacheEnable		=	$this->setInSession($this->mCacheEnable);
	}
	function set($clave, $valor){
		$res	= false;
		$cnn	= $this->cnn();
		if($this->mCacheEnable == true){
			$res	= $cnn->set($clave, $valor);//, false, 10)
		}
		return $res;
	}
	function get($clave){
		$val	= null;
		$cnn	= $this->cnn();
		if($this->mCacheEnable == true){
			$val	= $cnn->get($clave);
			$val	= ($val === false) ? null : $val;
		}
		return $val;
	}
	function cnn(){
		$mem	= new Memcache;
		if(!$mem->connect('localhost', 11211)){
			$this->mCacheEnable	= false;
		} else {
			$this->mCacheEnable	= true;
		}
		return $mem;
	}
	function clean($id = false){
		$cnn	= $this->cnn();
		if($this->mCacheEnable == true){
			if($id == false){
			$cnn->flush();
			} else {
				$cnn->delete($id);
			}
		}		
	}
	function isReady(){ return $this->mCacheEnable;	}
	function setInSession($value = null){
		if(isset($_SESSION)){
			if(isset($_SESSION["memcache.enable"])){
				if($value == null){
					$value	= $_SESSION["memcache.enable"];
				}
			}
			$_SESSION["memcache.enable"]	= $value;
		}
		return $value;
	}
}


?>