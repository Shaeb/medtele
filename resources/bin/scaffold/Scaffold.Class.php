<?php
require_once( '../constants.php' );
class Scaffold{
	private $connection;
	private $tables;
	
	public function __construct(DatabaseConnection $connection){
		$this->connection = $connection;
		$this->tables = array();
	}
	
	public function getTableData(){
		$this->connection->connect();
		$query = "show tables";
		if(!$this->connection->query($query)){
			return false;
		}
		$results = $this->connection->getResults();
		while($result = mysql_fetch_row($results)){
			if(1 <= count($result)){
				$this->tables[$result[0]] = $result[0];
			}
		}
	}
	
	public function getTableDefinitionData($tableName){
		if(!isset($tableName)){
			return false;
		}
		$this->connection->connect();
		$query = "show columns from {$tableName}";
		$this->connection->query($query);
		$results = $this->connection->getResults();
		$map = array();
		
		while($result = $this->connection->getObject()){
			$map[] = array("field" => $result->Field, "type" => $result->Type, "null" => $result->Null, 
				"key" => $result->Key, "default" => $result->Default, "extra" => $result->Extra, "value" => null );
		}	
		$this->tables[$tableName] = array( "table_name" => $tableName, "fields" => $map );
		$this->getTableKeys($tableName);
	}
	
	public function getTableKeys($tableName){
		if(!isset($tableName)){
			return false;
		}
		
		$this->connection->connect();
		$query = "show create table {$tableName}";
		$regexPrimaryKey = "/PRIMARY KEY \(\`[\w]+\`\)/";
		$regexForeignKeys = "/FOREIGN KEY \(\`[\w]+\`\) REFERENCES \`[\w]+\` \(\`[\w]+\`\)/";
		$regexFields = "/\`[\w]+\`/";
		$regexRemoveBackticks = "/\`/";
		$this->connection->query($query);
		$results = $this->connection->getResults();
		$map = array();
		
		while($result = mysql_fetch_row($results)){
			// getting primary keys ...
			$values = null;
			$num = preg_match_all($regexPrimaryKey, $result[1], $values);
			foreach( $values[0] as $value){
				//print_r($value);
				$num = preg_match_all( $regexFields, $value, $matches);
				//print_r($matches);
				if(1 == $num){
					$primary_key = preg_replace($regexRemoveBackticks, "", $matches[0][0]);
					$this->tables[$tableName]["references"]["primary_key"] = $primary_key;
				}
				$matches = null;
			}
			// getting foreign keys ...
			$values = null;
			$num = preg_match_all($regexForeignKeys, $result[1], $values);
			foreach( $values[0] as $value){
				//print_r($value);
				$num = preg_match_all( $regexFields, $value, $matches);
				//print_r($matches);
				if(3 == $num){
					$foreign_key = preg_replace($regexRemoveBackticks, "", $matches[0][0]);
					$referenced_table = preg_replace($regexRemoveBackticks, "", $matches[0][1]);
					$referenced_field = preg_replace($regexRemoveBackticks, "", $matches[0][2]);
					$map[] = array("foreign_key" => $foreign_key, "referenced_table" => $referenced_table, "referenced_field" => $referenced_field);
				}
				$matches = null;
			}
			//print_r($map);
			$this->tables[$tableName]["references"]["foreign_keys"] = $map;
		}
	}
	
	public function getTables(){
		return $this->tables;
	}
}

/*******
 *     [Employees] => Array
        (
            [table_name] => Employees
            [fields] => Array
                (
                    [0] => Array
                        (
                            [field] => employeeId
                            [type] => int(11)
                            [null] => NO
                            [key] => PRI
                            [default] => 
                            [extra] => auto_increment
                            [value] => 
                        )

                    [1] => Array
                        (
                            [field] => userId
                            [type] => int(11)
                            [null] => NO
                            [key] => MUL
                            [default] => 
                            [extra] => 
                            [value] => 
                        )

                    [2] => Array
                        (
                            [field] => employeeTitleId
                            [type] => int(11)
                            [null] => NO
                            [key] => MUL
                            [default] => 
                            [extra] => 
                            [value] => 
                        )

                )

            [references] => Array
                (
                    [primary_key] => employeeId
                    [foreign_keys] => Array
                        (
                            [0] => Array
                                (
                                    [foreign_key] => employeeTitleId
                                    [referenced_table] => employeetitles
                                    [referenced_field] => employeeTitleId
                                )

                            [1] => Array
                                (
                                    [foreign_key] => userId
                                    [referenced_table] => users
                                    [referenced_field] => userId
                                )

                        )

                )

        )

 */
class ScaffoldObject{
	private $data;
	private $values;
	private $connection;
	
	public function __construct($dataMap, DatabaseConnection $connection){
		if(isset($dataMap) && isset($connection)){
			$this->connection = $connection;
			$this->data = (isset($dataMap) && is_array($dataMap)) ? $dataMap : array();
			if(array_key_exists("fields",$this->data)){
				foreach($this->data["fields"] as $field){
					$this->values[$field["field"]] = $field["value"];
				}
			} else {
				$this->values = array();
			}
		}
	}
	
	public function __get($name){ 
		$value = null;
		if("values" == $name) {
			$value = $this->values;
		} else {
			if(array_key_exists($name,$this->values)){
				$value = $this->values[$name];
			}
		}
		return $value;
	}
	
	public function __set($name,$value){
		if(isset($name) && array_key_exists($name,$this->values)) {
			$this->values[$name] = $value;
		}
	}
	
	/*******
	 * conventional usage here:
	 * 	if called with one argument, will check to see if it is numeric, if so, will assume it is the primary key
	 * 		ex: $o->find(1) will search for a primary key = 1;
	 * if the one argument is a string == "all", then will grab all
	 * 		ex: $o->find("all"), then will grab all  
	 * 	if called with two arguments, the second parameter is expected to be a hash with the following support options
	 * 		filters = array of fields to search for
	 * 		order_by = field to arrange the search by
	 * 		order_type = desc || asc - desc = descending and asc = ascending, default is descending
	 * 		conditions = array of key value pairs, with the following guidelines
	 * 			field => value - evaluates the field is equal to the value
	 * 			field=> "> value" - evaluates as the field is greater than the value
	 * 			field=> ">= value" - evaluates as the field is greater than or equal to the value
	 * 			field=> "< value" - evaluates as the field is less than the value
	 *			field=> "<= value" - evaluates as the field is less than or equal to the value
	 * 			field=> "!= value" - evaluates as the field does not equal value
	 * 		limit = number to limit by
	 * 
	 * 	example: to get last 100 usernames who have logged in since Jan 1st 2010
	 * 		array( 
	 * 			"filters" => array( "username", "lastLoggedIn", "ipaddress" ),
	 * 			"order_by" => "lastLoggedIn",
	 * 			"order_type" => "desc",
	 * 			"conditions" => array( "lastLoggedIn" => " > 2010-01-01 00:00:00" ),
	 * 			"limit" => 100
	 * 		);
	 */
	public function find(){
		$numberOfArguments = func_num_args();
		if(0 == $numberOfArguments){
			return null;
		}
		$tableName = $this->data["table_name"];
		$key = $this->data["references"]["primary_key"];
		$arguments = func_get_args();
		$query = "select [FIELDS] from {$tableName}";
		$whereClause = "";
		$orderClause = "";
		$options = null;
		
		//ex: $o->find(1) will search for a primary key = 1;
		if(1 <= $numberOfArguments && is_numeric($arguments[0])){
			$query .= " where {$key} = {$arguments[0]}";
			$query = str_replace("[FIELDS]", "*", $query);
		}
		// will find all in any case
		if(0 == strcasecmp("all", $arguments[0]) || 1 == $numberOfArguments){
			$query = str_replace("[FIELDS]", "*", $query);
		}
		
		if( 2 <= $numberOfArguments ) {
			if(is_array($arguments[1])){
				$options = $arguments[1];
				if(array_key_exists("filters",$options)){
					$length = count($options["filters"]);
					if(!array_key_exists($key,$options["filters"])){
						$fields .= "{$key}, ";
					}
					for($i = 0; $i < $length; ($i++)){
						if(array_key_exists($options["filters"][$i], $this->values)) {
							$fields .= ( $i == ($length - 1)) ? $options["filters"][$i] : $options["filters"][$i] . ", ";
						}
					}
				} else {
					$fields = "*";
				}
				
				$regexStrip = "/,\s$/";
				$fields = preg_replace($regexStrip, "", $fields);

				if(array_key_exists("conditions",$options)){
					$query .=  "[WHERE] ";
					$regexOperators = "/^[\>\<\=\!]{1,2}/";
					$conditions = $options["conditions"];
					$keys = array_keys($conditions);
					foreach($keys as $key){
						$matches = array();
						if(0 != preg_match_all($regexOperators, $conditions[$key],$matches)){
							$whereClause .= " and {$key} {$conditions[$key]}";
						} else {
							$whereClause .= " and {$key} = {$conditions[$key]}";
						}
					}
					
					$regexStrip = "/\sand\s$/";
					$fields = preg_replace($regexStrip, "", $fields);
				}
				
				if(array_key_exists("order_by",$options) && array_key_exists($options["order_by"],$this->values)){					
				 	$query .= " [ORDER]";
					$orderClause = " order by " . $options["order_by"] . " 	";
					$orderClause .= (array_key_exists("order_type",$options)) ? $options["order_type"] : "desc";
				}
				
				$query = str_replace("[FIELDS]", $fields, $query);
				$query = str_replace("[WHERE]", $whereClause, $query);
				$query = str_replace("[ORDER]", $orderClause, $query);
			}
		}
		
		$query .= ";";
		
		$this->connection->connect();
		$this->connection->query($query);
		$results = $this->connection->getResults();
		echo $query . " : ";
		while($result = mysql_fetch_array($results, MYSQL_ASSOC)){
			$keys = array_keys($result);
			foreach($keys as $key){
				$this->values[$key] = $result[$key];
			} 
		}	
	}
	
	public function update(){
		$tableName = $this->data["table_name"];
		$primaryKey = $this->data["references"]["primary_key"];
		$query = "update {$tableName} set [FIELDS] where {$primaryKey} = " . $this->values[$primaryKey] . ";";
		
		$keys = array_keys($this->values);
		foreach($keys as $key){
			if($primaryKey != $key and isset($this->values[$key])){
				$fields .= "{$key} = '{$this->values[$key]}', "; // you can always add quotes, mysql will handle numbers just fine
			}
		}
		$regexStrip = "/,\s$/";
		$fields = preg_replace($regexStrip, "", $fields);
		$query = str_replace("[FIELDS]", $fields, $query);
		echo $query;
		
		$this->connection->connect();
		$success = $this->connection->queryExecute($query);
		
		return $success;
	}
	
	public function add(){
		if(!isset($this->data["table_name"])){
			return false;
		}
		if(!isset($this->connection)){
			return false;
		}
		$tableName = $this->data["table_name"];
		$query = "insert into {$tableName}([FIELDS]) values([VALUES]);";
		$keys = array_keys($this->values);
		$field = "";
		$values = "";
		foreach( $keys as $key){
			if(isset($this->values[$key])){
				$fields .= "{$key},";
				$values .= "'{$this->values[$key]}',";
			}	
		}
		$regexStrip = "/,$/";
		$fields = preg_replace($regexStrip, "", $fields);
		$values = preg_replace($regexStrip,"",$values);
	
		$query = str_replace("[FIELDS]", $fields, $query);
		$query = str_replace("[VALUES]", $values, $query);	
		$query .- ";";
		$this->connection->connect();
		$success = $this->connection->queryExecute($query);
		
		return $success;
	}

	public function delete(){
		if(!isset($this->data["references"]["primary_key"])){
			return false;
		}
		if(!isset($this->data["table_name"])){
			return false;
		}
		if(!isset($this->connection)){
			return false;
		}
		$primaryKey = $this->data["references"]["primary_key"];
		$tableName = $this->data["table_name"];
		$query = "delete from {$tableName} where {$primaryKey} = '{$this->values[$primaryKey]}';";
	
		$this->connection->connect();
		$success = $this->connection->queryExecute($query);
		
		return $success;
	}
}

class ScaffoldFactory{
	private $scaffold;
	private $tables;
	private $objects;
	private $connection;
	private static $instance;
	
	private function __construct(DatabaseConnection $connection){
		if(isset($connection)){
			$this->connection = $connection;
			$this->scaffold = new Scaffold($this->connection);
			$this->objects = array();
		}
	}	

	public static function getInstance(DatabaseConnection $connection) {
		if( !isset( self::$instance ) ) {
			self::$instance = new ScaffoldFactory($connection);
		}
		return self::$instance;
	}

	public function buildScaffoldObject($tableName){
		if(!isset($tableName)){
			return null;
		}
		if(array_key_exists($tableName,$this->objects)){
			return $this->objects[$tableName];
		}
		
		$this->scaffold->getTableData();
		$this->scaffold->getTableDefinitionData($tableName);
		$tables = $this->scaffold->getTables();
		$object = new ScaffoldObject($tables[$tableName], $this->connection);
		$this->objects[$tableName] = $object;
		return $object;
	}
}

$database = $application->getDatabaseConnection();

$scaffold = new Scaffold($database);
$scaffold->getTableData();
//echo "<h1>Table Names</h1>";
//print_r($scaffold->getTables());
$tables = $scaffold->getTables();
foreach($tables as $table){
	$scaffold->getTableDefinitionData($table);
}
//echo "<h1>table definitions</h1>";
//$tables = $scaffold->getTables();
//$tableNames = array_keys($tables);
////foreach($tableNames as $table){
////	echo "<h3>{$table}</h3><p>";
////	echo print_r($tables[$table]);
////	echo "</p>";
////}
$factory = ScaffoldFactory::getInstance($database);
//$object = new ScaffoldObject($tables["Users"], $database);
$object = $factory->buildScaffoldObject("Users");
//print_r($object);
$object->find(1, array( "filters" => array( "username", "password", "ipaddress", "caps lock"),
						"conditions" => array("userLevel" => "> 1", "ipaddress" => "'::1'"),
						"order_by" => "username", "order_type" => "asc"));
echo "<hr/>";
print_r($object->values);
echo "<hr/>";
$object2 = $object;
$object2->userId = null;
$object2->username = 'medtele@gmail.com';
$object2->userLevel = 2;
//$object->add();
$object3 = $factory->buildScaffoldObject("Users");
$object3->find(4);
$object3->delete();
print_r($object3);
?>