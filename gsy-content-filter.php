<?php

/*
 * Plugin Name: GSY Content Filter
 * Plugin URI: https://github.com/georgi-yankov/gsy-content-filter
 * Description: Filter words from the title, content, excerpt or tags in posts.
 * Version: 1.0
 * Author: Georgi Yankov
 * Author URI: http://gsy-design.com
 * Text Domain: gsy-content-filter
 * License: GPLv2
 */

/* Copyright 2014 Georgi Yankov (email : georgi.st.yankov@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

require_once 'includes/class-gsy-content-filter.php';

$gsy_content_filter = new GSY_Content_Filter();
load_plugin_textdomain('gsy-content-filter', false, plugin_basename(dirname(__FILE__) . '/localization/'));
