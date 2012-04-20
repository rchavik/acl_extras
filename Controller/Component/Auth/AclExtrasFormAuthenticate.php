<?php

App::uses('FormAuthenticate', 'Controller/Component/Auth');

class AclExtrasFormAuthenticate extends FormAuthenticate {

	public $settings = array(
		'fields' => array(
			'username' => 'username',
			'password' => 'password',
			'email' => 'email',
		),
		'userModel' => 'User',
		'scope' => array(),
		'recursive' => 0
	);

	protected function _findUser($username, $password) {
        $userModel = $this->settings['userModel'];
		list($plugin, $model) = pluginSplit($userModel);
		$fields = $this->settings['fields'];
		$conditions = array(
			'OR' => array(
				$model . '.' . $fields['username'] => $username,
				$model . '.' . $fields['email'] => $username,
				),
			$model . '.' . $fields['password'] => $this->_password($password),
			);
		if (!empty($this->settings['scope'])) {
			$conditions = array_merge($conditions, $this->settings['scope']);
		}
		$result = ClassRegistry::init($userModel)->find('first', array(
			'conditions' => $conditions,
			'recursive' => (int)$this->settings['recursive']
		));
		if (empty($result) || empty($result[$model])) {
			return false;
		}
		unset($result[$model][$fields['password']]);
		return $result[$model];
	}

}
