<?php

// If uninstall/delete not called from WordPress then exit
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

delete_option('gsy_content_filter_options');