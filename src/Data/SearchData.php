<?php 

namespace App\Data;

/* Moteur de recherche des évènements */
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
	* @var Date 
	*/
	public $date = '';

}