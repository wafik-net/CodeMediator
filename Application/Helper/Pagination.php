<?php 

class Pagination
{
	// Properties
	private $table;
	private $total_results = 10;
	private $total_items;
	private $total_pages;
	private $current_page;
	private $page_key = 'p';
	private $start_from;
	private $order_by = null;
	private $message = null;
	private $next_page;
    private $prev_page;
    private $max_list = 15;
    private $add_sql = null;


	function __construct($table = null)
	{
		// Define Table Of Items
		if($table == null):
			$this->message = 'Table of items is not defined !';
			return false;
		else:
			$this->table = $table;
		endif;

		// Calc Total Items 
		$database = new database();
		try{
			$sql = "SELECT * FROM $table";
			$stmt =  $database->connect()->prepare($sql);
			$stmt->execute();
			$this->total_items = $stmt->rowCount();
		}catch(PDOException $e){$this->message = $e->getMessage();return false;}

		// Current Page Number 
		$this->current_page = isset($_GET[$this->page_key]) ? $_GET[$this->page_key] : 1;

		// Next & Prviews Page 
		$this->next_page = ($this->current_page +1);
		$this->prev_page = ($this->current_page -1);

		// Calc Total Pages 
		if($this->total_pages == null):
		    $this->total_pages = ceil($this->total_items/$this->total_results);
		endif;
		

	}

	// Set Additional Sql ------------------------------
    public function sql($code = null)
    {
    	if($code == null):
    		$this->message = 'Function cannot be null';
    	else:
    		$this->add_sql = $code;
    	endif;
    }
    // Set Limit Items Per Page ------------------------
    public function limit($limit)
    {
    	// Set Total Results Per Page
    	$this->total_results = $limit;
    	// Calc Total Pages 
    	$this->total_pages  =  ceil($this->total_items/$this->total_results);
    	// Start From 
		$this->start_from = ($this->current_page -1)*$this->total_results;
    }
    // Set Max Pagination List Btn ---------------------
    public function maxList($max)
    {
    	$this->max_list = $max;
    }
    // Fetch Items By Order ----------------------------
    public function orderBy($key = null, $method = null)
    {
    	if($key == null || $method == null):
    		$this->message = 'Order key not defined';
    	else:
    		$this->order_by = " ORDER BY $key $method ";
    	endif;
    	
    }
    // Set Page Key ------------------------------------
    public function pageKey($key = null)
    {
    	$this->page_key = $key;
    }

    // Return Error Message ----------------------------
	public function getMessage()
	{
		return $this->message;
	}

	// Get Paginated Items -----------------------------------
	public function fetch()
	{

		$database = new database();
		try{
			$sql = null ;
			// check additional sql
			if($this->add_sql != null):
				$sql = "SELECT * FROM $this->table $this->add_sql";
			else:
				$sql = "SELECT * FROM $this->table";
			endif;
			// Check order by
			if($this->order_by != null):
				$sql .=" $this->order_by LIMIT $this->start_from , $this->total_results ";
			else:
			    $sql  .= " LIMIT $this->start_from , $this->total_results ";
			endif;
			$stmt =  $database->connect()->prepare($sql);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}catch(PDOException $e){$this->message = $e->getMessage();return false;}

	}
	// Get Pagination Buttons --------------------------------
	public function getMenu()
	{

		$menu = "<ul class='pagination'>";
		// First Page
		if($this->current_page >1 && $this->current_page <= $this->total_pages):
		    $menu .= "<li class='page-item active'><a class='page-link' href='?$this->page_key=1'>First</a></li>";
		endif;
		// Previous Page
		if($this->current_page > 1):
			$menu .= "<li class='page-item'>
			        	<a class='page-link' href='?$this->page_key=".$this->prev_page."'>
			        		<span aria-hidden='true'>&laquo;</span>
			        	</a>
			        </li>";
		endif;
		// Pages Num Links
		for($counter = 1; $counter < $this->total_pages && $counter <= $this->max_list; $counter++): 

			$menu .= "<li class='page-item'><a class='page-link' href='?$this->page_key=$counter'>$counter</a></li>";

		endfor;
		// Points Limit 
		if($this->total_pages > $this->max_list):
			$menu .= "<li class='page-item'><a class='page-link' href='?$this->page_key=1'>..</a></li>";
		endif;
		// Next Page
		if($this->current_page >= 1 && $this->current_page < $this->total_pages):
			$menu .= "<li class='page-item'><a class='page-link' href='?$this->page_key=".$this->next_page."'>
			          <span aria-hidden='true'>&raquo;</span></a></li></a></li>";
		endif;
		// Last Page
		if($this->current_page < $this->total_pages):
			$menu .= "<li class='page-item active'><a class='page-link'href='?$this->page_key=".$this->total_pages."'>Last</a></li>";
		endif;
		$menu .= "</ul>";
		return $menu;
	}
	
}