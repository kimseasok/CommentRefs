<?php
/*
 * @package CommentRefs
 */
 
namespace Inc\Base;
 
class ActivateCrefs
{
	public static function activate(){
        /*
         * Set default settings when activate the plugin
         */
        $options = array(
            
            'crefs_post_type' => array(
                
                'post'      => 'on',
                'message'   => '#Commentator Name# {\'s recent post:} #post title#'
                
            ),
            
            'crefs_get_ten_posts' => array(
                
                'minimum_comments'  => '10',
                'message'           => '{Only admin} can get 10 post in the list'
                
            ),
            
            'crefs_get_dofollow' => array(
                
                'minimum_comments'  => '10',
                'message'           => '{Only admin} can get dofollow attribute'
                
            ),
            
            'crefs_sm_integration' => array(
                
                'facebook_app'   => ''
                
            ),
            
            'crefs_comment_redirect' => array(
                
                'custom_url' => ''
                
            ),
            
            'crefs_prevent_lq' => array(
                'prevent_link_in_comment'   => 'on',
                'prevent_short_comment'     => 'on',
                'minimum_length'            => '10'
            ),
            
            'crefs_miscellaneous' => array(
                
                'credit_CommentRefs'        => 'on'
            )
            
        );
        
        foreach ($options as $option => $value){
            
            if (get_option($option)) {
                
                update_option($option, $value);
                
            } else {
                
                add_option($option, $value);
                
            }
            
        }
        
        flush_rewrite_rules();
    }
    
}