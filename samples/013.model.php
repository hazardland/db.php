<?php

	namespace galaxy
	{
		//ignore - ignore this class on scan
		/**
		* ignore
		*/
		class galaxy
		{
			public $stars = array();
		}

		class star
		{
			public $id;
			public $name;
			public $size;
			public function __construct ($name=null, $size=null)
			{
				$this->name = $name;
				$this->size = $size;
			}
		}

		class planet
		{
			/**
			* primary
			* length 16
			* @var string
			*/
			public $name;
			/**
			* type smallint
			* @var integer
			*/
			public $order;
			/**
			* @var boolean
			*/
			public $water;
			/**
			* @var \galaxy\star
			*/
			public $star;
			/**
			* enum
			* @var \galaxy\moon
			*/
			public $moons = array ();
			public function __construct ($star=null, $name=null, $order=null, $water=false, $weight=null)
			{
				$this->name = $name;
				$this->order = $order;
				$this->star = $star;
				$this->water = $water;
				$this->weight = $weight;

			}
			public function add (\galaxy\moon $moon)
			{
				$this->moons[$moon->id] = $moon;
			}
		}

		//cache load - cache all objects of moon
		//in temp array per script load
		/**
		* cache load
		*/
		class moon
		{
			public $id;
			public $name;
			public function __construct ($name=null)
			{
				$this->name = $name;
			}
		}
	}

?>