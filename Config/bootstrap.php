<?php

Croogo::hookHelper('*', 'AclExtras.AclExtras');

Croogo::hookBehavior('User', 'AclExtras.UserAro');
Croogo::hookBehavior('Role', 'AclExtras.RoleAro');

if (Configure::read('Site.acl_plugin') == 'AclExtras') {

	CroogoNav::remove('users.children.roles');
	CroogoNav::add('users.children.roles', array(
		'title' => 'Roles',
		'url' => array(
			'plugin' => 'acl_extras',
			'controller' => 'acl_extras_roles',
			'action' => 'index',
			),
		'weight' => 20,
		));

	CroogoNav::remove('users.children.permissions');
	CroogoNav::add('users.children.permissions', array(
		'title' => 'Permissions',
		'url' => array(
			'plugin' => 'acl_extras',
			'controller' => 'acl_extras_permissions',
			'action' => 'index',
			),
		'weight' => 30,
		));

}
