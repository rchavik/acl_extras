<?php

class AclExtrasHelper extends AppHelper {

	public $helpers = array(
		'Html',
		'Session',
		);

	public $allowedActions = array();

	public function beforeRender() {
		if (isset($this->params['admin']) && $this->Session->read('Auth.User.id')) {
			$this->Html->script('/acl_extras/js/acl_permissions.js', array('inline' => false));
			$this->Html->css('/acl_extras/css/admin', false, array('inline' => false));
		}
	}

	function getAllowedActionsByRoleId($roleId) {
		if (!empty($this->allowedActions[$roleId])) {
			return $this->allowedActions[$roleId];
		}
		$this->allowedActions[$roleId] = ClassRegistry::init('AclExtras.AclExtrasPermission')->getAllowedActionsByRoleId($roleId);
		return $this->allowedActions[$roleId];
	}

	function linkIsAllowedByRoleId($roleId, $url) {
		if (empty($url)) { return false; }
		$linkAction = Inflector::camelize($url['controller']) . '/' . $url['action'];
		if (isset($url['admin']) && $url['admin']) {
			$linkAction = Inflector::camelize($url['controller']) . '/admin_' . $url['action'];
		}
		if (in_array($linkAction, $this->getAllowedActionsByRoleId($roleId))) {
			return true;
		}
		return false;
	}

}
