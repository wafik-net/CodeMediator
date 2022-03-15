<?php 


class indexController extends Controller{

	function default()
	{
		$this->view('layout/header');
		$this->view('home/index');
	}

}