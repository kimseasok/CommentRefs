<?php
/*
 * @package CommentRefs
 */
 
namespace Inc\Base;
use \Inc\Base\ControlerCrefs;

class SettingLinksCrefs extends ControlerCrefs
{
    
	public function register(){
        /**
         * Add setting links to CommentRefs Plugin action links
         *
         * Param    Array   $links  Array action links
         * Return   Array   $links  Array Action links
         */
		add_filter("plugin_action_links_{$this->plugin}", array($this, 'setting_links'));
        
	}
	
	public function setting_links($links){
        
		$setting_links = '<a href="' .esc_url(get_site_url()) .'/wp-admin/admin.php?page=commentrefs" title="Settings">Settings</a>';
        
		$links[] = $setting_links;
        
		return $links;
        
	}
    
}