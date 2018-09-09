<?php
/*
 * @package CommentRefs
 */
namespace Inc\Base;
use \Inc\Base\ControlerCrefs;

class EnqueueCrefs extends ControlerCrefs
{
	public function register(){
        
        //Enqueue JS and CSS scripts in front end
		add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        
        //Enqueue JS and CSS scripts in Admin area
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
        
	}
    
	public function enqueue(){
        
        $post_type = (get_post_type()) ? get_post_type() : '';
        
        $allowed_post_types = (get_option('crefs_post_type')) ? get_option('crefs_post_type') : '';
        
        $is_allowed = (is_array($allowed_post_types) && isset($allowed_post_types["{$post_type}"])) ? $allowed_post_types["{$post_type}"] : 'off';
        
        //return is CommenRefs isn't turned on or comment is not open
        if(false === comments_open() || $is_allowed !== 'on') {
            
            return;
            
        }
        
        //get current user email base on login user id
        $current_user = (get_current_user_id()) ? get_current_user_id() : '';
        $current_user_email = (get_the_author_meta('email', $current_user)) ? get_the_author_meta('email', $current_user) : '';
        
        //get credit setting option
        $miscellaneous = (get_option('crefs_miscellaneous')) ? get_option('crefs_miscellaneous') : '';
        
        $credited = (is_array($miscellaneous) && isset($miscellaneous['credit_CommentRefs']) && $miscellaneous['credit_CommentRefs'] == 'on') ? true : false;
        
        //setup api setting for localize script
        $settings['plugin_url'] =   $this->plugin_url;
        $settings['title']      =   (get_the_title(get_the_ID())) ? get_the_title(get_the_ID()) : '';
        $settings['permalink']  =   (get_permalink(get_the_ID())) ? get_permalink(get_the_ID()) : '';
        $settings['wponce']     =   wp_create_nonce('CommentRefs');
        $settings['website']    =   (get_the_author_meta('user_url', $current_user)) ? get_the_author_meta('user_url', $current_user) : '';
        $settings['loggedin']   =   (is_user_logged_in()) ? true : false;
        $settings['is_admin']   =   (current_user_can('switch_themes')) ? true : false;
        $settings['is_author']  =   ($current_user == get_post_field('post_author', get_the_ID())) ? true : false;
        $settings['comments']   =   (get_comments(array('author_email' => $current_user_email, 'count' => true))) ? get_comments(array('author_email' => $current_user_email, 'count' => true)) : 0;
        $settings['credit_message']     =   ($credited) ? _x('CommentRef plugin by <a href="https://basicblogtalk.com" title="Basic Blog Talk" target="_blank">basicblogtalk.com</a>', 'CommentRefs') : '';
        
        //get setting support for get 10 posts
        $get_ten_posts  = (get_option('crefs_get_ten_posts') && is_array(get_option('crefs_get_ten_posts'))) ? get_option('crefs_get_ten_posts') : '';
        
        //Generate custom message for get 10 post
        if (is_array($get_ten_posts) && isset($get_ten_posts['message'])) {
            
            $get_ten_posts['message'] = __($get_ten_posts['message'] .' can enable ten post in the list', 'CommentRefs');
            
        } else {
            
            $get_ten_posts['message'] = '';
            
        }
        
        //get setting option for get dofollow attribute
        $get_dofollow   = (get_option('crefs_get_dofollow') && is_array(get_option('crefs_get_dofollow'))) ? get_option('crefs_get_dofollow') : '';
        
        if (is_array($get_dofollow) && isset($get_dofollow['message'])) {
            
            $get_dofollow['message'] = __($get_dofollow['message'] .' can get dofollow attribute', 'CommentRefs');
            
        } else {
            
            $get_dofollow['message'] = '';
            
        }
        
        //get setting options for social media integration
        $sm_integration = (get_option('crefs_sm_integration') && is_array(get_option('crefs_sm_integration'))) ? get_option('crefs_sm_integration') : '';
        
        //get setting options for prevent low quality comment
        $prevent_lq_comment = (get_option('crefs_prevent_lq') && is_array(get_option('crefs_prevent_lq'))) ? get_option('crefs_prevent_lq') : '';
        
        //setup CommentRefs api setting for localize script.
        $api_settings['general'] = $settings;
        $api_settings['get_ten'] = $get_ten_posts;
        $api_settings['get_dofollow'] = $get_dofollow;
        $api_settings['sm_integration'] = $sm_integration;
        $api_settings['prevent_lqc'] = $prevent_lq_comment;
        
        //enqeueu frontend css
		wp_enqueue_style('commentrefs-style', $this->plugin_url .'assets/css/commentrefs-style.css');
        
        //enqeueue frontend js
		wp_enqueue_script('commentrefs-script', $this->plugin_url .'assets/js/comment-refs.js', array(), '', true);
        
        //pass local script to frontend js handle
        wp_localize_script('commentrefs-script', 'crefs_api_url', $api_settings);

	}
    
    //enqeueue JS and CSS in Admin area
    public function admin_enqueue(){
        
        //enqeueue CSS
        wp_enqueue_style('commentrefs-admin-style', $this->plugin_url .'assets/css/admin.style.css');
        
        //enqeueue JS
        wp_enqueue_script('commentrefs-admin-script', $this->plugin_url .'assets/js/admin.script.js', array(), '', true);   
        
        //local script variable for admin ajax
        wp_localize_script('commentrefs-admin-style', 'cref_admin_ajax_url', array('url', get_site_url()));
    }
    
}