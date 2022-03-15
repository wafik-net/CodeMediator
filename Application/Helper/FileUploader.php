<?php 

class FileUploader
{

	private $file_dir;
	private $types_allowd = [];
	private $file_name;
	private $max_size;
	private $message;
	private $uploadOk = 1;

	// Set Directory Where To Upload --------
	function where($dir)
	{
		if(!is_dir($dir.DS)):
			$this->message = 'Unknown directory: '.$dir;
			$this->uploadOk = 0;
			return false;
		endif;
		$this->file_dir = $dir.DS;
		
	}
	// Set Max Size ------------------------------
	function setMaxSize($sizeMB = 1)
	{
		$this->max_size = $sizeMB*(1048000);
	}
	// Set Types Allowed -------------------------
	function allow($types)
	{
		if(!is_array($types)):
			$this->message = 'File types need to be in array';
			$this->uploadOk = 0;
			return false;
		endif;
		$this->types_allowd = $types;
	}
	// Get Message -------------------------------
	function getMessage()
	{
		return $this->message;
	}

	// Start Uploading ---------------------------
	function upload($fileName = null)
	{
		// Get File name
		if($fileName == null):
			$this->message = 'File name not exist';
			$this->uploadOk = 0;			
			return false;
		endif;
		$this->file_name = $fileName;

		// Get File Info
		$name = $_FILES[$fileName]['name'];
		$path = $this->file_dir.basename($name);
		$tmp  = $_FILES[$fileName]['tmp_name'];
		$size = $_FILES[$fileName]['size'];
		$ext  = pathinfo($name, PATHINFO_EXTENSION);

		// Check If File Selected
		if(empty($name)):
			$this->message = 'File not selected!';
			$this->uploadOk = 0;
			return false;
		endif;

		// Check Size
		if($this->max_size != null):
			if($size > $this->max_size):
				$this->message = 'File is to large';
				$this->uploadOk = 0;			
				return false;
			endif;
		endif;

		// Check File Types Allowed
		if(!empty($this->types_allowd)):
			if(!in_array($ext, $this->types_allowd)):
				$this->message = 'Type '.$ext.' not suported';
				$this->uploadOk = 0;			
				return false;
			endif;
		endif;
		// Upload File
		if($this->uploadOk == 1):
			if(!@copy($tmp, $path)):
				if(!@move_uploaded_file($tmp, $path)):
					$this->message = 'File did not uploaded!';
					return false;
				endif;
			endif;
			$this->message = 'File uploaded successfoly';
			return true;
		endif;
	}

	// Delete Uploaded File -----------------------
	function unset()
	{
		return @unlink($this->file_dir.$_FILES[$this->file_name]['name']) ? true : false;
	}

}