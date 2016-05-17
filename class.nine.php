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

	private $command=null;
	private $is_while=false;

	private $cmd_pattern = array("{{:only:}}","{{:where:}}");
	private $def_pattern = array("*","");

	private $selected_table = null;
	private $where_table = null;

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

	/** update sql **/
	public function updateRow($sql,$arg=array()){
		return $this->db_class->updateRow($sql,$arg);
	}

	/** insert sql **/
	public function insertRow($sql,$arg=array()){
		return $this->db_class->insertRow($sql,$arg);
	}

	/** fetch array **/
	public function fetchRow($sql,$arg=array()){
		return $this->db_class->getRow($sql,$arg);
	}

	/** while loop array **/
	public function fetchArray($sql,$arg=array()){
		return $this->db_class->getRows($sql,$arg);
	}

	/** delete sql **/
	public function deleteRow($sql,$arg=array()){
		return $this->db_class->deleteRow($sql,$arg);
	}

	/** count all row **/
	public function numRow($sql,$arg=array()){
		return $this->db_class->getNumber($sql,$arg);
	}

	public function init($is_while=true){
		$temp = $this->command;
		if($this->is_while==false){$is_while=false;}
		$i=0;
		while ($i < count($this->cmd_pattern)){
			$temp = eregi_replace ($this->cmd_pattern[$i] ,$this->def_pattern[$i] ,$temp);
			$i++;
		}
		$complete_sql = $temp;
		if($is_while==true){
			return $this->fetchArray($complete_sql,array());
		} else{
			return $this->fetchRow($complete_sql,array());
		}
	}
	public function get(){
		$temp = $this->command;
		if($this->is_while==false){$is_while=false;}
		$i=0;
		while ($i < count($this->cmd_pattern)){
			$temp = eregi_replace ($this->cmd_pattern[$i] ,$this->def_pattern[$i] ,$temp);
			$i++;
		}
		$complete_sql = $temp;
		return $complete_sql;
	}

	/** select * from ??? **/
	public function select($table=""){
		$cmd = "SELECT {{:only:}} FROM ".htmlspecialchars($table)." {{:where:}};";
		$this->selected_table = htmlspecialchars($table);
		$this->command = $cmd;
		return $this;
	}

	public function only($only=array()){
		if(isset($this->command)){
			$tbl_only = "";
			for($i=0;$i<count($only);$i++){
				if($i==(count($only)-1)){
					$tbl_only.=$only[$i];
				} else{
					$tbl_only.=$only[$i].",";
				}
			}
			$new_cmd = str_replace("{{:only:}}", $tbl_only, $this->command);
			$this->command = $new_cmd;
			return $this;
		} else{return "[ERROR] Not found command";}
	}

	public function find($column,$operate="=",$like){
		if(isset($this->command)){
			$this->is_while=false;
			$cmd = "WHERE ".$column." ".$operate." '".htmlspecialchars($like)."' ";
			$this->where_table = $cmd;
			$new_cmd = str_replace("{{:where:}}", $cmd, $this->command);
			return $this;
		} else{return "[ERROR] Not found command";}
	}

	public function delete($get_sql=false){
		$cmd = null;
		if(strlen($this->where_table)>10){
			if(isset($this->selected_table)){
				$cmd = "DELETE FROM ".htmlspecialchars($this->selected_table)." ".$this->where_table.";";
			} else{return "[ERROR] Not found table!";}
		} else{
			if(isset($this->selected_table)){
				$cmd = "DROP TABLE ".$this->selected_table.";";
			} else{return "[ERROR] Not found table!";}
		}

		if($get_sql==true){
			return $cmd;
		} else{
			return $this->deleteRow($cmd,array());
		}
	}

}

class n_auth{
	public static function check_version(){
		if(PHP_VERSION>5.3){
			return true;
		} else{
			return false;
		}
	}
}

class n_redirect{
	public static function to($url){
		return header( "location: ".$url );
	}
	public static function timerTo($time,$url){
		return header( "refresh:".$time."; url=".$url );
	}
	public static function back($url=true){
		if($url==true){
			return "javascript:history.go(-1);";
		} else{
 			return header("Location: javascript://history.go(-1)");
		}
	}
}

class n_http{
	public static function post($url){

	}
	public static function content($path){
		if(n_render::checkFunction("file_get_contents") and n_render::checkFunction("eval")){
			return (file_get_contents($path));
		} else{
			return false;
		}
	}
}

class n_detect{

	/** detect get parameter **/
	public static function get($request,$eval){
		if(preg_match("/(.*):(.*)/i", $request,$m1)){
			if(isset($_GET[$m1[1]]) and $_GET[$m1[1]]==$m1[2]){
				$eval($_GET[$m1[1]]);
			} else{return false;}
		} else{
			if(isset($_GET[$request])){
				$eval($_GET[$request]);
			} else{return false; }
		}
	}

	/** detect post parameter **/
	public static function post($request,$eval){
		if(preg_match("/(.*):(.*)/i", $request,$m1)){
			if(isset($_POST[$m1[1]]) and $_POST[$m1[1]]==$m1[2]){
				$eval($_POST[$m1[1]]);
			} else{return false;}
		} else{
			if(isset($_POST[$request])){
				$eval($_POST[$request]);
			} else{return false;}
		}
	}

	/** detect all (post,get) request **/
	public static function request($request,$eval){
		if(preg_match("/(.*):(.*)/i", $request,$m1)){
			if(isset($_REQUEST[$m1[1]]) and $_REQUEST[$m1[1]]==$m1[2]){
				$eval($_REQUEST[$m1[1]]);
			} else{return false;}
		} else{
			if(isset($_REQUEST[$request])){
				$eval($_REQUEST[$request]);
			} else{return false;}
		}
	}

	/** detect cookie parameter **/
	public static function cookie($request,$eval){
		if(isset($_COOKIE[$request])){
			$eval($_COOKIE[$request]);
		} else{return false;}
	}

	/** detect is this string is in url? **/
	public static function filter($string,$eval){
		if(preg_match("/has:(.*)/i",$string,$m)){
			if(preg_grep("/".$m[1]."/i",$_REQUEST)){
				$eval(true);
			} else{return false;}
		} else{
			if(in_array($string,$_REQUEST)){
				$eval(true);
			} else{return false;}
		}
	}
	
	/** detect path **/
	public static function path($path,$eval){
		$root_path = $_SERVER['PHP_SELF'];
		if($path==$root_path){
			$eval($root_path);
		} else{return false;}
	}

	/** detect all (post,get) request **/
	public static function session($request,$eval){
		if(preg_match("/(.*):(.*)/i", $request,$m1)){
			if(isset($_SESSION[$m1[1]]) and $_SESSION[$m1[1]]==$m1[2]){
				$eval($_SESSION[$m1[1]]);
			} else{return false;}
		} else{
			if(isset($_SESSION[$request])){
				$eval($_SESSION[$request]);
			} else{return false;}
		}
	}

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

	public static function draw_barcode($a=array()){
		include("modules/barcode/class.barcode.php");

		$text = isset($a["text"])?$a["text"]:"underfind";
		$filepath = isset($a["filepath"])?$a["filepath"]:"";
		$size = isset($a["size"])?$a["size"]:20;
		$orientation = isset($a["orientation"])?$a["orientation"]:"horizontal";
		$code_type = isset($a["code_type"])?$a["code_type"]:"code128";
		$print = isset($a["print"])?$a["print"]:false;

		$barcode = new n_barcode();
		return $barcode->draw($filepath,$text,$size,$orientation,$code_type,$print);
	}
}

class n_view{
	static public $content="{{:null:}}";
	protected static $instance;
	private static $call=0;

    protected function __construct () 
    {
        // disable creation of public instances 
    }

    public function init(){
    	self::$call=0;
    	return eval("?>".self::$content);
    }

	public function replace($section,$new_value){
		$replace = str_replace("{{:".$section.":}}",$new_value,static::$content);
		self::$call++;
		self::$content=$replace;
		return new self;
	}

	public static function make($path){
		$http = n_http::content($path);
		self::$content=$http;
		return new static;
	}

	public static function exist($path){
		if(file_exists($path)){
			return true;
		} else{return false;}
	}

}

class n_assets{
	private static $script=array();
	private static $style=array();
	public static function add($type="script",$path){
		switch($type){
			case"script":
			self::$script[]=$path;
			break;
			case"style":
			self::$style[]=$path;
			break;
			default:
			self::$script[]=$path;
			break;
		}
	}
	public static function script(){
		$display = "";
		for($i=0;$i<count(self::$script);$i++){
			$display .= PHP_EOL."<script type='text/javascript' src='".self::$script[$i]."'></script>";
		}
		print($display).PHP_EOL;
	}
	public static function style(){
		$display = "";
		for($i=0;$i<count(self::$style);$i++){
			$display .= PHP_EOL."<link rel='stylesheet' type='text/css' href=".self::$style[$i]." />";
		}
		print($display).PHP_EOL;
	}
}

class n_html{

	public static $save_template=array();

	public static function saveTemp($name=null,$html=null){
		if(isset($name) and strlen($name)>=3 and !isset(static::$save_template[$name])){
			static::$save_template[$name]=$html;
		} else{
			return "[ERROR] Wrong parameter name!";
		}
	}

	public static function loadTemp($name){
		if(isset(static::$save_template[$name])){
			return static::$save_template[$name];
		} else{return "[ERROR] Not found template!";}
	}

	public static function removeTemp($name){
		if(isset(static::$save_template[$name])){
			static::$save_template[$name]=null;
			return true;
		} else{return false;}
	}

	public static function clearTemp(){
		static::$save_template=null;
		if(!isset(static::$save_template) and count(static::$save_template)<=0){
			return true;
		} else{
			return false;
		}
	}

	public static function draw_input($d=array()){

		$type = isset($d["type"])?$d["type"]:"text";
		$required = isset($d["required"]) and $d["required"]==true?"required":"";
		$autocomplete =isset($d["autocomplete"]) and $d["autocomplete"]==false?"autocomplete=off":"";
		$disabled = isset($d["disabled"]) and $d["disabled"]==true?"disabled=disabled":"";
		$readonly = isset($d["readonly"]) and $d["readonly"]==true?"readonly":"";
		$placeholder = isset($d["placeholder"])?"placeholder='".$d["placeholder"]."'":"";
		$value = isset($d["value"])?"value='".$d["value"]."'":"";
		$class = isset($d["class"])?"class='".$d["class"]."'":"";

		$draw = '<input type="'.$type.'" '.$class.' '.$required.' '.$autocomplete.' '.$disabled.' '.$readonly.' '.$placeholder.' '.$value.' />';
		return $draw;
	}
}
?>
