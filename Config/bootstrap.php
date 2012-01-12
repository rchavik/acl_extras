<?php

Croogo::hookHelper('*', 'AclExtras.AclExtras');

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
