<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_blog_permissions extends Migration
{
	/**
	 * @var array Permissions to Migrate
	 */
	private $permissionValues = array(
		array(
			'name' => 'Blog.Content.View',
			'description' => 'View Blog Content',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Content.Create',
			'description' => 'Create Blog Content',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Content.Edit',
			'description' => 'Edit Blog Content',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Content.Delete',
			'description' => 'Delete Blog Content',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Reports.View',
			'description' => 'View Blog Reports',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Reports.Create',
			'description' => 'Create Blog Reports',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Reports.Edit',
			'description' => 'Edit Blog Reports',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Reports.Delete',
			'description' => 'Delete Blog Reports',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Settings.View',
			'description' => 'View Blog Settings',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Settings.Create',
			'description' => 'Create Blog Settings',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Settings.Edit',
			'description' => 'Edit Blog Settings',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Settings.Delete',
			'description' => 'Delete Blog Settings',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Developer.View',
			'description' => 'View Blog Developer',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Developer.Create',
			'description' => 'Create Blog Developer',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Developer.Edit',
			'description' => 'Edit Blog Developer',
			'status' => 'active',
		),
		array(
			'name' => 'Blog.Developer.Delete',
			'description' => 'Delete Blog Developer',
			'status' => 'active',
		),
    );

    /**
     * @var string The name of the permission key in the role_permissions table
     */
    private $permissionKey = 'permission_id';

    /**
     * @var string The name of the permission name field in the permissions table
     */
    private $permissionNameField = 'name';

	/**
	 * @var string The name of the role/permissions ref table
	 */
	private $rolePermissionsTable = 'role_permissions';

    /**
     * @var numeric The role id to which the permissions will be applied
     */
    private $roleId = '1';

    /**
     * @var string The name of the role key in the role_permissions table
     */
    private $roleKey = 'role_id';

	/**
	 * @var string The name of the permissions table
	 */
	private $tableName = 'permissions';

	//--------------------------------------------------------------------

	/**
	 * Install this version
	 *
	 * @return void
	 */
	public function up()
	{
		$rolePermissionsData = array();
		foreach ($this->permissionValues as $permissionValue) {
			$this->db->insert($this->tableName, $permissionValue);

			$rolePermissionsData[] = array(
                $this->roleKey       => $this->roleId,
                $this->permissionKey => $this->db->insert_id(),
			);
		}

		$this->db->insert_batch($this->rolePermissionsTable, $rolePermissionsData);
	}

	/**
	 * Uninstall this version
	 *
	 * @return void
	 */
	public function down()
	{
        $permissionNames = array();
		foreach ($this->permissionValues as $permissionValue) {
            $permissionNames[] = $permissionValue[$this->permissionNameField];
        }

        $query = $this->db->select($this->permissionKey)
                          ->where_in($this->permissionNameField, $permissionNames)
                          ->get($this->tableName);

        if ( ! $query->num_rows()) {
            return;
        }

        $permissionIds = array();
        foreach ($query->result() as $row) {
            $permissionIds[] = $row->{$this->permissionKey};
        }

        $this->db->where_in($this->permissionKey, $permissionIds)
                 ->delete($this->rolePermissionsTable);

        $this->db->where_in($this->permissionNameField, $permissionNames)
                 ->delete($this->tableName);
	}
}