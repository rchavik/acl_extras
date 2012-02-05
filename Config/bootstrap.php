<?php

if (Configure::read('Site.acl_plugin') == 'AclExtras') {

	Croogo::hookComponent('*', 'AclExtras.AclExtrasFilter');
	Croogo::hookHelper('*', 'AclExtras.AclExtras');

	Croogo::hookBehavior('User', 'AclExtras.UserAro');
	Croogo::hookBehavior('Role', 'AclExtras.RoleAro');

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

}
