<?php
class AclExtrasPermission extends AclExtrasAppModel {

	public $useTable = 'aros_acos';

	public $belongsTo = array(
		'AclAro' => array(
			'className' => 'AclExtras.AclExtrasAro',
			'foreignKey' => 'aro_id',
		),
		'AclAco' => array(
			'className' => 'AclExtras.AclExtrasAco',
			'foreignKey' => 'aco_id',
		),
	);

/** Generate allowed actions for current logged in Role
 *
 * @return array of elements formatted like ControllerName/action_name
 */
	public function getAllowedActionsByRoleId($roleId) {
		$acosTree = $this->AclAco->generateTreeList(array(
			'AclAco.parent_id !=' => null,
		), '{n}.AclAco.id', '{n}.AclAco.alias');
		$acos = array();
		$controller = null;
		foreach ($acosTree AS $acoId => $acoAlias) {
			if (substr($acoAlias, 0, 1) == '_') {
				$acos[$acoId] = $controller . '/' . substr($acoAlias, 1);
			} else {
				$controller = $acoAlias;
			}
		}
		$acoIds = array_keys($acos);

		$aro = $this->AclAro->find('first', array(
			'conditions' => array(
				'AclAro.model' => 'Role',
				'AclAro.foreign_key' => $roleId,
			),
		));
		$aroId = $aro['AclAro']['id'];

		$permissionsForCurrentRole = $this->find('list', array(
			'conditions' => array(
				'AclExtrasPermission.aro_id' => $aroId,
				'AclExtrasPermission.aco_id' => $acoIds,
				'AclExtrasPermission._create' => 1,
				'AclExtrasPermission._read' => 1,
				'AclExtrasPermission._update' => 1,
				'AclExtrasPermission._delete' => 1,
			),
			'fields' => array(
				'AclExtrasPermission.id',
				'AclExtrasPermission.aco_id',
			),
		));
		$permissionsByActions = array();
		foreach ($permissionsForCurrentRole AS $acoId) {
			$permissionsByActions[] = $acos[$acoId];
		}

		return $permissionsByActions;
	}
}