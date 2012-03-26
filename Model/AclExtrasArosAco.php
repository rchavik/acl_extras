<?php
class AclExtrasArosAco extends AclExtrasAppModel {

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

}