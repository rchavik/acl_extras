<?php

class AclExtrasActivation {

	public function beforeActivation($controller) {
		return true;
	}

	public function onActivation($controller) {
		//$controller->Setting->write('Site.acl_plugin', 'Acl', array('editable' => 1, 'title' => 'Acl Plugin'));
	}

	public function beforeDeactivation($controller) {
		return true;
	}

	public function onDeactivation($controller) {
	}

}
