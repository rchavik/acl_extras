<?php

class RoleAroBehavior extends ModelBehavior {

	public function parentNode($model) {
		$this->log('AroUpdater::parentNode');
		if (!$model->id && empty($model->data)) {
			return null;
		} else {
			$id = $model->id ? $model->id : $model->data['Role']['id'];
			$aro = $model->Aro->find('first', array(
				'conditions' => array(
					'model' => $model->alias,
					'foreign_key' => $id,
					)
				));
			if (empty($aro['Aro']['foreign_key'])) {
				$return = null;
			} else {
				$return = array('Role' => array('id' => $aro['Aro']['foreign_key']));
			}
			return $return;
		}
	}

	public function afterSave($model, $created) {
		if (empty($model->data['Role']['alias'])) {
			return;
		}
		$node = $model->node();
		$aro = $node[0];
		$aro['Aro']['alias'] = 'Role-' . $model->data['Role']['alias'];
		$model->Aro->save($aro);
	}

}
