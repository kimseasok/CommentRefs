<?php
/*
 * @package CommentRefs
 */
namespace Inc\Base;
use \Inc\Base\ControlerCrefs;

class EnqueueCrefs extends ControlerCrefs
{
	public function register(){
        
		add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
        
	}
	
    public function getMessage($message) {
        
        $re = '/\{.*\}/';
        $str = $message;
        preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
        $message = substr($matches[0][0], 0, -1);
        $message = substr($message, 1);
        
        return $message;        
    }
    
	public function enqueue(){
        
        $post_type = get_post_type();
        $allowed_post_types = get_option('crefs_post_type');
        $is_allowed = (is_array($allowed_post_types) && isset($allowed_post_types[$post_type])) ? $allowed_post_types[$post_type] : 'off';
        
        if(get_option('default_comment_status') !== 'open' or $is_allowed !== 'on') {
            
            return;
            
        }
        
        
        $user = get_current_user_id();
        $user_email = get_the_author_meta('email', $user);
        $miscellaneous = get_option('crefs_miscellaneous');
        $credited = (is_array($miscellaneous) && $miscellaneous['credit_CommentRefs'] == 'on') ? true : false;
        
        $settings['plugin_url'] =   $this->plugin_url;
        $settings['title']      =   get_the_title(get_the_ID());
        $settings['permalink']  =   get_permalink(get_the_ID());
        $settings['wponce']     =   wp_create_nonce('CommentRefs');
        $settings['website']    =   get_the_author_meta('user_url', $user);
        $settings['loggedin']   =   (is_user_logged_in()) ? true : false;
        $settings['is_admin']   =   (current_user_can('switch_themes')) ? true : false;
        $settings['is_author']  =   ($user == get_post_field('post_author', get_the_ID())) ? true : false;
        $settings['comments']   =   ($user) ? get_comments(array('author_email' => $user_email, 'count' => true)) : 0;
        $settings['credit_message']     =   ($credited) ? __('CommentRef plugin by <a href="https://basicblogtalk.com" title="Basic Blog Talk" target="_blank">basicblogtalk.com</a>', 'CommentRefs') : '';
        
        $get_ten_posts  = (is_array(get_option('crefs_get_ten_posts'))) ? get_option('crefs_get_ten_posts') : '';
        
        if (isset($get_ten_posts['message'])) {
            
            $get_ten_posts['message'] = __($this->getMessage($get_ten_posts['message']) .' can enable ten post in the list', 'CommentRefs');
            
        }
        
        $get_dofollow   = (is_array(get_option('crefs_get_dofollow'))) ? get_option('crefs_get_dofollow') : '';
        
        if (isset($get_dofollow['message'])) {
            
            $get_dofollow['message'] = __($this->getMessage($get_dofollow['message']) .' can get dofollow attribute', 'CommentRefs');
            
        }
        
        $sm_integration = (is_array(get_option('crefs_sm_integration'))) ? get_option('crefs_sm_integration') : '';
        $prevent_lq_comment = (is_array(get_option('crefs_prevent_lq'))) ? get_option('crefs_prevent_lq') : '';
        
		wp_enqueue_style('commentrefs-style', $this->plugin_url .'assets/css/commentrefs-style.css');
		wp_enqueue_script('commentrefs-script', $this->plugin_url .'assets/js/comment-refs.js', array(), '', true);
        
        $api_settings['general'] = $settings;
        $api_settings['get_ten'] = $get_ten_posts;
        $api_settings['get_dofollow'] = $get_dofollow;
        $api_settings['sm_integration'] = $sm_integration;
        $api_settings['prevent_lqc'] = $prevent_lq_comment;
        
        wp_localize_script('commentrefs-script', 'crefs_api_url', $api_settings);

	}
    
    public function admin_enqueue(){
        
        wp_enqueue_style('commentrefs-admin-style', $this->plugin_url .'assets/css/admin.style.css');
        wp_enqueue_script('commentrefs-admin-script', $this->plugin_url .'assets/js/admin.script.js', array(), '', true);   
        
        wp_localize_script('commentrefs-admin-style', 'cref_admin_ajax_url', array('url', get_site_url()));
    }
    
}