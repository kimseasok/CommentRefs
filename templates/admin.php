<div id="commentrefs_settings" class="wrap">
    
    <h1>CommentRefs Setting Options</h1>
    
    <?php settings_errors(); ?>
    
    <form method="post" action="options.php">
        
        <?php settings_fields('comment_refs_option_group'); ?>
        
        <?php do_settings_sections('commentrefs'); ?>
        
        <?php submit_button(); ?>
        
    </form>

</div>