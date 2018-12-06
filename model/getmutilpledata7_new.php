
<?php
/*
 *The Chain of Responsibility (CoR) design pattern is used when you need a request handled by the most appropriate object for the 
 *request. You don’t need to worry about which object handles the request or even if they’ll handle it the same all the time.
 */

include('connection.php');
include('dao.php');


/**
 * The default chaining behavior can be implemented inside a base handler class.
 */
class Handler{
	//next transaction
	private $next_handler;
	private $lever;
	private $result;
	private $row;
	private $PDO;
	private $dao;
	private $initInfo;	

	public function setDao ($dao){
		$this->dao = $dao;
	}
	public function getDao(){
		return $this->dao;
	}


	public function getPdo(){
        	return	$this->PDO;
	}
	public function setPdo($PDO){
        	$this->PDO = $PDO;
    	}
	
 	public function getInitInfo(){
        	return $this->initInfo;
    	}
	public function setInitInfo($initInfo){
        	$this->initInfo = $initInfo;
    	}
    	public function setRow($row){
    		$this->row = $row;
    	}
    	public function getRow(){
    		return $this->row;
    	}
	public function getNextHandler(){
		return $this->next_handler;
	}
   

    	//set who is the next one
    	public function setNext(Handler $handler){
        	$this->next_handler = $handler;
        	//get the uncompleted result and work on it, not sure how to use it here and update it later in each class
        	$this->next_handler->setDao($this->getDao());
        	$this->next_handler->setPdo($this->getPdo());
	
    	}
	/*check if the transaction complete , if complete go to the next
     	 *in this case, if first step has result, we will go to next levels one by one 
     	 *and excute the queries without making decision.
    	 */
    	final public function handlerMessage(){
		//do query no matter what, result is associate array and put in key=>value after query
		$result = $this->doQuery();
		//For the fist step, if doquery return is one
        	if( $result == 1){
            		return;
        	}
		//if still have next leveler. just go
		if($this->next_handler != null){
			//if select check existing we should skip the next step		   			          
			$this->next_handler->handlerMessage();
        	}
        
    	}
    
}



/**
 * All Concrete Handlers handle a request and pass it to the next handler in the chain.
 */
class getCdna extends Handler{
	

	protected function doQuery(){
		//we could use function construct to initial gene if it only used once
		$gene = $this->getInitInfo();
		$pdo = $this->getPdo();
		$myresult = array();
		$query = "select cdna_hgvs,exons_introns_affected,ptn_hgvs,hgmd_id,hgmd_type,Description,phenotype,var_id from VARIANT where cdna_hgvs = '".$gene."' or ptn_hgvs = '".$gene."' or gene_notation = '".$gene."' or Description = '".$gene."' or synonyms like '%".$gene."%'";
		//$query = "select cdna_hgvs,exons_introns_affected,ptn_hgvs,hgmd_id,hgmd_type,Description,phenotype,var_id from VARIANT limit 1";
		$stmt = $pdo->prepare($query);
	    	try {
	    		$stmt->execute();
		} catch (PDOException $e) {
	        	echo $e->getMessage();
		}
		$rows = $stmt->fetchAll();
		$num_rows = count($rows);
		if($num_rows == 0){			
			//echo $num_rows;
			return  1;
		}else{
			foreach($rows as $row) {
				$myresult["Nucleotide_change"]=$row[0];
				$myresult["Exon_Intron"]=$row[1];
				$myresult["Amino_acid_change"]=$row[2];
				$myresult["HGMD_accession"]=$row[3];
				$myresult["Mutation_Type"]=$row[4];
				$myresult["Mutation_Description"]=$row[5];
				$myresult["references"]="";
               			$myresult["clinInfor"]="not available";
               			$myresult["clinRec"] = "not available";
                		$myresult["Likely_phenotype"]="not available";
               			$myresult["clinPic"]="not available";
				$this->getDao()->updateByAR($myresult);
                                //set row for next class to use it
				$this->getNextHandler()->setRow($row);
			}
		}
	}
}



//If it has result on the first step, we will go to following steps one by one
class getPhenotype extends Handler{
	
	protected function doQuery(){
		$result = array();
		$pdo = $this->getPdo();		
		$row = $this->getRow();		 
		//set row for next class to use it
		$this->getNextHandler()->setRow($row);
		//??? don't get data yet
		$query="select  c.Phenotype from VARIANT d  inner join PHENOTYPE c on c.Pheno_ID=d.phenotype where d.phenotype=".$row[6]." limit 1";
	  	$stmt = $pdo->prepare($query);
	  	$rows = $stmt->fetchAll();
	  	try {
	    	$stmt->execute();
		} catch (PDOException $e) {
	        echo $e->getMessage();
		}
	  	
		$num_rows = count($rows); 
		if($num_rows == 0){
			
		}
		else{
			foreach($rows as $phenotyperow) {
				if(!$this->getDao()->getByKey("Likely_phenotype")){
					$result["Likely_phenotype"] = $phenotyperow[0];      
					$this->getDao()->updateByAR($result);
				}else{
					//$result["Likely_phenotype"]=$this->getDao()->getByKey("Likely_phenotype")."##".$phenotyperow[0];
					$result["Likely_phenotype"] = $this->getDao()->getByKey("Likely_phenotype")."##".$phenotyperow[0];
					$this->getDao()->updateByAR($result);
				}

			}
		}
	}
}



class LitURL extends Handler{
	
	
	protected function doQuery(){
		$result = array();
		$row = $this->getRow();	
		$pdo = $this->getPdo();
		$this->getNextHandler()->setRow($row);
		$query = "select LitCitation,LitURL from LITERATURE_REFS a left join  VARIANT_LIT b on a.LitID=b.`LitID` where b.`VarID`='".$row[7]."'";
  		 //$value = $this->getDao()->getByKey("clinInfor"); echo $value;
		$stmt = $pdo->prepare($query);
		try {
		    $stmt->execute();
		} catch (PDOException $e) {
		    echo $e->getMessage();
		}
		$rows = $stmt->fetchAll();
		$num_rows = count($rows);
		if($num_rows == 0){
		
		}else{//second level
			foreach($rows as $refrow) {
				//if no references exist
				if(!$this->getDao()->getByKey("references")){
					$result["references"] = $refrow[0]."!!".$refrow[1];
					$result["references"] = utf8_encode($result["references"]);
					$this->getDao()->updateByAR($result);
				}else{

					$result["references"] = $this->getDao()->getByKey("references")."##".$refrow[0]."!!".$refrow[1];
					$result["references"] = utf8_encode($result["references"]);
					$this->getDao()->updateByAR($result);
				}
			}

		}
	}
}
class getPicture extends Handler{
	
	
	protected function doQuery(){
		$pdo = $this->getPdo();         
                $row = $this->getRow();               
		$this->getNextHandler()->setRow($row); 
		$query = "select a.clinical_picture from MAJOR_PHENOTYPE a left join PHENO_TO_MAJOR_PHENO b on a.`major_phenotype_id`=b.`major_pheno_id` where b.`pheno_id`=".$row[6]."  limit 1";
  		$stmt = $pdo->prepare($query);
        	try {
    			 $stmt->execute();
		} catch (PDOException $e) {
     			echo $e->getMessage();
		}

        	$rows = $stmt->fetchAll();
        	$num_rows = count($rows);  
		if($num_rows == 0){
		}else{
			foreach($rows as $clinPicrow) {
		  		$result["clinPic"] = preg_replace('/[[:^print:]]/', '', $clinPicrow[0]);
				$this->getDao()->updateByAR($result);
			}
		}
	}
}

class getRecommendations extends Handler{
	
	 
	protected function doQuery(){
		$pdo = $this->getPdo();         
                $row = $this->getRow();                
		$this->getNextHandler()->setRow($row); 
		$query = "select a.recommendations from MAJOR_PHENOTYPE a left join PHENO_TO_MAJOR_PHENO b on a.`major_phenotype_id`=b.`major_pheno_id` where b.`pheno_id`=".$row[6]." limit 1";

 		$stmt = $pdo->prepare($query);
        	try {
     			$stmt->execute();
		} catch (PDOException $e) {
     			echo $e->getMessage();
		}

        	$rows = $stmt->fetchAll();
        	$num_rows = count($rows);
		if($num_rows == 0){
		  	
		}else{
			foreach($rows as $clinRec ) {
				$result["clinRec"] = $clinRec[0];
		  		$this->getDao()->updateByAR($result);
			}
		}
	}
}

class getInformation extends Handler{
	
	protected function doQuery(){

		$pdo = $this->getPdo();         
                $row = $this->getRow();                
		$query = "SELECT \"Information is based on Mount Sinai's International Center for Fabry Disease (ICFD) curated data of \", CASE needs_over when '1'  then concat(\" over \",num_males,\" males and \",num_females,\" females from  unrelated families\")  when '0' then   concat(\"from \",num_males,\" males and \",num_females,\" females from  unrelated families\") end as word,\". For affected males, the median &alpha;-Gal A enzyme activity in plasma is \", plasma_enzyme_activity_median,\" nmol/hr/ml, the median &alpha;-Gal A enzyme activity in leukocytes is \",wbc_enzyme_activity_median ,\" nmol/hr/mg.\" from CLINICAL_INFO_Desnick where variant_id='".$row[7]."' and clin_info_source ='MSSM'";
		$stmt = $pdo->prepare($query);
	    	try {
	    		$stmt->execute();

		} catch (PDOException $e) {
	     		echo $e->getMessage();
	  	}

	    	$rows = $stmt->fetchAll();
	    	$num_rows = count($rows);  
	  	if($num_rows == 0){
	  		$value = "Symptoms have not been described in reported patients";
		 	$this->getDao()->setKeyValue("clinInfor", $value);
		}else{
			foreach($rows as $mySymptoms) {			
		  		$value = $mySymptoms[0].$mySymptoms[1].$mySymptoms[2].$mySymptoms[3].$mySymptoms[4].$mySymptoms[5];	
		 		$result["clinInfor"] = utf8_encode($value);
				$this->getDao()->updateByAR($result);
			}		
		}
	}
}  


//get database instance
$pdo = Db::getInstance();
//print_r($pdo);
//get dao from dao.php which generate DAO, in this case, it will be an array
$dao = runFactory::getFactory()->getRunDao();
$gene = isset($_POST["gene"]) ? $_POST["gene"] : "Q157X";//Q107L

//instance  total 13 classes
$getCdna = new getCdna();
$getPhenotype = new getPhenotype();
$LitURL = new LitURL();
$getPicture = new getPicture();
$getRecommendations = new getRecommendations();
$getInformation = new getInformation();


//assemble the chain
$getCdna->setDao($dao);
$getCdna->setPdo($pdo);
$getCdna->setInitInfo($gene);

$getCdna->setNext($getPhenotype);
$getPhenotype->setNext($LitURL);
$LitURL->setNext($getPicture);
$getPicture->setNext($getRecommendations);
$getRecommendations->setNext($getInformation);


//transfer request
$getCdna->handlerMessage();

$output = $dao->generateJson();


function unicode_hex_to_utf8($hexcode) {
  	$arr = array(hexdec(substr($hexcode[1], 0, 2)), hexdec(substr($hexcode[1], 2, 2)));
  	$dest = '';
  	foreach ($arr as $src) {
	    if ($src < 0) {
	      return false;
	    } elseif ( $src <= 0x007f) {
	      $dest .= chr($src);
	    } elseif ($src <= 0x07ff) {
	      $dest .= chr(0xc0 | ($src >> 6));
	      $dest .= chr(0x80 | ($src & 0x003f));
	    } elseif ($src == 0xFEFF) {
	      // nop -- zap the BOM
	    } elseif ($src >= 0xD800 && $src <= 0xDFFF) {
	      // found a surrogate
	      return false;
	    } elseif ($src <= 0xffff) {
	      $dest .= chr(0xe0 | ($src >> 12));
	      $dest .= chr(0x80 | (($src >> 6) & 0x003f));
	      $dest .= chr(0x80 | ($src & 0x003f));
	    } elseif ($src <= 0x10ffff) {
	      $dest .= chr(0xf0 | ($src >> 18));
	      $dest .= chr(0x80 | (($src >> 12) & 0x3f));
	      $dest .= chr(0x80 | (($src >> 6) & 0x3f));
	      $dest .= chr(0x80 | ($src & 0x3f));
	    } else {
	      // out of range
	      return false;
	    }
	}
  	return $dest;
}


echo mb_convert_encoding($output, "ISO-8859-9", "UTF-8");


?>

