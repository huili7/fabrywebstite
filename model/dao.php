<?php

//map the database to object
class setObj{
	private $objArray = array();

	function set($objArray){
		$this->objArray = $objArray;
	}
	function get(){
		return $this->objArray;
	}
}

//delare the interface that provides methods for accessing the data
interface exampleI{
	public function init(setObj $objArray);
	public function generateJson();
	public function setKeyValue($key,$value);
	public function getByKey($key);
	public function updateByAR($result);
}


//implement the above interface    
class runDao implements exampleI {//extends setObj{
	private $mysetObj;
	//private $result;
	public function __construct(){
	}
	public function init(setObj $obj){
		$this->mysetObj = $obj->get();
		$this->mysetObj["Nucleotide_change"]="not available";
		$this->mysetObj["Exon_Intron"]="not available";
		$this->mysetObj["Amino_acid_change"]="not available";
		$this->mysetObj["HGMD_accession"]="not available";
		$this->mysetObj["Mutation_Type"]="not available";
		$this->mysetObj["Likely_phenotype"]="not available";
		$this->mysetObj["Mutation_Description"]="not available";
		$this->mysetObj["references"]="null";
		$this->mysetObj["clinInfor"]="not available";
		$this->mysetObj["clinRec"] = "not available";
		$this->mysetObj["Likely_phenotype"]="not available";
		$this->mysetObj["clinPic"]="not available";
	}
	public function setKeyValue($key,$value){	 
		$this->mysetObj[$key]=$value;
	}
	public function getByKey($key){
		//if($key == "references") return "test";
		return $this->mysetObj[$key];
	}
	public function generateJson(){
		return json_encode($this->mysetObj);
	}
	public function updateByAR($result){
		foreach ($result as $key=>$value) {
			if($key != ""){
				$this->setKeyValue($key, $value);			  
			}
		}
	}


}



//factory pattern 
class runFactory
{
	private static $_instance;
 
	public function __construct()
	{
	}
 
	/**
	* Set the factory instance
	* @param App_DaoFactory $f
	*/
	public static function setFactory(runFactory $f)
	{
	self::$_instance = $f;
	}
	 
	/**
	* Get a factory instance. 
	* @return App_DaoFactory
	*/
	public static function getFactory()
	{
	if(!self::$_instance)
	self::$_instance = new self;
	 
	return self::$_instance;
	}
	 
	/**
	* Get a Question DAO
	* @return App_Dao_Question_Interface
	*/
	public function getRunDao()
	{
	return new runDao();
	}
}
/*
$test1 = runFactory::getFactory()->getRunDao();
$test1->init(new setObj());
$obj=array("cdna_hgvs"=>"c.1A>C", "exons_introns_affected"=>"Exon 1");
$test1->updateByAR($obj);
//$test1->setKeyValue("k1","v1");
//$result=$test1->getByKey("k1");
//echo $result;
print_r($test1->generateJson());

*/
?>

