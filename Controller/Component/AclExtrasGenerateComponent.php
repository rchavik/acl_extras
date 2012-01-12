<?php
/**
 * AclExtrasGenerate Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  AclExtras
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link	 http://www.croogo.org
 */
class AclExtrasGenerateComponent extends Component {

	protected $controller = null;

/**
 * @param object $controller controller
 * @param array  $settings   settings
 */
	public function initialize(&$controller) {
		$this->controller =& $controller;
		App::uses('Folder', 'Utility');
		$this->folder = new Folder;
	}

/**
 * List all controllers (including plugin controllers)
 *
 * @return array
 */
	public function listControllers() {
		$controllerPaths = array();

		// app/Controller
		$this->folder->path = APP.'Controller'.DS;
		$controllers = $this->folder->read();
		foreach ($controllers['1'] AS $c) {
			if (substr($c, strlen($c) - 4, 4) == '.php') {
				$cName = Inflector::camelize(str_replace('Controller.php', '', $c));
				$controllerPaths[$cName] = APP.'Controller'.DS.$c;
			}
		}

		// Plugin/*/Controller/
		$this->folder->path = APP.'Plugin'.DS;
		$plugins = $this->folder->read();
		foreach ($plugins['0'] AS $p) {
			if ($p != 'Install') {
				$this->folder->path = APP.'Plugin'.DS.$p.DS.'Controller'.DS;
				$pluginControllers = $this->folder->read();
				foreach ($pluginControllers['1'] AS $pc) {
					if (substr($pc, strlen($pc) - 4, 4) == '.php') {
						$pcName = Inflector::camelize($p) .'/'. Inflector::camelize(str_replace('Controller.php', '', $pc));
						$controllerPaths[$pcName] = APP.'Plugin'.DS.$p.DS.'Controller'.DS.$pc;
					}
				}
			}
		}

		return $controllerPaths;
	}

/**
 * List actions of a particular Controller.
 *
 * @param string  $name Controller name (the name only, without having Controller at the end)
 * @param string  $path full path to the controller file including file extension
 * @param boolean $all  default is false. it true, private actions will be returned too.
 *
 * @return array
 */
	public function listActions($name, $path) {
		// base methods
		if (strstr($path, APP .'Plugin')) {
			$plugin = $this->getPluginFromPath($path);
			$pacName = Inflector::camelize($plugin) . 'AppController'; // pac - PluginAppController
			$pacPath = APP.'Plugin'.DS.$plugin.DS.$plugin.'AppController.php';
			App::import('Controller', $pacName, null, null, $pacPath);
			$baseMethods = get_class_methods($pacName);
		} else {
			$baseMethods = get_class_methods('AppController');
		}

		if (strpos($name, '/') !== false) {
			list($pluginName, $controllerName) = explode('/', $name);
			$controllerName .= 'Controller';
		} else {
			$controllerName = $name.'Controller';
		}
		App::import('Controller', $controllerName, null, null, $path);
		$methods = get_class_methods($controllerName);

		// filter out methods
		foreach ($methods AS $k => $method) {
			if (strpos($method, '_', 0) === 0) {
				unset($methods[$k]);
				continue;
			}
			if (in_array($method, $baseMethods)) {
				unset($methods[$k]);
				continue;
			}
		}

		return $methods;
	}

/**
 * Get plugin name from path
 *
 * @param string $path file path
 *
 * @return string
 */
	public function getPluginFromPath($path) {
		$pathE = explode(DS, $path);
		$pluginsK = array_search('Plugin', $pathE);
		$pluginNameK = $pluginsK + 1;
		$plugin = $pathE[$pluginNameK];

		return $plugin;
	}

}