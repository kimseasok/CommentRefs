<?php
/*
 * @package CommentRefs
 */
 
namespace Inc\Base;
 
class ActivateCrefs
{
	public static function activate(){
        
        $default_options = array(
            'crefs_post_type' => array(
                'message' => '\'s recent post:',
            ),
            'crefs_get_ten_posts' => array(
                'message' => 'Only Admin',
            ),
            'crefs_get_dofollow' => array(
                'message' => 'Only Admin',
            ),
        );
        
        foreach($default_options as $option => $value){
            if(is_array($value)) {
                $value = array_map('sanitize_text_field', $value);
                
                update_option($option, $value);
            }
        }
        
        flush_rewrite_rules();
        
    }
    
}