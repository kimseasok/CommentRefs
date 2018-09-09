<?php
/**
* @package CommentRefs
*/

namespace Inc\Base;

use \Inc\Base\ControlerCrefs;
use \Inc\Api\SanitizeApiCrefs;
    
class CommentCrefs extends ControlerCrefs
{
    
    // Register all services and hook in WordPress Filter and Action Hooks
     
	public function register(){
        
        /**
         * Check for compatible WordPress version, and
         * Disable CommentRefs plugin if WordPress version is lower than 3.5
         *
         * @since 1.0.0
         *
         */
        
        add_action('admin_init', array($this, 'unsupportedWordPressVersion'));
        
        /**
         * Disable CommentRef plugin if CommentLuv was ready installed
         *
         * @since 1.0.0
         *
         */        
        
        add_action('admin_init', array($this, 'commentluvReadyInstalled'));
        
        /**
         * Add CommentRefs metas data when user post a comments
         *
         * @since 1.0.0
         *
         * Param WP_Comment $comment_ID 
         * Param WP_Comment $comment_approved
         */  
        
        add_action('comment_post', array($this, 'addCommentMetas'), 10, 2);
        
        /**
         * Delete CommentRefs associated metas data from database when user delete comment
         *
         * @since 1.0.0
         *
         * Param WP_Comment $comment_ID 
         *
         */ 
        
        add_action('delete_comment', array($this, 'deleteCommentMetas'), 10, 1);
        
        /**
         * Delete CommentRefs associated metas data from database when user delete comment
         *
         * @since 1.0.0
         *
         * Param    String      $location       String URL for redirect location
         * Param    WP_Comment  $commentdata    Comment object
         *
         */ 
        
        add_filter('comment_post_redirect', array($this, 'redirectFirstComment'), 10, 2);
        
        /**
         * Generate HTML template for CommentRefs data and add it underneath comment
         *
         * @since 1.0.0
         *
         * Param    WP_Comment  $comments    Comment object
         * Return   WP_Comment  $comments    Comment object
         */
        
        add_filter('comments_array', array($this, 'addCommentRefsLinks'), 10, 1);
        add_filter('comment_text', array($this, 'addCommentRefsLinks'), 10, 1);
        
        /**
         * Add remove CommentRef action in comment page in admin area
         *
         * @since 1.0.0
         *
         * Param Array      $action     Array action links
         * Param WP_Comment $comments   Comment object
         * Retrun Array     $action     Array actions links
         *
         */
        
        add_filter('comment_row_actions', array($this, 'addRemoveCommentActionLink'), 10, 2);
        
        /**
         * Handle Ajax remove CommentRefs metas data when user click on remove CommentRefs action link
         *
         * @since 1.0.0
         *
         */
        
        add_action('wp_ajax_crefs_remove_comment_refs', array($this, 'removeCommentRefsMetasAjax'));
        
        /**
         * Handle count comment ajax for none login user.
         * Use for check if user can get 10 posts in front end
         *
         * @since 1.0.0
         *
         */
        
        add_action('wp_ajax_nopriv_crefs_get_comment_count', array($this, 'handleGetCommentCount'));
        
        /**
         * Check if the comment contain link or less words.
         * If so, call wp_die().
         *
         * @since 1.0.0
         *
         * Param    WP_Comment  $comments    Comment object
         * Return   WP_Comment  $comments    return comment object when successful
         *
         */
        
        add_filter('pre_comment_content', array($this, 'filterLowQualityComment'), 10, 1);
        
        /**
         * Import CommentLuv metas data when the setting is turned on.
         *
         * @since 1.0.0
         *
         * Param mix $old_value Old option.
         * Param mix $value     New option
         * Param string $option Option name
         *
         */
        
        add_action('update_option_crefs_miscellaneous', array($this, 'importCommentLuvData'), 10, 3);
        
	}
    
    /**
     * Helper method for generate custom message
     * Get replacable message and return string format
     *
     * @since 1.0.0
     *
     * Param string $comment_id string comment ID.
     * Return boolean
     *
     */
    
    public function isUserCanGetDofollow ($comment_id) {
        
        $get_do = (get_option('crefs_get_dofollow')) ? get_option('crefs_get_dofollow') : '';
        
        $comment_author_email = (get_comment_author_email($comment_id)) ? get_comment_author_email($comment_id) : '';
        
        $user = (get_user_by('email', $comment_author_email)) ? get_user_by('email', $comment_author_email) : '';
        
        // return true is Admin
        if (!empty($user) && user_can($user, 'editable_roles')) {
            
            return true;
            
        }
        
        //return true if everyone is set to on
        if(isset($get_do['everyone']) && $get_do['everyone'] == 'on') {
            
            return true;
            
        }
        
        //check if register is turned on
        if (isset($get_do['registered_the_site']) && $get_do['registered_the_site'] == 'on') {
            
            //if user logged in and user has id return true
            if (is_user_logged_in() && $user) {
                
                return true;
                
            }
            
        }
        
        // Check if post author can get dofollow
        if (isset($get_do['post_author']) && $get_do['post_author'] == 'on') {
            
            //Get author id of current post
            $author_id = (get_post_field('post_author', get_the_ID())) ? get_post_field('post_author', get_the_ID()) : '';
            
            //get user object by comment email
            $commentator = (get_user_by('email', $comment_author_email)) ? get_user_by('email', $comment_author_email) : '';
            
            $commentator_id = ($commentator->ID) ? $commentator->ID : '';
            
            //return true if commentator user_id = author user_id
            if((!empty($author_id) || !empty($author_id)) && $author_id == $commentator_id) {
                
                return true;
                
            }
            
        }
        
        // check if has previous comment is turn on
        if (isset($get_do['has_previous_comments']) && $get_do['has_previous_comments'] == 'on') {
            
            $minimum_comments = ($get_do['minimum_comments']) ? $get_do['minimum_comments'] : '';
            
            $comment_count = get_comments(array(
                'author_email'  =>  $comment_author_email,
                'count'         => true,
            ));
            
            if ((!empty($minimum_comments) || !empty($comment_count)) && $minimum_comments <= $comment_count) {
                
                return true;
                
            }
            
        }
        
        //check if share post is turned on
        if (isset($get_do['shared_the_post']) && $get_do['shared_the_post'] == 'on') {
            
            $crefs_metas = (get_comment_meta($comment_id, 'comment_refs_metas')) ? get_comment_meta($comment_id, 'comment_refs_metas') : '';
            
            $shared_platform = (is_array($crefs_metas) && isset($crefs_metas[0]['shared_on'])) ? $crefs_metas[0]['shared_on'] : '';
            
            //return true if user shared the post
            if(is_array($shared_platform) && !empty($shared_platform)) {
                
                return true;
            }
            
        }
        
        return false;
    }
    
    // Compare current WordPress version with require version (3.5).
    public function unsupportedWordPressVersion() {
        
        $require_wp_version = '3.5';
        
        $current_wp_version = (get_bloginfo('version')) ? get_bloginfo('version') : '';
        
        if(!empty($current_wp_version) && version_compare($current_wp_version, $require_wp_version) < 0) {
            
            // require plugin.php if deactivate_plugin not exists.
            if (!function_exists('deactivate_plugins')) {
                
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                
            }
            
            // Deactive CommentRefs plugin.
            deactivate_plugins('commentrefs/commentrefs.php', true);
            
            // Display error message for unsupported WordPress version
            add_action('admin_init', array($this, 'displayUnsupportedWordPressVersion'));
            
        }
        
    }
    
    // Deactive CommentRefs plugin is CommentLuv is active
    public function commentluvReadyInstalled() {
        
        // require plugin.php if deactivate_plugin not exists
        if(!function_exists('deactivate_plugins')) {
            
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            
        }
        
        // Deactive CommentRefs plugin if CommentLuv is active
        if (is_plugin_active('commentluv/commentluv.php')) {
            
            deactivate_plugins('commentrefs/commentrefs.php', true);
            
            add_action('admin_notices', array($this, 'displayCommentLuvReadyInstalled'));
            
        }
        
    }
    
    // Generate error message for unsupported WordVersion
    public function displayUnsupportedWordPressVersion() {
        
    ?>
        <div class="error notice-error is-dismissible">
            <p><?php _e( 'CommentRefs plugin requires at least WordPress version 3.5', 'commentrefs' ); ?></p>
        </div>
    <?php
        
    }
    
    //Generate error message for CommentLuv plugin was ready installed
    public function displayCommentLuvReadyInstalled() {
        
    ?>
        <div class="error notice-error is-dismissible">
            <p><?php _e( 'To avaid conflig, you should disable CommentLuv plugin before activate CommentRefs', 'commentrefs' ); ?></p>
        </div>
    <?php
        
    }
    
    //Add CommentRefs metas data to the database when user post a comment.
    public function addCommentMetas($comment_ID, $comment_approved) {        
        
        if (!isset($_POST['enable_commentrefs']) || !isset($_POST['comment_refs_metas'])) {
            return;
            
        }
        
        $crefs_wpnone = (isset($_POST['comment_refs_wponce'])) ? sanitize_text_field($_POST['comment_refs_wponce']) : '';
        $crefs_metas = (isset($_POST['comment_refs_metas'])) ? $_POST['comment_refs_metas'] : '';
        
        // validate and sanitize CommentRefs data
        if(is_array($crefs_metas) && !empty($crefs_metas)) {
            
            foreach($crefs_metas as $k => $v){
                
                switch ($k) {
                    
                    case 'title':
                        $crefs_metas["{$k}"] = sanitize_text_field($v);
                        break;
                        
                    case 'url':
                        $crefs_metas["{$k}"] = (wp_http_validate_url($v)) ? esc_url_raw($v) : '';
                        break;
                        
                    case 'shared_on':
                        
                        if(is_array($crefs_metas["{$k}"])) {
                            
                            $crefs_metas["{$k}"] = array_map('sanitize_text_field', $crefs_metas["{$k}"]);
                            
                        } else {
                            
                            $crefs_metas["{$k}"] = sanitize_text_field($v);
                        }
                        
                        break;
                        
                }
                
            }
            
        }
        
        // Add CommentRefs meta data if verify_nonce is true and comment is not spam.
        if(wp_verify_nonce($crefs_wpnone, 'CommentRefs') && $comment_approved !== 'spam') {
            
            $added_comment = add_comment_meta($comment_ID, 'comment_refs_metas', $crefs_metas);

        }
        

    }
    
    //Delete CommentRefs meta data from database when user delete comment
    public function deleteCommentMetas($comment_ID){
        
        if (!empty($comment_ID)) {
            
            $delete = delete_comment_meta($comment_ID, 'comment_refs_metas');
            
        }
        
    }
    
    // Redirect the first commentator to custom page or URL.
    public function redirectFirstComment($location, $commentdata) {
        
        if(!isset($commentdata) && !isset($commentdata->comment_author_email)) {
            
            return $location;
            
        };
        
        //get setting options from CommentRefs setting.
        $comment_redirect = get_option('crefs_comment_redirect') ? get_option('crefs_comment_redirect') : '';
        
        
        if (is_array($comment_redirect) && isset($comment_redirect['enabled'])) {
            
            if ($comment_redirect['enabled'] == 'on' && !empty($comment_redirect['redirect_to'])) {
                
                //Count user comments base on provided email.
                $comment_count = get_comments(array(
                    'author_email'  => $commentdata->comment_author_email,
                    'count'         => true,
                ));
                
                // Redirect commentator to custom page if has no more than 1 comment
                if ($comment_count == 1) {
                    
                    $redirect_to = absint($comment_redirect['redirect_to']);
                    
                    $redirect_page = (get_page_link($redirect_to)) ? get_page_link($redirect_to) : '';
                     
                    $location = (empty($redirect_page)) ? $location : $redirect_page;
                    
                }
                
                //Check if custom URL is set, override redirect location.
                if ($comment_redirect['enabled'] == 'on' && !empty($comment_redirect['custom_url'])) {
                    
                    $custom_url = (wp_http_validate_url($comment_redirect['custom_url'])) ? esc_url_raw($custom_url) : '';
                    
                    $location = (empty($custom_url)) ? $location : $custom_url;
                }
                
            }
             
        }
        
        return $location;
    }
    
    //Generate HTML template and add CommentRef link underneath comment data
    public function addCommentRefsLinks($comments) {
        
        //Make comments object is not empty
        if (is_array($comments) && !empty($comments)) {
            

            
            //Loop in comments object and generate HTML template for CommentRefs
            foreach($comments as $comment){
                
                //Get CommentRefs metas data base on Comment id
                $crefs_metas = (get_comment_meta($comment->comment_ID, 'comment_refs_metas')) ? get_comment_meta($comment->comment_ID, 'comment_refs_metas') : '';
                $crefs_metas = (is_array($crefs_metas) && isset($crefs_metas[0])) ? $crefs_metas[0] : '';
                
                //Generate HTML template for CommentRefs and append to comment data
                if(!empty($crefs_metas)) {
                    
                    //Get replacable message from CommentRefs setting
                    $message = (get_option('crefs_post_type')) ? get_option('crefs_post_type') : '';
                    $message = (is_array($message) && isset($message['message'])) ? $message['message'] : '';
                    
                    //Generate nofollow link related if the commentator cannot get dofollow
                    $link_rel = ($this->isUserCanGetDofollow($comment->comment_ID)) ? '' : 'rel="nofollow"';
                    
                    $crefs_title = (isset($crefs_metas['title']) && !empty($crefs_metas['title'])) ? esc_html($crefs_metas['title']) : '';
                    $crefs_url = (isset($crefs_metas['url']) && !empty($crefs_metas['url'])) ? esc_url_raw($crefs_metas['url']) : '';
                    
                    //add CommentRefs linked underneath comment content if title and url is not empty
                    if (!empty($crefs_title) && !empty($crefs_url)) {
                        
                        $link = '<p id="crefs-comment-' .esc_attr($comment->comment_ID) . '" class="crefs-link-wrap"><span class="crefs-message">' .esc_html($comment->comment_author) .' ' .esc_html($message) .'</span><span class="crefs-meta-content"><a href="' .$crefs_url .'" title="' .$crefs_title . '" ' .$link_rel .' data-comment-id="comment-' . $comment->comment_ID . '">' .strtolower($crefs_title) . '</a></span></p>';

                        $comment->comment_content .= wp_kses_post($link);
                        
                    }
                    
                }
                
            }
            
        } else {
            
            //Check if in the comment page in admin area
            if (is_admin()) {
                
                //Get CommentRefs meta data base on the comment id
                $crefs_metas = (get_comment_meta(get_comment_ID(), 'comment_refs_metas')) ? get_comment_meta(get_comment_ID(), 'comment_refs_metas') : '';
                $crefs_metas = (is_array($crefs_metas) && isset($crefs_metas[0])) ? $crefs_metas[0] : '';
                
                // Generate HTML template and append to comment data if any
                if(!empty($crefs_metas)) { 
                    
                    //Get replacable message from CommentRefs setting
                    $message = (get_option('crefs_post_type')) ? get_option('crefs_post_type') : '';
                    $message = (is_array($message) && isset($message['message'])) ? $message['message'] : '';
                    
                    //Generate nofollow link related if the commentator cannot get dofollow
                    $link_rel = ($this->isUserCanGetDofollow(get_comment_ID())) ? '' : 'rel="nofollow"';
                    
                    $crefs_title = (isset($crefs_metas['title']) && !empty($crefs_metas['title'])) ? esc_html($crefs_metas['title']) : '';
                    $crefs_url = (isset($crefs_metas['url']) && !empty($crefs_metas['url'])) ? esc_url_raw($crefs_metas['url']) : '';
                    
                    $link = '<p id="crefs-comment-' .esc_attr(get_comment_ID()) .'" class="crefs-link-wrap"><span class="crefs-mata-title">' .esc_html(get_comment_author()) .esc_html($message) .' </span><span class="crefs-meta-content"><a id="comment-link-id-' .esc_attr(get_comment_ID()) . '" href="' .$crefs_url .'" title="' .esc_attr($crefs_title) . '">' .$crefs_title . '</a><img id="comment-loading-id-' .esc_attr(get_comment_ID()) .'" src="' .esc_url($this->plugin_url .'/assets/images/loading-bar-64px.gif') . '"></span></p>';

                    $comments .= wp_kses_post($link);
                    
                }
                
            }
            
        }
        
        return $comments;
        
    }
    
    //Generate HTML template for remove CommentRefs and add to Comment action links
    public function addRemoveCommentActionLink ($actions, $comment) {
        
        //Add remove CommentRefs action link if you can delete comment post
        if(current_user_can('delete_post', $comment->comment_post_ID)) {
            
            $comment_refs_meta = (get_comment_meta($comment->comment_ID, 'comment_refs_metas')) ? get_comment_meta($comment->comment_ID, 'comment_refs_metas') : '';
            
            if(!empty($comment_refs_meta)){
                
                $nonce = wp_create_nonce('RemoveCommentRefs' .$comment->comment_ID);
                
                $actions['remove_refs'] = '<a href="#" class="crefs_remove_link" data-comment-id="' .esc_attr($comment->comment_ID) .'" data-nonce="' .esc_attr($nonce) .'">Remove Refs</a>';
            
            }
            
        }        
        
        return $actions;
        
    }
    
    //Handle remove comment link Ajax when user click on Remove CommentRefs action link
    public function removeCommentRefsMetasAjax () {
        
        $nonce = (isset($_POST['nonce'])) ? sanitize_text_field($_POST['nonce']) : '';
        
        $comment_id = (isset($_POST['comment_id'])) ? absint($_POST['comment_id']) : '';
        
        // Make sure verify_nonce is true
        if (!empty($comment_id) && wp_verify_nonce($nonce, 'RemoveCommentRefs' .$comment_id)) {
            
            $deleted = delete_comment_meta($comment_id, 'comment_refs_metas');
            
            //Send json data if fail or success.
            if ($deleted) {
                
                $comment = array(
                    'comment_id' => $comment_id
                );
                
                wp_send_json_success($comment);
                
            } else {
                
                $comment = array(
                    'comment_id' => $comment_id
                );
                
                wp_send_json_error($comment);
                
            }
                
        } else {
            
            wp_send_json_error();
            
        }
        
        wp_die();
        
    }
    
    //Handle ajax get comment count action.
    public function handleGetCommentCount() {
        
        $wp_nonce = (isset($_GET['wp_nonce'])) ? sanitize_text_fields($_GET['wp_nonce']) : '';
        
        $author_email = (isset($_GET['author_email'])) ? sanitize_email($_GET['author_email']) : '';
        
        //Make sure comment author email and wp_nonce is not empty.
        if (!empty($author_email) && !empty($wp_nonce)) {
            
            //check verify_nonce before processing query
            if (wp_verify_nonce($wp_nonce, 'CommentRefs') && is_email($author_email)) {
                
                $args = array (
                    'author_email' => $author_email,
                    'count'=> true
                );
                
                $comments = get_comments($args);
                
                //send json comment count if success
                if (!empty($comments)) {
                    
                    wp_send_json_success(array('previus_comments' => $comments));
                    
                }
                
            }
            
        } else {
            
            wp_send_json_error();
            
        }
        
        wp_die();
        
    }
    
    //Filter low quality comment and call wp_die()
    public function filterLowQualityComment ($comment) {
    
        $options = (get_option('crefs_prevent_lq')) ? get_option('crefs_prevent_lq') : '';
        
        $prevent_link_in_comment = (is_array($options) && isset($options['prevent_link_in_comment'])) ? $options['prevent_link_in_comment'] : '';

        $prevent_short_comment = (is_array($options) && isset($options['prevent_short_comment'])) ? $options['prevent_short_comment'] : '';
        
        $minimum_comment_length = (is_array($options) && isset($options['minimum_length'])) ? $options['minimum_length'] : '';
        
        //check if prevent link in comment is turned on.
        if ($prevent_link_in_comment == 'on') {
            
            $re = '/<a.+>/m';
            $str = $comment;

            preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
            
            // call wp_die() when found link in comment
            if (is_array($matches) && !empty($matches)) {

                wp_die(__('The comment did not pass quality filtering'));

            }
            
        }
        
        //Check if prevent short comment is turned on.
        if ($prevent_short_comment == 'on') {
            
            $comment_count = (str_word_count($comment)) ? str_word_count($comment) : 0;
            
            if(!empty($options['minimum_length'])) {
                
                $minimum_length = absint($options['minimum_length']);
                
                // call wp_die if the comment length is less than minimum comment length
                if ($minimum_length > $comment_count) {
                    
                    wp_die(__('The comment did not pass the quality filtering'));
                    
                }
                
            }
            
        }
                
        return $comment;
        
    }
    
    //Import CommentLuv metas data to CommentRefs plugin if setting option is set to on
    public function importCommentLuvData($old_value, $value, $option) {
        
        if (!isset($value['import_data_from_commentluv']) && $value['import_data_from_commentluv'] !== 'on') {
            return;
        }
        
        if (!isset($value['imported']) || $value['imported'] != true) {
            
            $comments = (get_comments(array('meta_key' => 'cl_data'))) ? get_comments(array('meta_key' => 'cl_data')) : '';
            
            if(!empty($comments)) {
                
                foreach ($comments as $comment) {

                    $commentluv_data = (get_comment_meta($comment->comment_ID, 'cl_data')) ? get_comment_meta($comment->comment_ID, 'cl_data') : '';
                    
                    if (is_array($commentluv_data) && !empty($commentluv_data)) {
                        
                        $commentrefs_data['title']  = isset($commentluv_data[0]['cl_post_title']) ? sanitize_text_field($commentluv_data[0]['cl_post_title']) : '';
                        $commentrefs_data['url']    = ($commentluv_data[0]['cl_post_url']) ? esc_url_raw($commentluv_data[0]['cl_post_url']) : '';
                        
                        $commentrefs_meta = add_comment_meta($comment->comment_ID, 'comment_refs_metas', $commentrefs_data, true);
                        
                    }

                }
                
                //Mark as imported
                if (is_array($value) && !isset($value['imported'])) {
                    
                    $value['imported'] = true;
                    update_option($option, $value);
                    
                }
                
            }
            
        }
        
    }
    
}