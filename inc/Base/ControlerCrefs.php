<?php
/*
 * @package CommentRefs
 */
 
namespace Inc\Base;

class ControlerCrefs
{
	public $plugin_path;
    
	public $plugin_url;
	
	public function __construct(){
        
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            
            $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
            $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
            $this->plugin = plugin_basename(dirname(__FILE__, 3)) .'/commentrefs.php';
            
        } else {
            
            $this->plugin_path = plugin_dir_path($this->dirName(__FILE__, 2));
            $this->plugin_url = plugin_dir_url($this->dirName(__FILE__, 2));
            $this->plugin = plugin_basename($this->dirName(__FILE__, 3)) .'/commentrefs.php';
            
        }
        
	}
    
    public function dirName($path, $depth = '') {
        
        if ($depth && $depth >= 0) {
            
            for ($i = 1; $i <= $depth; $i++) {
                
                $path = dirname($path);
            }
            
            return $path;
        }
        
        return false;
    }
    
}