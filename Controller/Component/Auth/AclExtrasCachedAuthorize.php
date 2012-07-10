<?php
/**
 * AclExtrasCachedAuthorize
 *
 * PHP version 5
 *
 * @package  AclExtras
 * @since    1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
App::uses('BaseAuthorize', 'Controller/Component/Auth');

class AclExtrasCachedAuthorize extends BaseAuthorize {

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->_setPrefixMappings();
	}

/**
 * sets the crud mappings for prefix routes.
 *
 * @return void
 */
	protected function _setPrefixMappings() {
		$crud = array('create', 'read', 'update', 'delete');
		$map = array_combine($crud, $crud);

		$prefixes = Router::prefixes();
		if (!empty($prefixes)) {
			foreach ($prefixes as $prefix) {
				$map = array_merge($map, array(
					$prefix . '_index' => 'read',
					$prefix . '_add' => 'create',
					$prefix . '_edit' => 'update',
					$prefix . '_view' => 'read',
					$prefix . '_remove' => 'delete',
					$prefix . '_create' => 'create',
					$prefix . '_read' => 'read',
					$prefix . '_update' => 'update',
					$prefix . '_delete' => 'delete'
				));
			}
		}
		$this->mapActions($map);
	}

/**
 * check request request authorization
 *
 */
	public function authorize($user, CakeRequest $request) {
		$allowed = false;
		$Acl = $this->_Collection->load('Acl');
		$user = array($this->settings['userModel'] => $user);
		$action = $this->action($request);

		$cacheName = 'permissions_' . strval($user['User']['id']);
		if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
			$permissions = array();
			Cache::write($cacheName, $permissions, 'permissions');
		}

		if (!isset($permissions[$action])) {
			$allowed = $Acl->check($user, $action);
			$permissions[$action] = $allowed;
			Cache::write($cacheName, $permissions, 'permissions');
			$hit = false;
		} else {
			$allowed = $permissions[$action];
			$hit = true;
		}

		if (Configure::read('debug')) {
			$status = $allowed ? ' allowed.' : ' denied.';
			$cached = $hit ? ' (cache hit)' : ' (cache miss)';
			CakeLog::write(LOG_ERR, $user['User']['username'] . ' - ' . $action . $status . $cached);
		}

		if ($allowed && !empty($request->params['pass'][0])) {
			$id = $request->params['pass'][0];
			if (is_numeric($id)) {
				$allowed = $this->_authorizeByContent($user['User'], $request);
			}
		}

		return $allowed;
	}

	protected function _authorizeByContent($user, CakeRequest $request) {
		if (!isset($this->settings['actionMap'][$request->params['action']])) {
			throw new CakeException(
				__('_authorizeByContent() - Access of un-mapped action "%1$s" in controller "%2$s"',
				$request->action,
				$request->controller
			));
		}

		if (empty($request->params['pass'][0])) {
			return false;
		}

		$user = array($this->settings['userModel'] => $user);
		$acoNode = $this->_getAco($request->params['pass'][0]);
		$alias = sprintf('%s.%s', $acoNode['model'], $acoNode['foreign_key']);
		$action = $this->settings['actionMap'][$request->params['action']];

		$cacheName = 'permissions_content_' . strval($user['User']['id']);
		if (($permissions = Cache::read($cacheName, 'permissions')) === false) {
			$permissions = array();
			Cache::write($cacheName, $permissions, 'permissions');
		}

		if (!isset($permissions[$alias][$action])) {
			$Acl = $this->_Collection->load('Acl');
			$allowed = $Acl->check($user, $acoNode, $action);
			$permissions[$alias][$action] = $allowed;
			Cache::write($cacheName, $permissions, 'permissions');
			$hit = false;
		} else {
			$allowed = $permissions[$alias][$action];
			$hit = true;
		}

		if (Configure::read('debug')) {
			$status = $allowed ? ' allowed.' : ' denied.';
			$cached = $hit ? ' (cache hit)' : ' (cache miss)';
			CakeLog::write(LOG_ERR, $user['User']['username'] . ' - ' . $action . '/' . $request->params['pass'][0] . $status . $cached);
		}
		return $allowed;
	}

/**
 * Builds acoNode for Acl->check()
 *
 * @param integer $id The passed id param
 * @return array
 */
	protected function _getAco($id) {
		$modelClass = $this->_Controller->modelClass;
		return array('model' => $modelClass, 'foreign_key' => $id);
	}

}
