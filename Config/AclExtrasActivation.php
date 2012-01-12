<?php

class AclExtrasActivation {

	public function beforeActivation($controller) {
		return true;
	}

	public function onActivation($controller) {
	}

	public function beforeDeactivation($controller) {
		if (Configure::read('Site.acl_plugin') == 'AclExtras') {
			// plugin in use
			return false;
		}
		$loaded = CakePlugin::loaded();
		$leftovers = array_diff($loaded, array('AclExtras'));
		foreach ($leftovers as $plugin) {
			// only allow deactivation when alternate Acl plugin is active
			if (preg_match('/^Acl/', $plugin)) {
				return true;
			}
		}
		return false;
	}

	public function onDeactivation($controller) {
	}

}
