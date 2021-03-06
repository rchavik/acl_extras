<?php

class UserAroBehavior extends ModelBehavior {

	public function parentNode($model) {
		if (!$model->id && empty($model->data)) {
			return null;
		}
		$data = $model->data;
		if (empty($model->data)) {
			$data = $model->read();
		}
		if (!isset($data['User']['role_id'])) {
			$data['User']['role_id'] = $model->field('role_id');
		}
		if (!isset($data['User']['role_id']) || !$data['User']['role_id']) {
			return null;
		} else {
			return array('Role' => array('id' => $data['User']['role_id']));
		}
	}

	public function afterSave($model, $created) {
		if (empty($model->data['User']['username'])) {
			return;
		}
		$node = $model->node();
		$aro = $node[0];
		$aro['Aro']['alias'] = $model->data['User']['username'];
		$model->Aro->save($aro);
	}

}
