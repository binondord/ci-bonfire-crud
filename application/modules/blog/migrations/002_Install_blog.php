<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_blog extends Migration
{
	/**
	 * @var string The name of the database table
	 */
	private $table_name = 'blog';

	/**
	 * @var array The table's fields
	 */
	private $fields = array(
		'post_id' => array(
                    'type'           => 'bigint',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ),
                'title' => array(
                    'type'       => 'varchar',
                    'constraint' => 255,
                    'null'       => false,
                ),
                'slug' => array(
                    'type'       => 'varchar',
                    'constraint' => 255,
                    'null'       => false,
                ),
                'body' => array(
                    'type' => 'text',
                    'null' => true,
                ),
                'created_on' => array(
                    'type' => 'datetime',
                    'null' => false,
                ),
                'modified_on' => array(
                    'type' => 'datetime',
                    'null' => false,
                ),
                'deleted' => array(
                    'type' => 'tinyint',
                    'constraint' => 1,
                    'null' => false,
                    'default' => 0,
                ),
	);

	/**
	 * Install this version
	 *
	 * @return void
	 */
	public function up()
	{
		$this->dbforge->add_field($this->fields);
		$this->dbforge->add_key('post_id', true);
		$this->dbforge->create_table($this->table_name);
	}

	/**
	 * Uninstall this version
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
                
                // Remove the permissions.
                $this->load->model('roles/role_permission_model');
                $this->load->model('permissions/permission_model');

                $permissionKey = $this->permission_model->get_key();
                foreach ($this->permissionValues as $permissionValue) {
                    $permission = $this->permission_model->select($permissionKey)
                                                         ->find_by('name', $permissionValue['name']);
                    if ($permission) {
                        // permission_model's delete method calls the role_permission_model's
                        // delete_for_permission method.
                        $this->permission_model->delete($permission->{$permissionKey});
                    }
                }
	}
}