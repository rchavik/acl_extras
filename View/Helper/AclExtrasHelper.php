<?php

class AclExtrasHelper extends AppHelper {

	public $helpers = array(
		'Html',
		);

	public function beforeRender() {
		if (isset($this->params['admin'])) {
			$this->Html->script('/acl_extras/js/acl_permissions.js', false);
			$this->Html->css('/acl_extras/css/admin', false, array('inline' => false));
		}
	}

}
