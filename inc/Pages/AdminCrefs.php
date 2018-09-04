<?php
/*
 * @package CommentRefs
 */
namespace Inc\Pages;
use \Inc\Base\ControlerCrefs;
use \Inc\Api\SettingsApiCrefs;
use \Inc\Api\Callbacks\AdminCallbacksCrefs;

class AdminCrefs extends ControlerCrefs
{
	public $settings;
	public $callbacks;
	
	public function __construct(){
        
		$this->callbacks = new AdminCallbacksCrefs();
		$this->settings = new SettingsApiCrefs();
        
	}
	
	public function register(){
        
		$pages = array(
            
			array(
                
				'page_title'	=> 'Comment Refs Setting Options',
				'menu_title'	=> 'Comment Refs',
				'capability'	=> 'manage_options',
				'menu_slug'		=> 'commentrefs',
				'callback' 		=> array($this->callbacks, 'pageCallback'),
				'icon_url'		=> 'dashicons-format-status',
				'position'		=> 110
			),
            
		);
        
        $this->setSettings();
        $this->setSections();
        $this->setFields();
		$this->settings->AddPages($pages)->register();

	}
    
    public function setSettings() {
        
        $args = array(
            
            array(
                
                'option_group'  => 'comment_refs_option_group',
                'option_name'   => 'crefs_post_type',
                //'callback'      => array($this->callbacks, 'settingsCallback'),
                
            ),
            
            array(
                
                'option_group'  => 'comment_refs_option_group',
                'option_name'   => 'crefs_get_ten_posts',
                //'callback'      => array($this->callbacks, 'settingsCallback'),
                
            ),
            
            array(
                
                'option_group'  => 'comment_refs_option_group',
                'option_name'   => 'crefs_get_dofollow',
                //'callback'      => array($this->callbacks, 'settingsCallback'),
                
            ),
            
            array(
                
                'option_group'  => 'comment_refs_option_group',
                'option_name'   => 'crefs_sm_integration',
                //'callback'      => array($this->callbacks, 'settingsCallback'),
                
            ),
            
            array(
                'option_group'  => 'comment_refs_option_group',
                'option_name'   => 'crefs_comment_redirect',
                //'callback'      => array($this->callbacks, 'settingsCallback'),
                
            ),
            
            array(
                
                'option_group'  => 'comment_refs_option_group',
                'option_name'   => 'crefs_prevent_lq',
                //'callback'      => array($this->callbacks, 'settingsCallback'),
                
            ),
            
            array(
                
                'option_group'  => 'comment_refs_option_group',
                'option_name'   => 'crefs_miscellaneous',
                //'callback'      => array($this->callbacks, 'settingsCallback'),
                
            ),
            
        );
        
        $this->settings->setSettings($args);
        
    }

    public function setSections() {
        
        $args = array(
            
            array(
                
                'id'        => 'crefs_post_type_section',
                'title'     => 'Enable CommentRefs',
                'callback'  => array($this->callbacks, 'scPostType'),
                'page'      => 'commentrefs',
                
            ),
            
            array(
                
                'id'        => 'crefs_get_ten_posts_section',
                'title'     => 'Get 10 posts in the list',
                'callback'  => array($this->callbacks, 'scGetTenPosts'),
                'page'      => 'commentrefs',
                
            ),
            
            array(
                'id'        => 'crefs_get_dofollow_section',
                'title'     => 'Get DOFOLLOW attribute',
                'callback'  => array($this->callbacks, 'scGetDofollow'),
                'page'      => 'commentrefs',
                
            ),
            
            array(
                
                'id'        => 'crefs_sm_integration_section',
                'title'     => 'Social Media Integration',
                'callback'  => array($this->callbacks, 'scSocialMediaIntegration'),
                'page'      => 'commentrefs',
                
            ), 
            
            array(
                
                'id'        => 'crefs_comment_redirect_section',
                'title'     => 'First comment redirect',
                'callback'  => array($this->callbacks, 'scFirstCommentRedirect'),
                'page'      => 'commentrefs',
                
            ), 
            
            array(
                
                'id'        => 'crefs_prevent_low_quality_section',
                'title'     => 'Prevent low quality comments',
                'callback'  => array($this->callbacks, 'scPreventLowQaulityComment'),
                'page'      => 'commentrefs',
                
            ),
            
            array(
                
                'id'        => 'crefs_miscellaneous_section',
                'title'     => 'Miscellaneous',
               // 'callback'  => array($this->callbacks, 'firstRedirectSectionCallback'),
                'page'      => 'commentrefs',
                
            ),
            
        );
        
        $this->settings->setSections($args);
        
    }
    
    public function setFields() {
        
        $args = array(
            
            array(
                
                'id'        => 'crefs_post_type',
                'title'     => 'Post Types:',
                'callback'  => array($this->callbacks, 'fcPostType'),
                'page'      => 'commentrefs',
                'section'   =>  'crefs_post_type_section',
                'args'      => array(
                    
                    'label_for' => 'crefs_post_type',
                    'class'     => 'crefs_post_type',
                    
                    )
                
                ),
            
            array(
                
                'id'        => 'crefs_get_ten_posts',
                'title'     => 'Select commentator',
                'callback'  => array($this->callbacks, 'fcGetTenPosts'),
                'page'      => 'commentrefs',
                'section'   =>  'crefs_get_ten_posts_section',
                'args'      => array(
                    
                    'label_for' => 'crefs_get_ten_posts',
                    'class'     => 'crefs_get_ten_posts',
                    
                )
                
            ),
            
            array(
                
                'id'        => 'crefs_get_dofollow',
                'title'     => 'Select commentator',
                'callback'  => array($this->callbacks, 'fcGetDofollow'),
                'page'      => 'commentrefs',
                'section'   =>  'crefs_get_dofollow_section',
                'args'      => array(
                    
                    'label_for' => 'crefs_get_dofollow',
                    'class'     => 'crefs_get_dofollow',
                    
                )
                
            ),
            
            array(
                
                'id'        => 'crefs_sm_integration',
                'title'     => 'Select commentator',
                'callback'  => array($this->callbacks, 'fcSocialMediaIntegration'),
                'page'      => 'commentrefs',
                'section'   =>  'crefs_sm_integration_section',
                'args'      => array(
                    
                    'label_for' => 'crefs_social_media_integration_section',
                    'class'     => 'crefs_social_media_integration_section',
                    
                ) 
                
            ),            
            
            array(
                
                'id'        => 'crefs_comment_redirect',
                'title'     => 'Redirect',
                'callback'  => array($this->callbacks, 'fcCommentRedirect'),
                'page'      => 'commentrefs',
                'section'   =>  'crefs_comment_redirect_section',
                'args'      => array(
                    
                    'label_for' => 'crefs_comment_redirect',
                    'class'     => 'crefs_comment_redirect',
                    
                )
                
            ),
            
            array(
                
                'id'        => 'crefs_prevent_lq',
                'title'     => 'Redirect',
                'callback'  => array($this->callbacks, 'fcPreventLQComment'),
                'page'      => 'commentrefs',
                'section'   =>  'crefs_prevent_low_quality_section',
                'args'      => array(
                    
                    'label_for' => 'crefs_prevent_lq',
                    'class'     => 'crefs_prevent_lq',
                    
                )
                
            ),
            
            array(
                
                'id'        => 'crefs_miscellaneous',
                'title'     => 'Redirect',
                'callback'  => array($this->callbacks, 'fcMiscellaneous'),
                'page'      => 'commentrefs',
                'section'   =>  'crefs_miscellaneous_section',
                'args'      => array(
                    
                    'label_for' => 'crefs_miscellaneous',
                    'class'     => 'crefs_miscellaneous',
                    
                )
                
            )
            
        );
        
        $this->settings->setFields($args);
    }
	
}