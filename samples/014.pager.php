<?php

	include '../db.php';

	//Create pager for page 2 with 8 items per page
	$pager = new \db\pager (3, 8);

	//When we set total item count pager recalculates itself
	$pager->total (30);

	\db\debug ($pager);

	\db\debug ($pager->page, 'current page');
	\db\debug ($pager->pages, 'pages count');
	\db\debug ($pager->total, 'total items');
	\db\debug ($pager->count, 'item count per page');
	\db\debug ($pager->from, 'first item number for result');



?>