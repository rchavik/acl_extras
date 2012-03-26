<?php

/**
 * AclExtrasAccess Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclExtrasAccessComponent extends Component {

	protected $controller = null;

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

/**
 * ACL: add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @param array $allowRoles Role aliases
 * @return void
 */
	public function addAco($action, $allowRoles = array()) {
		$Aco = $this->controller->Acl->Aco;
		// AROs
		$aroIds = array();
		if (count($allowRoles) > 0) {
			$roles = ClassRegistry::init('Role')->find('list', array(
				'conditions' => array(
					'Role.alias' => $allowRoles,
				),
				'fields' => array(
					'Role.id',
					'Role.alias',
				),
			));
			$roleIds = array_keys($roles);
			$aros = $this->controller->Acl->Aro->find('list', array(
				'conditions' => array(
					'Aro.model' => 'Role',
					'Aro.foreign_key' => $roleIds,
				),
				'fields' => array(
					'Aro.id',
					'Aro.alias',
				),
			));
			$aroIds = array_keys($aros);
		}

		$actionPath = $this->controller->Auth->authorize[AuthComponent::ALL]['actionPath'];
		$root = $Aco->node($actionPath);
		$root = $root[0];

		// ACOs
		$acoNode = $Aco->node($actionPath.'/'.$action);
		if (!isset($acoNode['0']['Aco']['id'])) {
			$actionName = false;
			switch (substr_count($action, '/')) {
			case 0:
				list($controllerName) = explode('/', $action);
				$parentPath = $actionPath;
			break;
			case 1:
				list($controllerName, $actionName) = explode('/', $action);
				$parentPath = $actionPath .'/'. $controllerName;
			break;
			case 2:
				list($pluginName, $controllerName, $actionName) = explode('/', $action);
				$parentPath = $pluginName . '/' . $controllerName;
			break;
			default:
				die(sprintf('Invalid action: %s', $action));
			break;
			}

			if (isset($pluginName)) {
				// get or create plugin ACO
				$pluginNode = $Aco->node($actionPath . '/' . $pluginName);
				if (!$pluginNode) {
					$Aco->create(array(
						'parent_id' => $root['Aco']['id'],
						'model' => null,
						'alias' => $pluginName,
					));
					$pluginNode = $Aco->save();
					$pluginNode['Aco']['id'] = $Aco->id;
				} else {
					$pluginNode = $pluginNode[0];
				}

				// get or create plugin's controller ACO
				$controllerNode = $Aco->node($actionPath . '/' . $pluginName . '/' . $controllerName);
				if (!$controllerNode) {
					$Aco->create(array(
						'parent_id' => $pluginNode['Aco']['id'],
						'model' => null,
						'alias' => $controllerName,
					));
					$parentNode = $Aco->save();
					$parentNode['Aco']['id'] = $Aco->id;
				} else {
					$parentNode = $controllerNode[0];
				}
				$alias = $actionName;
			} else {

				$parentNode = $Aco->node($parentPath);
				$parentNode = $parentNode[0];

				if (empty($parentNode)) {
					$controllerNode = $Aco->node($actionPath);
					$controllerNode = $controllerNode[0];
					list($junk, $currentNode) = explode('/', $parentPath);
					$Aco->create(array(
						'parent_id' => $controllerNode['Aco']['id'],
						'model' => null,
						'alias' => $currentNode,
						));
					$parentNode = $Aco->save();
				}

				if (!empty($actionName)) {
					$alias = $actionName;
				} else {
					$alias = $controllerName;
				}
			}

			$parentId = $parentNode['Aco']['id'];
			$acoData = array(
				'parent_id' => $parentId,
				'model' => null,
				'foreign_key' => null,
				'alias' => $alias,
			);
			$this->controller->Acl->Aco->id = false;
			$this->controller->Acl->Aco->save($acoData);
			$acoId = $this->controller->Acl->Aco->id;
		} else {
			$acoId = $acoNode['0']['Aco']['id'];
		}

		// Permissions (aros_acos)
		foreach ($aroIds AS $aroId) {
			$permission = $this->controller->Acl->Aro->Permission->find('first', array(
				'conditions' => array(
					'Permission.aro_id' => $aroId,
					'Permission.aco_id' => $acoId,
				),
			));
			if (!isset($permission['Permission']['id'])) {
				// create a new record
				$permissionData = array(
					'aro_id' => $aroId,
					'aco_id' => $acoId,
					'_create' => 1,
					'_read' => 1,
					'_update' => 1,
					'_delete' => 1,
				);
				$this->controller->Acl->Aco->Permission->id = false;
				$this->controller->Acl->Aco->Permission->save($permissionData);
			} else {
				// check if not permitted
				if ($permission['Permission']['_create'] == 0 ||
					$permission['Permission']['_read'] == 0 ||
					$permission['Permission']['_update'] == 0 ||
					$permission['Permission']['_delete'] == 0) {
					$permissionData = array(
						'id' => $permission['Permission']['id'],
						'aro_id' => $aroId,
						'aco_id' => $acoId,
						'_create' => 1,
						'_read' => 1,
						'_update' => 1,
						'_delete' => 1,
					);
					$this->controller->Acl->Aco->Permission->id = $permission['Permission']['id'];
					$this->controller->Acl->Aco->Permission->save($permissionData);
				}
			}
		}
	}

/**
 * ACL: remove ACO
 *
 * Removes ACOs and their Permissions
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @return void
 */
	public function removeAco($action) {
		$acoNode = $this->controller->Acl->Aco->node($this->controller->Auth->authorize['all']['actionPath'].'/'.$action);
		if (isset($acoNode['0']['Aco']['id'])) {
			$this->controller->Acl->Aco->delete($acoNode['0']['Aco']['id']);
		}
	}

}
