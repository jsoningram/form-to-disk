<?php
	/*
	Plugin Name: Form to Disk
	Version: 0.2
	Description: Writes form submissions to local text file and sends file via email at specified reccurence
	Author: TEN 
	Author URI: http://enthusiastnetwork.com/
	Plugin URI: 
	Text Domain: mtod-f2d 
	*/

	require 'src/Settings.php';
	require 'src/f2d.php';
	require 'src/f2d_cron.php';
