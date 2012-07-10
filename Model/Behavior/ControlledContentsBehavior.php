<?php

class ControlledContentsBehavior extends ModelBehavior {

	public function setup(Model &$Model, $config = array()) {
		$Model->Behaviors->load('Acl', array(
			'className' => 'CroogoAcl',
			'type' => 'controlled',
			));
	}

	public function parentNode($model) {
		if (!$model->id && empty($model->data)) {
			return null;
		} else {
			$alias = $model->alias;
			if ($model->id) {
				$id = $model->id;
			} else {
				$id = $model->data[$alias][$model->primaryKey];
			}
			$aco = $model->Aco->find('first', array(
				'conditions' => array(
					'model' => $alias,
					'foreign_key' => $id,
					)
				));
			if (empty($aco['Aco']['foreign_key'])) {
				$return = 'contents';
			} else {
				$return = array($alias => array(
					'id' => $aco['Aco']['foreign_key']
					));
			}
			return $return;
		}
	}

	public function afterSave(Model $model, $created) {
		if (empty($model->data[$model->alias][$model->primaryKey])) {
			return;
		}
		$node = $model->node();
		$aco = $node[0];
		$alias = $model->alias;
		$aco['Aco']['alias'] = sprintf(
			'%s.%s', $alias, $model->data[$alias][$model->primaryKey]
			);
		$model->Aco->save($aco);

		if ($user = AuthComponent::user()) {
			$aro = array('User' => $user);
			$model->Aco->Permission->allow($aro, $aco['Aco']['alias']);
		}
	}

}
