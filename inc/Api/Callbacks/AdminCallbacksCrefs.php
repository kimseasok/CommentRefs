<?php
/**
* @package CommentRefs
*/

namespace Inc\Api\Callbacks;
use \Inc\Base\ControlerCrefs;

class AdminCallbacksCrefs extends ControlerCrefs
{
    
    /**
     * Helper method for generate HTML tempate for toggle icon
     *
     * Param    String  $id     String id of the setting option
     * Param    String  $check  String check attribute of the checkbox input
     * Return   void
     *
     */
    
    public function generateToggle($id, $checked) {
        
        $is_off = (empty($checked)) ? 'off' : '';
        
?>

        <div class="toggle-icon <?php echo esc_attr($is_off) ?>" roll="button" data-id="<?php echo esc_attr($id) ?>">
            
            <span class="ball-icon"></span>
            
        </div>

<?php
            
    }
    
    /**
     * Helper method for generte HTML template for toggle checkbox input
     *
     * Param    Array   $options    Array setting options of the setting field
     * Param    String  $fields     String field id of the setting field
     * Param    String  $classes    String classes of the checkbox input
     * Param    Array   $excludes   Array options that need to be exludes from generating
     * Return   void
     *
     */
    
    public function generateCheckbox($options, $field, $classes = '', $excludes = array()) {
        
        $has_options = (is_array(get_option($field)) && !empty(get_option($field))) ? get_option($field) : '';
        
        //Make sure the options is array and not empty
        if(is_array($options) && !empty($options)){
            
            // exclude the options from generating HTML input
            if(is_array($excludes) && !empty($excludes)){
                
                $options = array_diff($options, $excludes);
                
            }
            
            //Loop in option array and genrate HTML inputs
            foreach($options as $k => $option) {
                
                $option = esc_attr($option);
                
                $field = esc_attr($field);
                
                $classes = (isset($classes)) ? esc_attr($classes) : 'inline-options';
                
                $id = esc_attr($field .'_' .$option);
                
                $label = str_replace('_', ' ', $option);
                $label = trim($label, " \t\n\r\0\x0B");
                $label = esc_attr($label);
                
                //print checked attribute if option have previous value
                $checked = (is_array($has_options) && isset($has_options["{$option}"])) ? 'checked' : '';
?>

                <label for="<?php echo $label ?>" class="<?php echo $classes ?>">
                    
                    <?php $this->generateToggle($id, $checked); ?>
                    
                    <input type="checkbox" name="<?php echo $field ?>[<?php echo $option ?>]" id="<?php echo $id ?>" <?php echo $checked ?>> <?php echo esc_html(ucfirst($label)) ?>
                    
                </label>

<?php
                    
            }
            
        } else {
            
            $option = $options;
            
?>
            <label for="<?php echo esc_attr($field .'_' . $option) ?>">
                
                <input type="checkbox" name="<?php echo esc_attr($fild) ?>[<?php echo esc_attr($option) ?>]" id="<?php echo esc_attr($field .'_' . $option) ?>"> <?php echo esc_html(ucfirst($option)) ?>
                
            </label>
<?php
                
        }
        
    }
    
    /**
     * Helper method for generate HTML template for text input
     *
     * Param    Array   $options    Array options ID
     * Param    String  $field      String setting field ID
     * Param    String  $title      String title for the input field
     * Param    String  $desc       String HTML description for the input field.
     * Param    String  $classes    String classes names for the input field
     * Return   void
     *
     */
    
    public function generateInputBox($options, $field, $title='', $desc='', $classes='') {
        
        $has_options = (get_option($field)) ? get_option($field) : '';
        
        foreach($options as $k => $option){
            
            $option = esc_attr($option);
            
            $field = (!empty($field)) ? esc_attr($field) : '';
            
            $title = (!empty($title)) ? esc_html($title) : '';
            
            $classes = (!empty($classes)) ? 'class="' .esc_attr($classes) .'"' : '';
            
            $id = esc_attr($field .' ' .$option);
            
            $saved_option = (isset($has_options["{$option}"])) ? $has_options[$option] : '';
            
            if (!empty($saved_option) && !is_numeric($saved_option)) {
                switch($field) {
                        
                    case 'crefs_post_type':
                        $saved_option = '#Author Name#{' .$saved_option .'} #post title#';
                        break;
                        
                    case 'crefs_get_ten_posts':
                        $saved_option = '{' .$saved_option .'} can get 10 posts in the list';
                        break;
                        
                    case 'crefs_get_dofollow':
                        $saved_option = '{' .$saved_option .'} can get dofollow attribute';
                }
                
            }
            
?>

            <label for="<?php echo $id ?>" class="block-options">
                
                <?php if(!empty($title)) : ?>
                
                    <span class="crefs-option-title"><?php echo $title ?></span>
                
                <?php endif; ?>
            
                <input type="text" name="<?php echo $field ?>[<?php echo $option ?>]" value="<?php echo $saved_option ?>" <?php echo $classes ?>>
                
                <p class="description"><?php echo wp_kses_post($desc) ?></p>
                
            </label>
                
<?php
                
        }
        
    }
    
    /**
     * Helper method for getting replace custom message
     *
     * Param    String  $message    string replacable message
     * Return   String  $message    String clean message
     */
    public function getMessage($message) {
        
        if (empty($message)) {
            return;
        }
        
        $re = '/\{.*\}/';
        
        $str = $message;
        
        preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
        $message = (is_array($matches) && isset($matches[0][0])) ? $matches[0][0] : $message;
        
        $message = str_replace('{', '', $message);
        $message = str_replace('}', '', $message);
        
        return $message;
        
    }
    
    public function pageCallback() {
        
        include_once $this->plugin_path .'templates/admin.php';
        
    }
    
    /**
     * Validate and santize setting options
     *
     * Param    Mix $input
     * Return   Mix $input
     *
     */
    
    public function sanitizeSettingsOptions($input) {
        
        
        if (is_array($input)) {
            
            foreach($input as $option => $value){
                
                switch($option) {
                        
                        //valid text inputs and sanitize it as text
                    case 'message':
                        
                        $value = (!empty($value)) ? $this->getMessage($value) : '';
                        $value = sanitize_text_field($value);
                        $input["{$option}"] = $value;
                        break;
                        
                        //valid int input fields and make sure it none negative int
                    case 'minimum_comments':
                    case 'minimum_length':
                    case 'redirect_to':
                    case 'facebook_app':
                        
                        $input["{$option}"] = absint($value);
                        break;
                        
                        //sanitize url field
                    case 'custom_url':
                        
                        $input{"{$option}"} = (wp_http_validate_url($value)) ? esc_url_raw($value) : '';
                        break;
                        
                    default:
                        //sanitize checkbox input as text and make sure save value is on
                        $input["{$option}"] = sanitize_text_field($value);
                        $input["{$option}"] = ($value === 'on') ? $value : '';
                        
                }

            }
            
        } else {
            
            $input = sanitize_text_field($input);
            
        }
        
        return $input;
        
    }
    
    //Output HTMl description for crefs_posttype section
    public function scPostType() {
        
?>
        <p>Select one ore more post types where you want to enable CommentRefs. <span class="crefs-default"><strong>Default: </strong>Post</span></p>
<?php

    }
    
    //Output HTMl description for crefs_get_ten_posts section
    public function scGetTenPosts() {
        
?>
        <p>Configure who can recieve 10 posts hinted in CommentRefs list.<br>
            <span class="crefs-default"><strong>Default:</strong> only Admin can get 10 posts hinted</span></p>

<?php
        
    }
    
    //Output HTMl description for crefs_get_dofollow section
    public function scGetDofollow() {
        
?>
        <p>Configure who can receive dofollow attribute after leaving comment.<br>
            <span class="crefs-default"><strong>Default:</strong> only admin can get dofollow attribute</span></p>
<?php
        
    }
    
    //Output HTMl description for crefs_sm_integration section
    public function scSocialMediaIntegration(){
        
    }
    
    //Output HTMl description for crefs_first_comment_redirect section
    public function scFirstCommentRedirect() {
        
?>
        <p>Configure redirect behavior for first commentator.<br>
            <span class="crefs-default"><strong>Default:</strong> comment's location</span></p>
<?php
        
    }
    
    //Output HTMl description for crefs_prevent_low_quality section
    public function scPreventLowQaulityComment() {
        
?>
        <p>Configure following settings to prevent link builder and low qaulity comment</p>
<?php
        
    }
    
    //Output HTML content for Post Type setting options
    public function fcPostType() {
        
        $args = array(
            
            'public'    =>  true,
            'show_ui'   =>  true,
            
        );
        
        $options = get_post_types($args);
        
        //Generate HTML template of toggle checkbox options for all custom post types excepted attachment
        $this->generateCheckbox($options, 'crefs_post_type', '', array('attachment'));
        
        //Generate HTML template of custom message
        $this->generateInputBox(array('message'), 'crefs_post_type', 'Custom Message','<strong>Replace the text inside the bracket</strong> with you custom message. The custom message will display under each comment.', 'regular-text code');
        
    }
    
    //Output HTML content for Post Type setting options
    public function fcGetTenPosts() {
        
        $options = array('everyone', 'registered_the_site', 'post_author', 'shared_the_post', 'has_previous_comments');
        
        //Generate HTML template of toggle checkbox for get_ten_posts
        $this->generateCheckbox($options, 'crefs_get_ten_posts', 'block-options');
        
        //Generate HTML template of input box for minimum comment length
        $this->generateInputBox(array('minimum_comments'), 'crefs_get_ten_posts', '', 'Set number of comments the commentator should has', 'small-text');
        
        //Generate HTML template of input box for custom message
        $this->generateInputBox(array('message'), 'crefs_get_ten_posts', 'Custom Message', '<strong>Replace the text inside the brackets</strong> with your custom message. The message will display under CommentRefs\' list when needed.', 'regular-text code');
        
    }
    
    //Output HTML content for Get Dofollow setting options
    public function fcGetDofollow() {
        
        $options = array('everyone', 'registered_the_site', 'post_author', 'shared_the_post', 'has_previous_comments');
        
        //Generate HTML template of toggle checkbox for get_dofollow
        $this->generateCheckbox($options, 'crefs_get_dofollow', 'block-options');
        
        //Generate HTML template of input box for minimum comment length
        $this->generateInputBox(array('minimum_comments'), 'crefs_get_dofollow', '', 'Set number of comments the commentator should has', 'small-text');
        
        //Generate HTML template of input box for custom message
        $this->generateInputBox(array('message'), 'crefs_get_dofollow', 'Custom Message','<strong>Replace the text inside the brackets</strong> with your custom message. The message will display under CommentRefs\' list when needed.', 'regular-text code');
        
    }
    
    //Output HTML content for Social Media Integration setting options
    public function fcSocialMediaIntegration(){
        
        //Generate HTML template of Input box for facebook_app setting option
        $this->generateInputBox(array('facebook_app'), 'crefs_sm_integration', 'Facebook App ID', 'Facebook app ID use for generate share dialog box');
        
    }
    
    //Output HTML content for First Comment Redirect setting options
    public function fcCommentRedirect(){
        
        //query all page and generate HTML template of selection options for queried pages.
        $args = array(
            'post_type'     => 'page',
            'post_status'   => 'publish',
        );
        
        $pages = new \WP_Query($args);
        
        $this->generateCheckbox(array('enabled'), 'crefs_comment_redirect', 'block-options');
        
        $has_options = (!empty(get_option('crefs_comment_redirect'))) ? get_option('crefs_comment_redirect') : '';
        
        $redirect_to = (!empty($has_options['redirect_to'])) ? (int)$has_options['redirect_to'] : '';
        
?>
        <label for="" class="block-options">Select page: 
            <select name="crefs_comment_redirect[redirect_to]">
                <option value="">Redirect to</option>
                <?php while ($pages->have_posts()) : $pages->the_post(); ?>
                
                <?php $selected = ($redirect_to == get_the_ID()) ? 'selected' : ''; ?>
                <option value="<?php the_ID() ?>" <?php echo $selected ?>><?php the_title()?></option>
                <?php endwhile; wp_reset_postdata();?>
            </select>
        </label>

<?php
        
        //Generate HTML template of input box for custom URL
        $this->generateInputBox(array('custom_url'), 'crefs_comment_redirect', 'Custom URL', 'The URL where you want to redirect the first commentator. This option will override the selected page', 'regular-text code');
        
    }
    
    //Output HTML content for Prevent Low Quality Comment setting options
    public function fcPreventLQComment() {
        
        $options = array('prevent_link_in_comment', 'prevent_short_comment');
        
        //Generate HTML template of toggle checkbox for prevent low quality comment
        $this->generateCheckbox($options, 'crefs_prevent_lq', 'block-options');
        
        //Generate HTML template of input box for minimum comment length
        $this->generateInputBox(array('minimum_length'), 'crefs_prevent_lq', '', 'Set minimum word length to be accepted. This option will apply if prevent short is enable', 'small-text');
        
    }
    
    //Output HTML content for Miscellaneous setting options
    public function fcMiscellaneous() {
        
        $options = array('import_data_from_commentluv', 'remove_data_on_uninstall', 'credit_CommentRefs');
        
        //Generate HTML template of toggle checkbox for miscellaneous 
        $this->generateCheckbox($options, 'crefs_miscellaneous', 'block-options');
        
    }
    
}
