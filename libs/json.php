<?php
/**
 * Class JSON
 *
 * Description:
 * This class is to create JSON (Javascript
 * Object Notation) objects. Originally I wrote it to
 * create configurations for some javascript libraries
 * I use however if you would like to see some extra
 * functionality in this please let me know.
 * 
 * Background:
 * The problem with the Prado TJavascript::encode() method
 * is that if you pass a function as a value Prado will
 * enclose it with quotes and your Javascript will think
 * its a string. Someone from the Prado forums showed me
 * an example around this however I think it overcomplicated
 * the situation and all I was trying to do was not have my
 * function enclosed in quotes. Nonetheless from this experience
 * I decided to write my own JSON class because I couldn't
 * find anything out there that met my needs and here it is.
 * 
 * Examples:
 * Example 1 - Usage:
 * <code>
 * $json=new JSON();
 * $json->add("name", "some_name");
 * $json->add("close_effect", "Effects.Close", JSON::TYPE_JAVASCRIPT);
 * $json->add("array_test", array(1, 2, 3));
 * echo $json->encode(); // This would output {"name" : "some_name", "close_func" : Effects.Close, "array_test" : [1, 2, 3]}
 * </code>
 * 
 * Example 2 - Util Example:
 * <code>
 * // Array to JSON
 * $array=array();
 * $array['book']='Some book';
 * $array['price']=10.99;
 * $json=JSONUtil::arrayToJSON($array);
 * echo $json->encode(); // This would output {"book" : "Some book", "price" : 10.99}
 * 
 * // JSON to Array (basically the opposite of above)
 * $array2=JSONUtil::jsonToArray($json);
 * print_r($array2);
 * </code>
 *
 * Requirements:
 * - PHP 5
 * 
 * @author Richard Sumilang <richard@richard-sumilang.com>
 * @version $Revision: $  $Date: $
 * @copyright Richard Sumilang  <richard@richard-sumilang.com>
 */
class JSON{
	
	const TYPE_AUTO=0;
	const TYPE_BOOLEAN=1;
	const TYPE_NUMERIC=2;
	const TYPE_STRING=3;
	const TYPE_ARRAY=4;
	const TYPE_JAVASCRIPT=5;
	
	
	private $items=array();
	
	
	/**
	 * Adds an item to the list to be encoded
	 * @param string $property
	 * @param mixed $value
	 * @param integer $type Use available type constants
	 */
	public function add($property, $value, $type=0){
		$this->items[]=new JSONItem($property, $value, $type);
	}
	
	
	/**
	 * Returns Item at index specified
	 * @param integer $i
	 * @return JSONItem|null
	 */
	public function itemAt($i){
		if(isset($this->items[$i]))
			return $this->items[$i];
		return null;
	}
	
	
	/**
	 * Removes an item given by the index
	 * @param integer $i
	 */
	public function removeAt($i){
		if(isset($this->items[$i]))
			unset($this->items[$i]);
	}
	
	
	/**
	 * Finds the item you passed in the collection
	 * of items, removes it and returns the index
	 * it was in
	 * @param JSONItem $item
	 * @return integer Index 
	 */
	public function remove(JSONItem $item){
		if(is_numeric(($i=array_search($item, $this->items)))){
			unset($this->items[$i]);
			return $i;
		}
		return null;
	}
	
	/**
	 * Inserts an item at the specific index
	 * @param integer $i
	 * @param JSONItem $item
	 */
	public function insertAt($i, JSONItem $item){
		$this->items[$i]=$item;
	}
	
	
	/**
	 * @return integer The number of items in system
	 */
	public function getCount(){
		return count($this->items);
	}
	
	/**
	 * Encodes current object to be used in javascript
	 * @return string
	 */
	public function &encode(){
		$str='{';
		for ($i=0; $this->getCount() > $i; $i++){
			$item=$this->itemAt($i);
			$str.="'".$item->getProperty()."' : ";
			
			if(self::TYPE_AUTO==$item->getType()){
				switch (gettype($item->getValue())){
					case "boolean":
						$type=self::TYPE_BOOLEAN;
					break;
					
					case "integer":
						$type=self::TYPE_NUMERIC;
					break;
					
					case "double":
						$type=self::TYPE_NUMERIC;
					break;
					
					case "string":
						$type=self::TYPE_STRING;
					break;
					
					case "array":
						$type=self::TYPE_ARRAY;
					break;
					
					default:
						$type=self::TYPE_STRING;
					break;
				}
			}else
				$type=$item->getType();
			
			switch ($type){
				
				case self::TYPE_BOOLEAN:
					$str.=(($item->getValue()) ? 'true' : 'false').', ';
				break;
				
				case self::TYPE_NUMERIC:
					$str.=$item->getValue().", ";
				break;
				
				case self::TYPE_STRING:
					$str.="'".$item->getValue()."', ";
				break;
				
				case self::TYPE_ARRAY:
					$str.="[";
					$itemArr=$item->getValue();
					foreach ($itemArr as $itemVal){
						switch (gettype($itemVal)){
							case "boolean":
								$str.=(($itemVal) ? 'true' : 'false').', ';
							break;
							
							case "integer":
								$str.=$itemVal.", ";
							break;
							
							case "double":
								$str.=$itemVal.", ";
							break;
							
							case "string":
								$str.="'".$itemVal."', ";
							break;
							
							default:
								$str.="'".$itemVal."', ";
							break;
						}
					}
					$str=substr($str, 0, strlen($str)-2);
					$str.="], ";
				break;
				
				case self::TYPE_JAVASCRIPT:
					$str.=$item->getValue().", ";
				break;
				
			}
		}
		$str=substr($str, 0, strlen($str)-2);
		$str.='}';
		return $str;
	}
		
}


?>