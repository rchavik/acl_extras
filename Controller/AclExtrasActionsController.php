<?php
/**
 * AclExtrasActions Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  AclExtras
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link	 http://www.croogo.org
 */
class AclExtrasActionsController extends AclExtrasAppController {
	public $uses = array('AclExtras.AclAco');
	public $components = array('AclExtras.AclExtrasGenerate');

	public function admin_index() {
		$this->set('title_for_layout', __('Actions'));

		$this->set('acos', $this->AclExtrasFilter->acoTreelist());
	}

	public function admin_add() {
		$this->set('title_for_layout', __('Add Action'));

		if (!empty($this->data)) {
			$this->Acl->Aco->create();

			// if parent_id is null, assign 'controllers' as parent
			if ($this->data['Aco']['parent_id'] == null) {
				$this->data['Aco']['parent_id'] = 1;
				$acoType = 'Controller';
			} else {
				$acoType = 'Action';
			}

			if ($this->Acl->Aco->save($this->data['Aco'])) {
				$this->Session->setFlash(sprintf(__('The %s has been saved'), $acoType), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(sprintf(__('The %s could not be saved. Please, try again.'), $acoType), 'default', array('class' => 'error'));
			}
		}

		$acos = $this->AclExtrasFilter->acoTreelist();
		$this->set(compact('acos'));
	}

	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Action'));

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Action'), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Acl->Aco->save($this->data['Aco'])) {
				$this->Session->setFlash(__('The Action has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Action could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Acl->Aco->read(null, $id);
		}

		$acos = $this->AclExtrasFilter->acoTreelist();
		$this->set(compact('acos'));
	}

	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Action'), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Acl->Aco->delete($id)) {
			$this->Session->setFlash(__('Action deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
	}

	public function admin_move($id, $direction = 'up', $step = '1') {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Action'), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if ($direction == 'up') {
			if ($this->Acl->Aco->moveUp($id)) {
				$this->Session->setFlash(__('Action moved up'), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			}
		} else {
			if ($this->Acl->Aco->moveDown($id)) {
				$this->Session->setFlash(__('Action moved down'), 'default', array('class' => 'success'));
				$this->redirect(array('action'=>'index'));
			}
		}
	}

	public function admin_generate() {
		App::uses('AclExtras', 'AclExtras.Lib');
		$AclExtras = new AclExtras();
		$AclExtras->startup($this);
		$AclExtras->aco_sync();

		if (isset($this->params['named']['permissions'])) {
			$this->redirect(array('plugin' => 'acl_extras', 'controller' => 'acl_extras_permissions', 'action' => 'index'));
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

}