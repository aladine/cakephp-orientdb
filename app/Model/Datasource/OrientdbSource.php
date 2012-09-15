<?php
//require_once('../../Plugin/congow/vendor/autoload.php');
//App::uses('congow/vendor/autoload','');
App::import('Vendor', 'OrientDB',array('file'=>'orientdb-php/OrientDB/OrientDB.php'));
class OrientdbSource extends DataSource{

	protected $_db=null;
	protected $_schema = array('recordPos'=>array('type'=>'Integer'),
								'clusterId'=>array('type'=>'Integer'),
								'type'=>array('type'=>'String'),
								'name'=>array('type'=>'String'),
								'in'=>array('type'=>'Linkset'),
								'out'=>array('type'=>'Linkset')
								);
	protected $_rootPassword = 'F7010F0873BA34D8FA861CF79BFFBEC3CF6DDD71C0C3162F2CEA326A66ED0EA8';


	public function __construct($config = array()) {
	    parent::__construct($config);
	    $this->connect();
	}

	public function connect(){
		/*
		$parameters = Congow\Orient\Binding\BindingParameters::create('http://admin:admin@127.0.0.1:2480/temp');
		$orient = new Congow\Orient\Binding\HttpBinding($parameters);
		return $orient;
		*/
		try {        
	        $db = new OrientDB('localhost', 2424, 30);
	    }
	    catch (Exception $e) {
	        die('Failed to connect: ' . $e->getMessage());
	    }
	    
	    //echo 'Connecting as root...' . PHP_EOL;
	    try {
	        $connect = $db->connect('root', $this->_rootPassword);
	        $db->DBOpen('first', 'admin', 'admin');
	    }
	    catch (OrientDBException $e) {
	        die('Failed to connect(): ' . $e->getMessage());
	    }
	    $this->_db =  $db;
	}

	public function describe($Model){
		return $this->_schema;
	}

	public function listSources($data = NULL){
		return null;
	}

	public function calculate($Model, $func, $params)	{
		return 'COUNT';
	}

    /*
    array(
		'conditions' => null,
		'fields' => null,
		'joins' => array(),
		'limit' => null,
		'offset' => null,
		'order' => array(
			(int) 0 => null
		),
		'page' => (int) 1,
		'group' => null,
		'callbacks' => true
	)
    */
 	
 	
 	public function command_query( $str) {
 		//debug($str);
 		$recs = $this->_db->command(OrientDB::COMMAND_QUERY, $str);
 		return $recs;
 	}
	
	
	private function getCondition($values =  array(), $glue){
		$conds = array();
		if (empty($values)) return '1 = 1';
		foreach ($values as $key => $value) {
			$conds[] = $key.' = "'.$value.'"';
		}
		return  implode($glue, $conds); 
	}


	public function read(Model $model, $queryData = array()) {
		//debug($queryData);

		try {	
	        	if(isset($queryData['conditions']['recordPos'])) $recs = $this->_db->recordLoad($queryData['conditions']['clusterID'].':'.$queryData['conditions']['recordPos']);
	        	else $recs = $this->_db->command(OrientDB::COMMAND_QUERY, 'SELECT FROM V WHERE '.self::getCondition($queryData['conditions'],' AND '));
        }
        catch (OrientDBException $e) {
            die('OrientDB Exception: ' . $e->getMessage());
        }
        //echo 'SELECT FROM V WHERE '.self::getCondition($queryData['conditions']);
        
        return $recs;
	}

	public function update(Model $model, $fields = array(), $values = array()) {
		return $this->create($model, $fields, $values);
	}



	public function create(Model $model, $fields = array(), $values = array()) {
		$values = array_combine($fields,$values);
		//debug('CREATE VERTEX SET '.self::getCondition($values,' , '));

		try {	
		 	if(is_array($values)){
		 		$rec = new OrientDBRecord();
		 		foreach ($values as $key => $value) {
		 			$rec->data->$key= $value;
		 		}
		 		//$rec->data = $values;
		 		// 6 is V cluster
	        	$recordPos = $this->_db->recordCreate(6,$rec);
				return $recordPos;
	        	}
	        }            
        catch (OrientDBException $e) {
            die('OrientDB Exception: ' . $e->getMessage());
        }
	}

	public function delete(Model $model,  $conds = array()) {
		$this->_db->recordDelete($conds['recordPos'].''.$conds['clusterID']);
	}

	public function query($method, $params, $Model){
		//debug(gettype($params[0]['command']));
		if(method_exists($this,$method)){
			return call_user_func_array(array($this,$method), $params);
		} 
		else if($method == 'command_query') return self::command_query($params);
	}



}

?>
