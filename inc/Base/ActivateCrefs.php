<?php
/*
 * @package CommentRefs
 */
 
namespace Inc\Base;
 
class ActivateCrefs
{
	public static function activate(){
        
        flush_rewrite_rules();
        
    }
    
}