<?php
/**
 * Class JSONUtil
 *
 * A collection of utility functions for manipulating JSON
 * objects.
 * 
 * @author Richard Sumilang <richard@richard-sumilang.com>
 * @version $Revision: $  $Date: $
 */
class JSONUtil{
	
	
	/**
	 * Pass in an array to make into a JSON object
	 * which will use the auto feature to for each
	 * value when encoding
	 * @param array $array
	 * @return JSON
	 */
	public static function &arrayToJSON($array){
		$json=new JSON();
		foreach ($array as $property=>$value)
			$json->add($property, $value);
		return $json;
	}
	
	/**
	 * Pass in a JSON object to return it back as
	 * an associative array
	 * @param JSON $json
	 */
	public static function &jsonToArray(JSON $json){
		$array=array();
		for ($i=0; $json->getCount() > $i; $i++){
			$item=$json->itemAt($i);
			$array[$item->getProperty()]=$item->getValue();
		}
		return $array;
	}
	
	
	
}
?>