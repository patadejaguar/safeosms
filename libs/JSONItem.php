<?php
/**
 * Class JSONItem
 *
 * The JSON object stores items as a JSONItem instance
 * 
 * @author Richard Sumilang <richard@richard-sumilang.com>
 * @version $Revision: $  $Date: $
 */
class JSONItem{
	
	private $property;
	private $value;
	private $type;
	
	/**
	 * Constructor
	 * @param string $property
	 * @param mixed $value
	 * @param integer $type
	 */
	public function __construct($property, $value, $type){
		$this->setProperty($property);
		$this->setValue($value);
		$this->setType($type);
	}
	
	
	/**
	 * @param string $property Name of property
	 */
	public function setProperty($property){
		$this->property=$property;
	}
	
	/**
	 * @return string Name of property
	 */
	public function getProperty(){
		return $this->property;
	}
	
	
	/**
	 * @param mixed $value Value
	 */
	public function setValue($value){
		$this->value=$value;
	}
	
	/**
	 * @return mixed Value
	 */
	public function getValue(){
		return $this->value;
	}
	
	
	/**
	 * @param integer $type Type
	 */
	public function setType($type){
		$this->type=$type;
	}
	
	/**
	 * @return integer Type
	 */
	public function getType(){
		return $this->type;
	}
	
	
}
?>