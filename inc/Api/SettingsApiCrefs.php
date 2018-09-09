<?php
/*
 * @package CommentRefs
 */
 
namespace Inc\Api;
 
class SettingsApiCrefs
{
	public $admin_pages = array();
    
	public $admin_subpages = array();
    
	public $settings = array();
    
	public $sections = array();
    
	public $fields = array();
	
    //Regiser all services
	public function register(){
        
        /**
         * Add admin pages if the pages properties is not empty
         *
         * Param
         * Return void
         *
         */
        
        
		if(!empty($this->admin_pages)){
            
			add_action('admin_menu', array($this, 'addAdminMenu'));
            
		}
        
        /**
         * Register all setting options if the settings properties is not empty
         *
         * Param
         * Return void
         *
         */
        
        if(!empty($this->settings)){
            
            add_action('admin_init', array($this, 'registerCustomFields') );
            
        }
	}
    
    /**
     * Setter method for adding Admin pages
     *
     * Param    Array $pages
     * Return   Array $pages
     */
    
	public function addPages(array $pages){
        
		$this->admin_pages = $pages;

		return $this;
	}
	
    /**
     * Add subpage to associate admin page if any
     *
     * Param    String  $title  string subpage title
     * Return   Array   $this   Array subpage properies
     *
     */
    
	public function withSubPage(string $title = null){
        
        //make sure admin page is not empty
		if(empty($this->admin_pages)){
            
			return $this;
            
		}
        
		$admin_page = $this->admin_pages[0];
		
		$subpages = array(
            
			array(
                
				'parent_slug'	=> $admin_page['menu_slug'],
				'page_title'	=> __($admin_page['page_title'], 'CommentRefs'),
				'menu_title'	=> ($title) ? $title : $admin_page['menu_title'],
				'capability'	=> $admin_page['capability'],
				'menu_slug'		=> $admin_page['menu_slug'],
				'callback' 		=> $admin_page['callback'],
                
			),
            
		);
		
		$this->admin_subpages = $subpages;
        
		return $this;
        
	}
	
    /**
     * Add all subpages if the property are set
     *
     * Param    Array   $pages  Array subpage arguments properties
     * Return   Array   $this   Array subpage arguments properties
     *
     */
    
	public function addSubPages(array $pages) {
        
		$this->admin_subpages = array_merge($this->admin_subpages, $pages);
        
		return $this;
	}
    
    /**
     * Setter method for registering setting options
     *
     * Param    Array $settings Array arguments properties for register setting
     * Return   Array $this     Array arguments properties
     *
     */
    
	function setSettings(array $settings) {
        
		$this->settings = $settings;
        
		return $this;
	}
	
    /**
     * Setter method for register setting sections
     *
     * Param    Array $section  Array argument propertiess for registering setting section
     * Return   Array $this     Array arguments properties
     *
     */
    
	function setSections(array $sections) {
        
		$this->sections = $sections;
        
		return $this;
	}
    
    /**
     * Setter method for adding setting fields
     *
     */
	function setFields(array $fields) {
        
		$this->fields = $fields;
        
		return $this;
	}
	
    /**
     * Add all Admin pages and its subpages
     *
     * Param
     * Return void
     *
     */
    
	public function addAdminMenu(){
        
		foreach($this->admin_pages as $page){
            
			add_menu_page($page['page_title'], __($page['menu_title'], 'CommentRefs'), $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
            
		}
		
		foreach($this->admin_subpages as $page){
            
			add_submenu_page($page['parent_slug'], $page['page_title'], __($page['menu_title'], 'CommentRefs'), $page['capability'], $page['menu_slug'], $page['callback']);
            
		}
	}
	
    /**
     * Register all setting options
     * Add all setting sections and its associate setting fields
     *
     * Param
     * Return void
     *
     */
	public function registerCustomFields() {
        
		foreach($this->settings as $setting) {
            
			register_setting($setting['option_group'], $setting['option_name'], isset($setting['callback']) ? $setting['callback'] : 'sanitize_text_fields');
            
		}
		
		foreach($this->sections as $section){
            
			add_settings_section($section['id'], __($section['title'], 'CommentRefs'), isset($section['callback']) ? $section['callback'] : '', $section['page']);
            
		}
		
		foreach($this->fields as $field){
            
			add_settings_field($field['id'], __($field['title'], 'CommentRefs'), isset($field['callback']) ? $field['callback'] : '', $field['page'], $field['section'], isset($field['args']) ? $field['args'] : '');
            
		}
		
	}
	
}