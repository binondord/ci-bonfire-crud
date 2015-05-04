<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Content controller
 */
class Content extends Admin_Controller
{
    protected $permissionCreate = 'Blog.Content.Create';
    protected $permissionDelete = 'Blog.Content.Delete';
    protected $permissionEdit   = 'Blog.Content.Edit';
    protected $permissionView   = 'Blog.Content.View';

    /**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
        $this->auth->restrict($this->permissionView);
		$this->load->model('blog/blog_model');
        $this->lang->load('blog');
		
            $this->form_validation->set_error_delimiters("<span class='error'>", "</span>");
        
		Template::set_block('sub_nav', 'content/_sub_nav');

		Assets::add_module_js('blog', 'blog.js');
	}

	/**
	 * Display a list of Blog data.
	 *
	 * @return void
	 */
	public function index()
	{
        // Deleting anything?
		if (isset($_POST['delete'])) {
            $this->auth->restrict($this->permissionDelete);
			$checked = $this->input->post('checked');
			if (is_array($checked) && count($checked)) {

                // If any of the deletions fail, set the result to false, so
                // failure message is set if any of the attempts fail, not just
                // the last attempt

				$result = true;
				foreach ($checked as $pid) {
					$deleted = $this->blog_model->delete($pid);
                    if ($deleted == false) {
                        $result = false;
                    }
				}
				if ($result) {
					Template::set_message(count($checked) . ' ' . lang('blog_delete_success'), 'success');
				} else {
					Template::set_message(lang('blog_delete_failure') . $this->blog_model->error, 'error');
				}
			}
		}
        
        
        
		$records = $this->blog_model->find_all();

		Template::set('records', $records);
        
    Template::set('toolbar_title', lang('blog_manage'));

		Template::render();
	}
    
    /**
	 * Create a Blog object.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->auth->restrict($this->permissionCreate);
        
		if (isset($_POST['save'])) {
			if ($insert_id = $this->save_blog()) {
				log_activity($this->auth->user_id(), lang('blog_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'blog');
				Template::set_message(lang('blog_create_success'), 'success');

				redirect(SITE_AREA . '/content/blog');
			}

            // Not validation error
			if ( ! empty($this->blog_model->error)) {
				Template::set_message(lang('blog_create_failure') . $this->blog_model->error, 'error');
            }
		}

		Template::set('toolbar_title', lang('blog_action_create'));

		Template::render();
	}
	/**
	 * Allows editing of Blog data.
	 *
	 * @return void
	 */
	public function edit()
	{
		$id = $this->uri->segment(5);
		if (empty($id)) {
			Template::set_message(lang('blog_invalid_id'), 'error');

			redirect(SITE_AREA . '/content/blog');
		}
        
		if (isset($_POST['save'])) {
			$this->auth->restrict($this->permissionEdit);

			if ($this->save_blog('update', $id)) {
				log_activity($this->auth->user_id(), lang('blog_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'blog');
				Template::set_message(lang('blog_edit_success'), 'success');
				redirect(SITE_AREA . '/content/blog');
			}

            // Not validation error
            if ( ! empty($this->blog_model->error)) {
                Template::set_message(lang('blog_edit_failure') . $this->blog_model->error, 'error');
			}
		}
        
		elseif (isset($_POST['delete'])) {
			$this->auth->restrict($this->permissionDelete);

			if ($this->blog_model->delete($id)) {
				log_activity($this->auth->user_id(), lang('blog_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'blog');
				Template::set_message(lang('blog_delete_success'), 'success');

				redirect(SITE_AREA . '/content/blog');
			}

            Template::set_message(lang('blog_delete_failure') . $this->blog_model->error, 'error');
		}
        
        Template::set('blog', $this->blog_model->find($id));

		Template::set('toolbar_title', lang('blog_edit_heading'));
		Template::render();
	}

	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	/**
	 * Save the data.
	 *
	 * @param string $type Either 'insert' or 'update'.
	 * @param int	 $id	The ID of the record to update, ignored on inserts.
	 *
	 * @return bool|int An int ID for successful inserts, true for successful
     * updates, else false.
	 */
	private function save_blog($type = 'insert', $id = 0)
	{
		if ($type == 'update') {
			$_POST['id'] = $id;
		}

        // Validate the data
        $this->form_validation->set_rules($this->blog_model->get_validation_rules());
        if ($this->form_validation->run() === false) {
            return false;
        }

		// Make sure we only pass in the fields we want
		
		$data = $this->blog_model->prep_data($this->input->post());

        // Additional handling for default values should be added below,
        // or in the model's prep_data() method
        

        $return = false;
		if ($type == 'insert') {
			$id = $this->blog_model->insert($data);

			if (is_numeric($id)) {
				$return = $id;
			}
		} elseif ($type == 'update') {
			$return = $this->blog_model->update($id, $data);
		}

		return $return;
	}
}