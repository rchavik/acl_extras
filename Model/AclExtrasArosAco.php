<?php
class AclExtrasArosAco extends AclExtrasAppModel {

	var $useTable = 'aros_acos';

	var $belongsTo = array(
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