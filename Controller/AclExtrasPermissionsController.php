<?php
/**
 * AclExtrasPermissions Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  AclExtras
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclExtrasPermissionsController extends AclExtrasAppController {

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array(
		'AclExtras.AclExtrasAco',
		'AclExtras.AclExtrasAro',
		'AclExtras.AclExtrasPermission',
		'Role',
	);

	public function admin_index() {
		$this->set('title_for_layout', __('Permissions', true));

		$acos = $this->AclExtrasFilter->acoTreelist();
		$roles = $this->Role->find('list');

		$this->set(compact('acos', 'roles'));

		$rolesAros = $this->AclExtrasAro->find('all', array(
			'conditions' => array(
				'AclExtrasAro.model' => 'Role',
				'AclExtrasAro.foreign_key' => array_keys($roles),
			),
		));
		$rolesAros = Set::combine($rolesAros, '{n}.AclExtrasAro.foreign_key', '{n}.AclExtrasAro.id');

		$permissions = array(); // acoId => roleId => bool
		foreach ($acos AS $acoId => $aco) {
			if (substr_count($aco[0], '-') != 0) {
				$permission = array();
				foreach ($roles AS $roleId => $roleTitle) {
					$hasAny = array(
						'aco_id'  => $acoId,
						'aro_id'  => $rolesAros[$roleId],
						'_create' => 1,
						'_read'   => 1,
						'_update' => 1,
						'_delete' => 1,
					);
					if ($this->AclExtrasPermission->hasAny($hasAny)) {
						$permission[$roleId] = 1;
					} else {
						$permission[$roleId] = 0;
					}
					$permissions[$acoId] = $permission;
				}
			}
		}
		$this->set(compact('rolesAros', 'permissions'));
	}

	public function admin_toggle($acoId, $aroId) {
		if (!$this->RequestHandler->isAjax()) {
			$this->redirect(array('action' => 'index'));
		}

		// see if acoId and aroId combination exists
		$conditions = array(
			'AclExtrasPermission.aco_id' => $acoId,
			'AclExtrasPermission.aro_id' => $aroId,
		);
		if ($this->AclExtrasPermission->hasAny($conditions)) {
			$data = $this->AclExtrasPermission->find('first', array('conditions' => $conditions));
			if ($data['AclExtrasPermission']['_create'] == 1 &&
				$data['AclExtrasPermission']['_read'] == 1 &&
				$data['AclExtrasPermission']['_update'] == 1 &&
				$data['AclExtrasPermission']['_delete'] == 1) {
				// from 1 to 0
				$data['AclExtrasPermission']['_create'] = 0;
				$data['AclExtrasPermission']['_read'] = 0;
				$data['AclExtrasPermission']['_update'] = 0;
				$data['AclExtrasPermission']['_delete'] = 0;
				$permitted = 0;
			} else {
				// from 0 to 1
				$data['AclExtrasPermission']['_create'] = 1;
				$data['AclExtrasPermission']['_read'] = 1;
				$data['AclExtrasPermission']['_update'] = 1;
				$data['AclExtrasPermission']['_delete'] = 1;
				$permitted = 1;
			}
		} else {
			// create - CRUD with 1
			$data['AclExtrasPermission']['aco_id'] = $acoId;
			$data['AclExtrasPermission']['aro_id'] = $aroId;
			$data['AclExtrasPermission']['_create'] = 1;
			$data['AclExtrasPermission']['_read'] = 1;
			$data['AclExtrasPermission']['_update'] = 1;
			$data['AclExtrasPermission']['_delete'] = 1;
			$permitted = 1;
		}

		// save
		$success = 0;
		if ($this->AclExtrasPermission->save($data)) {
			$success = 1;
		}

		$this->set(compact('acoId', 'aroId', 'data', 'success', 'permitted'));
	}

	function admin_upgrade() {
		App::import('Component', 'AclExtras.AclExtrasUpgrade');
		$this->AclUpgrade = new AclUpgradeComponent($this->Components);
		$this->AclUpgrade->initialize($this);
		if (($errors = $this->AclUpgrade->upgrade()) === true) {
			$this->Session->setFlash(__('Acl Upgrade complete', true));
		} else {
			$message = '';
			foreach ($errors as $error) {
				$message .= $error . '<br />';
			}
			$this->Session->setFlash($message);
		}
		$this->redirect($this->referer());
	}

}