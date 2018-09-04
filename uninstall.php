<?php
/**
* @package CommentRefs
*/

/*
 * Delete CommentRefs data on unistall the plugin if remove data option is set.
 */

$setting = get_option('crefs_miscellaneous');

if (is_array($setting) && $setting['remove_data_on_uninstall'] == 'on') {
    
    $comments = get_comments(array('meta_key' => 'comment_refs_metas'));

    foreach ($comments as $comment) {
        
        delete_comment_meta($comment->comment_ID, 'comment_refs_metas');
    }
    
    $options = array('crefs_post_type', 'crefs_get_ten_posts', 'crefs_get_dofollow', 'crefs_sm_integration', 'crefs_comment_redirect', 'crefs_prevent_lq', 'crefs_miscellaneous');
    
    foreach ($options as $option) {
        
        delete_option($option);
        
    }
    
}