<?php

class AclExtrasHelper extends AppHelper {

	public $helpers = array(
		'Html',
		'Session',
		);

	public function beforeRender() {
		if (isset($this->params['admin']) && $this->Session->read('Auth.User.id')) {
			$this->Html->script('/acl_extras/js/acl_permissions.js', array('inline' => false));
			$this->Html->css('/acl_extras/css/admin', false, array('inline' => false));
		}
	}

}
