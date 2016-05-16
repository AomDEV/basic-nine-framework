<?php
/***********************************************************/
# @ NINE FRAMEWORK Version 1.0.0; Example Project Release!
# @ Script and System by @Aom Siriwat
# Full Version is coming soon!
/************************************************************/

class n_database{

	public $db_name=null;
	public $db_host=null;
	public $db_user=null;
	private $db_pass=null;
	public $db_class=null;

	/** settings database at start **/
	public function __construct($db_user,$db_pass,$db_host,$db_name){ //Settings Database
		require("modules/database/db.pdo.php");
		$this->db_name= $db_name;
		$this->db_host= $db_host;
		$this->db_user= $db_user;
		$this->db_pass= $db_pass;
		$this->db_class= new database($db_user,$db_pass,$db_host,$db_name) or die("Failed to connect!");
	}

	/** view all database name **/
	public function view_all_db($with_table=false){
		$rst_db=$this->db_class->getRows("SHOW DATABASES;",array());
		$tables = array("n_head"=>array("Database List"),"n_body"=>"");
		foreach($rst_db as $link=>$row){
			$tables["n_body"][] = array($row["Database"]);
		}

		if($with_table==true){
			return n_render::add_to_table($tables);
		} else{
			return $tables["n_body"];
		}
	}

	/** view all table name **/
	public function view_all_tbl($with_table=false,$db_name){
		$rst_db=$this->db_class->getRows("SHOW TABLES FROM ".htmlspecialchars($db_name).";",array());
		$tables = array("n_head"=>array("Tables List"),"n_body"=>"");
		foreach($rst_db as $link=>$row){
			$tables["n_body"][] = array($row["Tables_in_".$db_name]);
		}

		if($with_table==true){
			return n_render::add_to_table($tables);
		} else{
			return $tables["n_body"];
		}
	}

	/** view all column name **/
	public function view_all_clmn($with_table=false,$tbl_name){
		$rst_db=$this->db_class->getRows("SHOW COLUMNS FROM ".htmlspecialchars($tbl_name).";",array());
		$tables = array("n_head"=>array("Column List"),"n_body"=>"");
		foreach($rst_db as $link=>$row){
			$tables["n_body"][] = array($row["Field"]);
		}

		if($with_table==true){
			return n_render::add_to_table($tables);
		} else{
			return $tables["n_body"];
		}
	}

	/** view all data in table **/
	public function view_all_data($with_table=false,$table_name){
		$rst_db=$this->db_class->getRows("SELECT * FROM ".$table_name.";",array());
		$getColumn = $this->view_all_clmn(false,$table_name);
		$arrayColumn = array();
		$rowColumn = array();
		foreach($getColumn as $link=>$row){array_push($arrayColumn,$row[0]);}
		$tables = array("n_head"=>$arrayColumn,"n_body"=>"");
		foreach($rst_db as $link=>$row){
			for($c=0;$c<3;$c++){array_push($rowColumn,$row[$arrayColumn[$c]]);}
			$tables["n_body"][] = $rowColumn;
			$rowColumn=array();
		}

		if($with_table==true){
			return n_render::add_to_table($tables);
		} else{
			return $rst_db;
		}
	}

	/** update single data **/
	public function sql_single_update_data($table_name,$set_column,$new_value,$where_column,$where_value){
		return $this->db_class->updateRow("UPDATE ".$table_name." SET ".$set_column."=? WHERE ".$where_column."=?;",array($new_value,$where_value));
	}

}

class n_auth{
 //Soon!
}

class n_render{
	/** import special file **/
	public static function import_file(){
		include("modules/define/define.html.php");
		return true;
	}

	/** render array to table **/
	public static function add_to_table($array=array()){
		if(isset($array["n_head"]) and isset($array["n_body"])){
			print _TABLE_START." class='' border=1"._TAG_CLOSE;
			print _TR_START._TAG_CLOSE;
			for($h=0;$h<count($array["n_head"]);$h++){
				print(_TH_START._TAG_CLOSE.$array["n_head"][$h]."</th>");
			}
			print _TR_END._TR_START._TAG_CLOSE;
			for($b=0;$b<count($array["n_body"]);$b++){
				if(count($array["n_body"])>0){
					print _TR_START._TAG_CLOSE;
					for($bs=0;$bs<count($array["n_body"][$b]);$bs++){
						print(_TD_START._TAG_CLOSE.$array["n_body"][$b][$bs]._TD_END);
					}
					print _TR_END;
				} else{
					print(_TD_START._TAG_CLOSE.$array["n_body"][$b][0]._TD_END);
				}
			}
			print _TR_END;
			print _TABLE_END;
		} else{
			return false;
		}
	}

	/** check function is enable **/
	public static function checkFunction($fnc_name="phpinfo"){
		$disabled = explode(',', ini_get('disable_functions'));
		return !in_array($fnc_name, $disabled);
	}

	/** iframe website **/
	public static function iframe($ur){
		return _IFRAME_START." src='".$url."'"._TAG_CLOSE._IFRAME_END;
	}

	/** get all request to make page request **/
	public static function setup_request($request){
		if(isset($request["page"])){
			$page = str_replace("../","",$request["page"]);
			if(file_exists("modules/page/".$page.".php")){
				$file = "modules/page/".$page.".php";
				if(self::checkFunction('file_get_contents')==true and self::checkFunction('eval')==true){
					$source = file_get_contents($file);
					echo eval("?>".$source);
				} else{
					include($file);
				}
			} else{
				return false;
			}
		} else{
			return false;
		}
	}
}
?>
