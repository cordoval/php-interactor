<?php

/**
 * This file is part of the PhpInteractor package
 *
 * @package    PhpInteractor
 * @author     Mark Badolato <mbadolato@cybernox.com>
 * @copyright  Copyright (c) CyberNox Technologies. All rights reserved.
 * @license    http://www.opensource.org/licenses/MIT MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpInteractor\Tests;

use PhpInteractor\DependencyManager;

class DependencyManagerTest extends \PHPUnit_Framework_TestCase
{
    const DEFINED_INTERACTOR        = 'TestInteractorName';
    const NON_DEFINED_INTERACTOR    = 'InteractorNotSpecificallyDefinedSoOnlyGlobalDependencies';

    /** @var DependencyManager */
    private $manager;

    /** @test */
    public function registerGlobalDependency()
    {
        $this->manager->registerGlobalDependency('standard', new \stdClass());
        $dependencies = $this->manager->getDependencyMap(self::NON_DEFINED_INTERACTOR);
        $this->assertCount(1, $dependencies);
        $this->assertTrue($dependencies->containsKey('standard'));
        $this->assertInstanceOf('stdClass', $dependencies->get('standard')->get());
    }

    /** @test */
    public function registerInteractorDependency()
    {
        $this->manager->registerInteractorDependency('standard', new \stdClass(), self::DEFINED_INTERACTOR);
        $dependencies = $this->manager->getDependencyMap(self::DEFINED_INTERACTOR);
        $this->assertCount(1, $dependencies);
        $this->assertTrue($dependencies->containsKey('standard'));
        $this->assertInstanceOf('stdClass', $dependencies->get('standard')->get());
    }

    /** @test */
    public function registerMultipleInteractorDependencies()
    {
        $this->manager->registerInteractorDependency('standard', new \stdClass(), self::DEFINED_INTERACTOR);
        $this->manager->registerInteractorDependency('additional', new \stdClass(), self::DEFINED_INTERACTOR);
        $dependencies = $this->manager->getDependencyMap(self::DEFINED_INTERACTOR);
        $this->assertCount(2, $dependencies);
        $this->assertTrue($dependencies->containsKey('standard'));
        $this->assertTrue($dependencies->containsKey('additional'));
        $this->assertInstanceOf('stdClass', $dependencies->get('additional')->get());
    }

    /** @test */
    public function registerGlobalAndInteractorDependencies()
    {
        $this->manager->registerGlobalDependency('global', new \stdClass());
        $this->manager->registerInteractorDependency('standard', new \stdClass(), self::DEFINED_INTERACTOR);

        $dependencies = $this->manager->getDependencyMap(self::NON_DEFINED_INTERACTOR);
        $this->assertCount(1, $dependencies);
        $this->assertTrue($dependencies->containsKey('global'));
        $this->assertFalse($dependencies->containsKey('standard'));

        $dependencies = $this->manager->getDependencyMap(self::DEFINED_INTERACTOR);
        $this->assertCount(2, $dependencies);
        $this->assertTrue($dependencies->containsKey('global'));
        $this->assertTrue($dependencies->containsKey('standard'));
        $this->assertInstanceOf('stdClass', $dependencies->get('global')->get());
        $this->assertInstanceOf('stdClass', $dependencies->get('standard')->get());
    }

    /** @test */
    public function registerGlobalAndMultipleInteractorWithDependencies()
    {
        $this->manager->registerGlobalDependency('global', new \stdClass());
        $this->manager->registerInteractorDependency('standard', new \stdClass(), self::DEFINED_INTERACTOR);
        $this->manager->registerInteractorDependency('additional', new \stdClass(), 'TestInteractorName2');

        $dependencies = $this->manager->getDependencyMap(self::NON_DEFINED_INTERACTOR);
        $this->assertCount(1, $dependencies);
        $this->assertTrue($dependencies->containsKey('global'));
        $this->assertFalse($dependencies->containsKey('standard'));

        $dependencies = $this->manager->getDependencyMap(self::DEFINED_INTERACTOR);
        $this->assertCount(2, $dependencies);
        $this->assertTrue($dependencies->containsKey('global'));
        $this->assertTrue($dependencies->containsKey('standard'));
        $this->assertFalse($dependencies->containsKey('additional'));
        $this->assertInstanceOf('stdClass', $dependencies->get('global')->get());
        $this->assertInstanceOf('stdClass', $dependencies->get('standard')->get());

        $dependencies = $this->manager->getDependencyMap('TestInteractorName2');
        $this->assertCount(2, $dependencies);
        $this->assertTrue($dependencies->containsKey('global'));
        $this->assertTrue($dependencies->containsKey('additional'));
        $this->assertFalse($dependencies->containsKey('standard'));
        $this->assertInstanceOf('stdClass', $dependencies->get('global')->get());
        $this->assertInstanceOf('stdClass', $dependencies->get('additional')->get());
    }

    /** @test */
    public function registerInteractorDependencyAlreadyDefinedGlobally()
    {
        $this->manager->registerGlobalDependency('standard', new \stdClass());
        $this->manager->registerInteractorDependency('standard', new \stdClass(), self::DEFINED_INTERACTOR);
        $dependencies = $this->manager->getDependencyMap('standard');
        $this->assertCount(1, $dependencies);
        $this->assertTrue($dependencies->containsKey('standard'));
    }

    protected function setUp()
    {
        $this->manager = new DependencyManager();
    }
}
