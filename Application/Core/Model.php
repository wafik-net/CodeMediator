<?php 
/*
 *---------------------------------------------------------------
 * CodeMediator - Model
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 * This File Contains All Database Operations
 *
 */
class Model 
{

	private static $actionMsg = null;
	
	// Set Actions Msg -----------------------------------------------
	public static function setMessage($str)
	{
		self::$actionMsg = $str;
	}

	// Get Actions Msg -----------------------------------------------
	public static function getMessage()
	{
		return self::$actionMsg;
	}

    // Custom Sql ----------------------------------------------------
    public static function sql($code)
    {
    	$database = new Database();
    	try{
    		$stmt = $database->connect()->prepare($code);
    		return $stmt->execute() ? $stmt->fetchAll(PDO::FETCH_OBJ) : false;
    	}catch(PDOException $e){
    		self::setMessage($stmt->errorInfo()[2]);
    		return false;
    	}
    }

	// Get All Items -------------------------------------------------
	public static function get_all($table ,$data = null)
	{
		$database = new database();
		try{

			$sql = "SELECT * FROM  $table";
			if(is_array($data) AND $data!= null):
				extract($data);
				// Set Where -------------------
				if(isset($where)):
					$sql .= " WHERE $where";
				endif;
				// Set Order By -----------------
				if(isset($order) && isset($key)):
					$sql .= " ORDER BY $key $order";
				endif;
				// Set Limit --------------------
				if(isset($limit)):
					$sql .= " Limit $limit ";
				endif;
			endif;
    		$stmt = $database->connect()->prepare($sql);
    		if($stmt->execute()):
    			return $stmt->fetchAll(PDO::FETCH_OBJ);
    		endif;
    	}catch(PDOException $e)
    	{
    		self::setMessage($stmt->errorInfo()[2]);
    		return false;
    	}
	}

	// Get Specific Item ------------------------------------------------
	public static function get_where($table,$array) 
	{

		$database = new database();

        foreach ($array as $key => $value):
        	$sql = "SELECT * FROM $table WHERE $key  = ? ";
        endforeach;
		
		try{
			$stmt = $database->connect()->prepare($sql);
			$stmt->execute([$value]);
			if($result = $stmt->fetch(PDO::FETCH_OBJ)):
				return $result;
			else:
				return false;
			endif;
		}catch(PDOException $e)
		{
			self::setMessage($stmt->errorInfo()[2]);
			return false;
		}

	}

	// Get Specific Item ------------------------------------------------
	public static function get_all_where($table,$array) 
	{

		$database = new database();

        foreach ($array as $key => $value):
        	$sql = "SELECT * FROM $table WHERE $key  = ? ";
        endforeach;
		try{
			$stmt = $database->connect()->prepare($sql);
			$stmt->execute([$value]);
			if($result = $stmt->fetchAll(PDO::FETCH_OBJ)):
				return $result;
			else:
				return false;
			endif;
		}catch(PDOException $e){
			self::setMessage($stmt->errorInfo()[2]);
			return false;
		}
		

	}

	// Add New Item ---------------------------------------------------
	public static function insert($table,$array) 
	{

		 $database = new database();

		/*-------------------------
		 |        Way One         |
		 -------------------------*/

		/**
			$keys = implode(',' ,array_keys($array));
			foreach ($array as $key => $value):
				$prep[':'.$key] = htmlspecialchars($value);
			endforeach;
		     $sql = "INSERT INTO posts( $keys ) 
		       	               VALUES(".implode(',', array_keys($prep)) .")";
			$stmt = $database->connect()->prepare($sql);
			if($stmt->execute($prep)):
				return true;
			else:
				return false;
			endif;
		**/
        
        /*-------------------------
		 |        Way Two         |
		 -------------------------*/
        
	    $keys = array_keys($array);
	    $values = array_values($array);
	    $keylist = implode(',' ,$keys);
	    $valuelist = str_repeat("?,", count($keys)-1);
		$sql = "INSERT INTO $table($keylist) VALUES ({$valuelist}?) ";
		try{
			$stmt = $database->connect()->prepare($sql);
			$stmt->execute($values);
			return true;
		}catch(PDOException $e){
			self::setMessage($stmt->errorInfo()[2]);
			return false;
		}
		
	              
					
	}

	// Delete Item ------------------------------------------------------
	public static function delete($table, $array)
	{
		$database = new database();
		foreach ($array as $key => $value):
        	$sql = "DELETE FROM $table WHERE $key = ? ";
        endforeach;
        try{
        	$stmt = $database->connect()->prepare($sql); 
        	if(!$stmt->execute([$value])): 
        		return false;
        	endif;
        	return true;
        }catch(PDOException $e)
        {
        	self::setMessage($stmt->errorInfo()[2]);
        	return false;
        }     
	}

	// Delete Item ------------------------------------------------------
	public static function delete_all($table)
	{
		$database = new database();
        try{
        	$sql = "DELETE FROM $table";
        	$stmt = $database->connect()->prepare($sql); 
        	if(!$stmt->execute()): 
        		return false;
        	endif;
        	return true;
        }catch(PDOException $e)
        {
        	self::setMessage($stmt->errorInfo()[2]);
        	return false;
        }     
	}

	// Update Item ------------------------------------------------------
		public static function update($table, $data, $where = null, $bind = true) 
	{
		/*
		 *  Update Table Where $where[colum = value ]
		 *  IF array $where is not exist => update All
		*/

		$database = new database();
		// Set Where Statment -------------------------------
		$colum = null;
		if(is_array($where)):		
			foreach ($where as $col => $colvalue):
				$colum .= $col.' = '.("'{$colvalue}'").' AND ';
			endforeach;
			// Trim Last AND
		    $colum = rtrim($colum, ' AND ');
		else:
			$colum = true;
		endif;

		//---------------------------------------------------

		try{

			foreach ($data as $key => $value): 
				if($bind == true):
					$sql = "UPDATE $table SET  $key = :val WHERE $colum ";
				else:
					$sql = "UPDATE $table SET  $key = $value WHERE $colum ";
				endif;
				$stmt = $database->connect()->prepare($sql);
				$stmt->bindparam(':val', $value);
			    $stmt->execute();
			endforeach;
			
		}catch(PDOException $e)
		{	
			self::setMessage($stmt->errorInfo()[2]);
			return false;	
		}
		return true;
	}
	// Update All Item ------------------------------------------------------
	public static function update_all($table, $array) 
	{

		$database = new database();

		try{
			foreach ($array as $key => $value): 	
			$sql = "UPDATE $table SET  $key = :val ";
			$stmt = $database->connect()->prepare($sql);
			$stmt->bindparam(':val', $value);
		    $stmt->execute();
			endforeach;
		}catch(PDOException $e){	
			self::setMessage($stmt->errorInfo()[2]);
			return false;	
		}
		return true;
	}
	// Update All Item ------------------------------------------------------
	public static function update_all_except($table, $array, $id=null) 
	{

		$database = new database();

		try{
			foreach ($array as $key => $value): 	
			$sql = "UPDATE $table SET  $key = :val WHERE id != :id ";
			$stmt = $database->connect()->prepare($sql);
			$stmt->bindparam(':val', $value);
			$stmt->bindparam(':id', $id);
		    $stmt->execute();
			endforeach;
		}catch(PDOException $e)
		{	
			self::setMessage($stmt->errorInfo()[2]);
			return false;	
		}
		return true;
	}

	// Authentication -------------------------------------------------
	public static function auth($table, $array, $method = 'AND')
	{
		$database = new database();

		// Set Where Statment -------------------------------
		$colum = null;
		$method = ' '.$method.' ';
		if(is_array($array)):		
			foreach ($array as $col => $colvalue):
				$colum .= $col.' = '.("'{$colvalue}'").$method;
			endforeach;
			// Trim Last AND
		    $colum = rtrim($colum, $method);
		else:
			$colum = true;
		endif;	
		//---------------------------------------------------

		try{

			$sql = "SELECT * FROM $table WHERE $colum ";	
			$stmt = $database->connect()->prepare($sql);			
		    $stmt->execute();
		    $row = $stmt->fetch(PDO::FETCH_OBJ);
		    if($stmt->rowCount() >= 1):
				return $row;
			else:
				return 0;
			endif;
	
		}catch(PDOException $e)
		{	
			self::setMessage($stmt->errorInfo()[2]);
			return false;	
		}

	}


	// Check IF Item Exist ----------------------------------------------
	public static function exist($table, $array)
	{
		$database = new database();

		foreach ($array as $key => $value):
        	$sql = "SELECT * FROM $table WHERE $key  = ? ";
        endforeach;

        try{
        	$stmt = $database->connect()->prepare($sql);
			$stmt->execute([$value]);
			// [-] Fetch Data
			$row = $stmt->fetch(PDO::FETCH_OBJ);
			// [-] If Exist Return True
			if($stmt->rowCount() >= 1):
				return true;
			else:
				return false;
			endif;
        }catch(PDOException $e)
        {
        	self::setMessage($stmt->errorInfo()[2]);
        	return false;
        }
        
		
	}
	
	// Search Item ------------------------------------------------------
	public static function  like($table,$array)
	{
		$database = new database();

		foreach ($array as $key => $value):
        	$sql = " SELECT * FROM $table WHERE lower($key) LIKE lower('%".filter_quotes($value)."%') ";
        endforeach;

		try{
			$stmt = $database->connect()->prepare($sql);
			$stmt->execute([$value]);
			if($result = $stmt->fetchAll(PDO::FETCH_OBJ)):
				return $result;
			else:
				return false;
			endif;
		}catch(PDOException $e) 
		{
			self::setMessage($stmt->errorInfo()[2]);
			return false;
		}				
	}

	// Count Rows ----------------------------------------------------
	public static function count_rows($table)
	{
		$database = new database();
		try{
			$sql  = " SELECT * FROM $table ";
			$stmt =  $database->connect()->prepare($sql);
			return $stmt->execute() ?  $stmt->rowCount() : false;
		}catch(PDOException $e)
		{
			self::setMessage($stmt->errorInfo()[2]);
			return false;
		}
	}
	    // Count Total Colums ---------------------------------
	public static function count_cols($table ,$colum = null)
	{
		$database = new database();
    	if($colum == null):
    		return false;
    	endif;
    	try{
    		$sql = "SELECT * FROM  $table";
    		$stmt = $database->connect()->prepare($sql);
    		$stmt->execute();
    	    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    		$result = 0;
			foreach ($rows as $row):
			    $result = $result+$row->$colum;
			endforeach;
			return $result;

    	}catch(PDOException $e)
    	{
    		self::setMessage($stmt->errorInfo()[2]);
    		return false;
    	}
	}







}
