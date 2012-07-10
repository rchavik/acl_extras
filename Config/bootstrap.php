<?php

if (Configure::read('Site.acl_plugin') == 'AclExtras') {

	Croogo::hookComponent('*', 'AclExtras.AclExtrasFilter');
	Croogo::hookComponent('*', array(
		'CroogoAccess' => array(
			'className' => 'AclExtras.AclExtrasAccess',
			),
		));
	Croogo::hookHelper('*', 'AclExtras.AclExtras');

	Croogo::hookBehavior('User', 'AclExtras.UserAro');
	Croogo::hookBehavior('Role', 'AclExtras.RoleAro');
	Croogo::hookBehavior('Node', 'AclExtras.ControlledContents');

	CroogoNav::add('users.children.roles', array(
		'title' => 'Roles',
		'url' => array(
			'plugin' => 'acl_extras',
			'controller' => 'acl_extras_roles',
			'action' => 'index',
			),
		'weight' => 20,
		));

	CroogoNav::add('users.children.permissions', array(
		'title' => 'Permissions',
		'url' => array(
			'plugin' => 'acl_extras',
			'controller' => 'acl_extras_permissions',
			'action' => 'index',
			),
		'weight' => 30,
		));

	Cache::config('permissions', array(
		'duration' => '+1 hour',
		'path' => CACHE . 'queries',
		'engine' => 'File',
	));
}
