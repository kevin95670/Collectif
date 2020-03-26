<?php 

namespace App\Data;

class SearchData{

	/**
	* @var string 
	*/
	public $query = '';

	/**
	* @var int 
	*/
	public $page = 1;

	/**
	* @var Categories[] 
	*/
	public $categories = [];

	/**
	* @var string 
	*/
	public $city = '';

	/**
	* @var string 
	*/
	public $name = '';

	/**
	* @var Date 
	*/
	public $date = '';

}