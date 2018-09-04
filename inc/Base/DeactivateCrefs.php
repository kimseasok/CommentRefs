<?php
/*
 * @package CommentRefs
 */
 
namespace Inc\Base;
 
class DeactivateCrefs
{
    
	public static function deactivate(){
        
		flush_rewrite_rules();
        
	}
    
}