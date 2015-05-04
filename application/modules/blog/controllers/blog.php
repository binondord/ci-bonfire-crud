<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Blog controller
 */
class Blog extends Front_Controller
{
    protected $permissionCreate = 'Blog.Blog.Create';
    protected $permissionDelete = 'Blog.Blog.Delete';
    protected $permissionEdit   = 'Blog.Blog.Edit';
    protected $permissionView   = 'Blog.Blog.View';

    /**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('blog/blog_model');
        $this->lang->load('blog');
		
        

		Assets::add_module_js('blog', 'blog.js');
	}

	/**
	 * Display a list of Blog data.
	 *
	 * @return void
	 */
	public function index()
	{
        
        
        
        
		$records = $this->blog_model->find_all();

		Template::set('records', $records);
        

		Template::render();
	}
    
}