<?php

App::uses('Component', 'Controller');
App::uses('AppController', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');
app::uses('CroogoComponent', 'Controller/Component');

class MockCroogoComponent extends CroogoComponent {
	public function startup(Controller $controller) {
		$this->controller = $controller;
	}
}

class CroogoTestController extends AppController {
}

class AclExtrasCroogoComponentTest extends CroogoTestCase {

	public $fixtures = array(
		'aco', 'aro', 'aros_aco',
		);

	public function setUp() {
		parent::setUp();

		$this->Controller = new CroogoTestController(new CakeRequest(), new CakeResponse());
		$this->Controller->constructClasses();
		$this->Controller->Croogo = new MockCroogoComponent($this->Controller->Components);
		$this->Controller->Components->set('Croogo', $this->Controller->Croogo);
		$this->Controller->startupProcess();
	}

	public function testAddRemoveAcos() {
		$Aco = ClassRegistry::init('Aco');

		$this->Controller->Croogo->addAco('CroogoTestController');
		$parent = $Aco->findByAlias('CroogoTestController');
		$this->assertNotEmpty($parent);

		$this->Controller->Croogo->addAco('CroogoTestController/index');
		$child = $Aco->findByParentId($parent['Aco']['id']);
		$this->assertNotEmpty($child);

		$this->Controller->Croogo->removeAco('CroogoTestController/index');
		$child = $Aco->findByParentId($parent['Aco']['id']);
		$this->assertEmpty($child);

		$this->Controller->Croogo->removeAco('CroogoTestController');
		$parent = $Aco->findByAlias('CroogoTestController');
		$this->assertEmpty($parent);
	}

	public function testAddRemovePluginAcos() {
		$Aco = ClassRegistry::init('Aco');

		$this->Controller->Croogo->addAco('TestPlugin/TestPluginTest');
		$parent = $Aco->findByAlias('TestPluginTest');
		$this->assertNotEmpty($parent);

		$this->Controller->Croogo->addAco('TestPlugin/TestPluginTest/index');
		$child = $Aco->findByParentId($parent['Aco']['id']);
		$this->assertNotEmpty($child);

		$this->Controller->Croogo->removeAco('TestPlugin/TestPluginTest/index');
		$child = $Aco->findByParentId($parent['Aco']['id']);
		$this->assertEmpty($child);

		$this->Controller->Croogo->removeAco('TestPlugin/TestPluginTest');
		$parent = $Aco->findByAlias('TestPluginTest');
		$this->assertEmpty($parent);

		$this->Controller->Croogo->addAco('TestPlugin/TestPluginTest/index');
		$child = $Aco->node('controllers/TestPlugin/TestPluginTest/index');
		$this->assertNotEmpty($child);

		$this->Controller->Croogo->removeAco('TestPlugin');
		$parent = $Aco->findByAlias('TestPluginTest');
		$this->assertEmpty($parent);

		$child = $Aco->node('controllers/TestPlugin/TestPluginTest/index');
		$this->assertEmpty($child);
	}
}
