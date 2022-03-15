<?php 
/*
 *---------------------------------------------------------------
 * CodeMediator -  Database
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 *
 */

class Database
{
	private $hostname;
	private $database;
	private $username;
	private $password;

	function __construct()
	{
		global $database;
		$this->hostname = $database['hostname'];
		$this->database = $database['database'];
		$this->username = $database['username'];
		$this->password = $database['password'];

	}
	function connect()
	{
		try{
			$conn = new PDO("mysql:host=".$this->hostname.";dbname=".$this->database.";charset=utf8",
				            $this->username,$this->password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e)
		{
			echo show_msg('<b>Faild Connecting To Database</b> '.$e->getMessage(), DANGER);
			echo show_msg("<b>Try to install database correctly from </b> <a href='".base_url('install')."'>here</a> ", WARNING);
			die();
		}
		return $conn;
	}
}
