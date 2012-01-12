<?php
class AclExtrasArosAco extends AclExtrasAppModel {

	var $useTable = 'aros_acos';

	var $belongsTo = array(
	    'AclAro' => array(
	        'className' => 'AclExtras.AclAro',
	        'foreignKey' => 'aro_id',
	    ),
	    'AclAco' => array(
	        'className' => 'AclExtras.AclAco',
	        'foreignKey' => 'aco_id',
	    ),
	);

}