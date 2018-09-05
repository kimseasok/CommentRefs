<?php
/**
 * @package CommentRefs
 */
/*
Plugin Name: Comment Refs
Plugin URI: https://github.com/kimseasok/CommentRefs
Description: Build bigger community and inspire more reading by reward the commentator a link to their recent post.
Version: 1.0.0
Author: Kimsea Sok
Author URI: https://basicblogtalk.com
License: GPLv2 or later
Text Domain: CommentRefs
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

// Make sure we don't expose any info if called directly

defined ('ABSPATH') or die ('Hey, selly human!... You\'re accessing none existing page');

//Require once the Composer Autoload

if (file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
    
	require_once dirname(__FILE__).'/vendor/autoload.php';
    
}



//Register activation and deactivation hooks

register_activation_hook(__FILE__, 'activate_crefs');
register_deactivation_hook(__FILE__, 'deactivate_crefs');

function activate_crefs(){
    
	Inc\Base\ActivateCrefs::activate();
    
}

function deactivate_crefs(){
    
	Inc\Base\DeactivateCrefs::deactivate();
    
}

//Initialize all the core classes of the plugin

if(class_exists('Inc\\InitCrefs')){
    
	Inc\InitCrefs::register_services();
    
}