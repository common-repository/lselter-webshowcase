<?php

/*
Plugin Name: Lselter Webshowcase
Plugin URI: http://design.lselter.co.uk/lselter-webshowcase
Description: Builds and maintains screenshots of websites.
Version: 00.01.08
Author: Lönja
Author URI: http://lselter.co.uk
License: GPL2
	Copyright 2014  L Selter  (email : Loenja@lselter.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

//block direct access to php code
defined('ABSPATH') or die("No script kiddies please!");

//load config defaults
require_once("lswc_config.php");
$plugin_base_file= __FILE__;
//register and configure custom post(s)
require_once('lswc_cron.php');
require_once("lswc-site-post.php");
//core screenshot code
require_once("lswc_cachethumbs.php");
//register and configure plugin settings page.
require_once("lswc_options.php");
//todo: wordpress plugin dependency,  work out best solution.
//feature: style bugerator,
//feature: optional add alternative screenshot APIs
/* free, easy, low quality: http://wwww.website-screenshot.de/
 * high quality png very flexible rates: https://screenshotmachine.com/apiguide.php
 *
 */

//feature: implement archive system. also archive page to view page history.
//feature: ideally find api that allows 1080p screenshots. with width 1920px
//feature: convert arctext lib for use as a slider
//todo: add localisations
//todo: allow multiple urls to showcase different parts, seperate size options to just one or 2 sizes to allow several pages be imaged