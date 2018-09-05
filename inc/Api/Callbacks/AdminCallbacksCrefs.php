<?php
/**
* @package CommentRefs
*/

namespace Inc\Api\Callbacks;
use \Inc\Base\ControlerCrefs;

class AdminCallbacksCrefs extends ControlerCrefs
{
    // Helper method for generating HTML element for toggle button
    
    public function generateToggle($id, $checked) {
        
        $is_off = (!empty($checked)) ? '' : 'off';
        
?>

        <div class="toggle-icon <?php echo $is_off?>" roll="button" data-id="<?php echo $id ?>">
            
            <span class="ball-icon"></span>
            
        </div>

<?php
            
    }
    
    public function generateCheckbox($options, $field, $classes = '', $excludes = array()) {
        
        $has_options = (is_array(get_option($field)) && !empty(get_option($field))) ? get_option($field) : '';
        
        if(is_array($options) && !empty($options)){
            
            if(is_array($excludes) && !empty($excludes)){
                
                $options = array_diff($options, $excludes);
                
            }
            
            
            foreach($options as $k => $option) {
                
                $option = esc_attr($option);
                $field = esc_attr($field);
                $classes = (isset($classes)) ? esc_attr($classes) : 'inline-options';
                
                $id = esc_attr($field .'_' .$option);
                
                $label = str_replace('_', ' ', $option);
                $label = trim($label, " \t\n\r\0\x0B");
                $label = esc_attr($label);
                
                $checked = (is_array($has_options) && isset($has_options["{$option}"])) ? 'checked' : '';
?>

                <label for="<?php echo $label ?>" class="<?php echo $classes ?>">
                    
                    <?php $this->generateToggle($id, $checked); ?>
                    
                    <input type="checkbox" name="<?php echo $field ?>[<?php echo $option ?>]" id="<?php echo $id ?>" <?php echo $checked ?>> <?php echo esc_html(ucfirst(trim($label))) ?>
                    
                </label>

<?php
                    
            }
            
        } else {
            
            $option = $options;
            
?>
            <label for="<?php echo esc_attr($field .'_' . $option) ?>">
                
                <input type="checkbox" name="<?php echo esc_attr($fild) ?>[<?php echo esc_attr($option) ?>]" id="<?php echo esc_attr($field .'_' . $option) ?>"> <?php echo esc_html(ucfirst(trim($option))) ?>
                
            </label>
<?php
                
        }
        
    }
    
    public function generateInputBox($options, $field, $title='', $desc='', $classes='') {
        
        $has_options = (get_option($field)) ? get_option($field) : '';
        
        foreach($options as $k => $option){
            
            $option = esc_attr($option);
            
            $field = (!empty($field)) ? esc_attr($field) : '';
            
            $title = (!empty($title)) ? esc_html($title) : '';
            
            $classes = (!empty($classes)) ? 'class="' .esc_attr($classes) .'"' : '';
            
            $id = esc_attr($field .' ' .$option);
            
            $saved_option = (isset($has_options["{$option}"])) ? $has_options[$option] : '';
            
?>

            <label for="<?php echo $id ?>" class="block-options">
                
                <?php if(!empty($title)) : ?>
                
                    <span class="crefs-option-title"><?php echo $title ?></span>
                
                <?php endif; ?>
            
                <input type="text" name="<?php echo $field ?>[<?php echo $option ?>]" value="<?php echo $saved_option ?>" <?php echo $classes ?>>
                
                <p class="description"><?php echo $desc ?></p>
                
            </label>
                
<?php
                
        }
        
    }
    
    public function pageCallback() {
        
        require_once $this->plugin_path .'templates/admin.php';
        
    }
    
    function settingsCallback($input) {
        
        return $input;
    }
    
    public function scPostType() {
        
?>

        <p>Select one ore more post types where you want to enable CommentRefs.
            
            <span class="crefs-default"><strong>Default:</strong> Post</span>
            
        </p>

<?php
        
    }
    
    public function scGetTenPosts() {
        
?>
        <p>Configure who can recieve 10 posts hinted in CommentRefs list.<br>
            
            <span class="crefs-default"><strong>Default:</strong> only Admin can get 10 posts hinted</span>
            
        </p>

<?php
        
    }
    
    public function scGetDofollow() {
        
?>
        <p>
            
            Configure who can receive dofollow attribute after leaving comment.<br>
            
            <span class="crefs-default"><strong>Default:</strong> only admin can get dofollow attribute</span>
            
        </p>

<?php
        
    }
    
    public function scFirstCommentRedirect() {
        
?>
        <p>
            
            Configure redirect behavior for first commentator.<br>
            
            <span class="crefs-default"><strong>Default:</strong> comment's location</span>
            
        </p>
<?php
        
    }
    
    public function scSocialMediaIntegration(){
        
    }
    
    public function scPreventLowQaulityComment() {
        
?>

        <p>Configure following settings to prevent link builder and low qaulity comment</p>

<?php
        
    }
    
    public function fcPostType() {
        
        $args = array(
            
            'public'    =>  true,
            'show_ui'   =>  true,
            
        );
        
        $options = get_post_types($args);
        
        $this->generateCheckbox($options, 'crefs_post_type', '', array('attachment'));
        
        $this->generateInputBox(array('message'), 'crefs_post_type', 'Custom Message','<strong>Replace the text inside the bracket</strong> with you custom message. The custom message will display under each comment.', 'regular-text code');
        
    }
    
    public function fcGetTenPosts() {
        
        $options = array('everyone', 'registered_the_site', 'post_author', 'shared_the_post', 'has_previous_comments');
        
        $this->generateCheckbox($options, 'crefs_get_ten_posts', 'block-options');
        
        $this->generateInputBox(array('minimum_comments'), 'crefs_get_ten_posts', '', 'Set number of comments the commentator should has', 'small-text');
        
        $this->generateInputBox(array('message'), 'crefs_get_ten_posts', 'Custom Message', '<strong>Replace the text inside the brackets</strong> with your custom message. The message will display under CommentRefs\' list when needed.', 'regular-text code');
        
    }
    
    public function fcGetDofollow() {
        
        $options = array('everyone', 'registered_the_site', 'post_author', 'shared_the_post', 'has_previous_comments');
        
        $this->generateCheckbox($options, 'crefs_get_dofollow', 'block-options');
        
        $this->generateInputBox(array('minimum_comments'), 'crefs_get_dofollow', '', 'Set number of comments the commentator should has', 'small-text');
        
        $this->generateInputBox(array('message'), 'crefs_get_dofollow', 'Custom Message','<strong>Replace the text inside the brackets</strong> with your custom message. The message will display under CommentRefs\' list when needed.', 'regular-text');
        
    }
    
    public function fcSocialMediaIntegration(){
        
       $this->generateInputBox(array('facebook_app'), 'crefs_sm_integration', 'Facebook App ID', 'Facebook app ID use for generate share dialog box');
        
    }
    
    public function fcCommentRedirect(){
        
        $args = array(
            
            'post_type'     => 'page',
            'post_status'   => 'publish',
            
        );
        
        $pages = new \WP_Query($args);
        
        $this->generateCheckbox(array('enabled'), 'crefs_comment_redirect', 'block-options');
        
        $has_options = (!empty(get_option('crefs_comment_redirect'))) ? get_option('crefs_comment_redirect') : '';
        
        $redirect_to = (!empty($has_options['redirect_to'])) ? (int)$has_options['redirect_to'] : '';
        
?>
        <label for="" class="block-options">
            
            Select a preferred page.<br>
            
            <select name="crefs_comment_redirect[redirect_to]">
                
                <option value="">Redirect to</option>
                
                <?php while ($pages->have_posts()) : $pages->the_post(); ?>
                
                <?php $selected = ($redirect_to == get_the_ID()) ? 'selected' : ''; ?>
                
                <option value="<?php the_ID() ?>" <?php echo $selected ?>><?php the_title()?></option>
                
                <?php endwhile; wp_reset_postdata();?>
                
            </select>
            
        </label>

<?php
        
        $this->generateInputBox(array('custom_url'), 'crefs_comment_redirect', 'Custom URL', 'The URL where you want to redirect the first commentator. This option will override the selected page', 'regular-text code');
        
    }
    
    public function fcPreventLQComment() {
        
        $options = array('prevent_link_in_comment', 'prevent_short_comment');
        
        $this->generateCheckbox($options, 'crefs_prevent_lq', 'block-options');
        
        $this->generateInputBox(array('minimum_length'), 'crefs_prevent_lq', '', 'Set minimum word length to be accepted. This option will apply if prevent short is enable', 'small-text');
        
    }
    
    public function fcMiscellaneous() {
        
        $options = array('import_data_from_commentluv', 'remove_data_on_uninstall', 'credit_CommentRefs');
        
        $this->generateCheckbox($options, 'crefs_miscellaneous', 'block-options');
        
    }
    
}
