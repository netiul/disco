<?php

/*
 * This file is part of the Disco package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\Disco\Proxy\Configuration;

use bitExpert\Disco\Config\BeanConfiguration;
use bitExpert\Disco\Config\BeanConfigurationWithConflictingAliases;
use bitExpert\Disco\Config\BeanConfigurationWithConflictingAliasesInParentClass;
use bitExpert\Disco\Config\ExtendedBeanConfigurationOverwritingParentAlias;
use bitExpert\Disco\Config\InterfaceConfiguration;
use bitExpert\Disco\Config\InvalidConfiguration;
use bitExpert\Disco\Config\MissingBeanAnnotationConfiguration;
use bitExpert\Disco\Config\MissingReturnTypeConfiguration;
use bitExpert\Disco\Config\NonExistentReturnTypeConfiguration;
use Laminas\Code\Generator\ClassGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ProxyManager\Exception\InvalidProxiedClassException;

/**
 * Unit tests for {@link \bitExpert\Disco\Proxy\Configuration\ConfigurationGenerator}.
 */
class ConfigurationGeneratorUnitTest extends TestCase
{
    /**
     * @var ConfigurationGenerator
     */
    private $configGenerator;

    /**
     * @var ClassGenerator&MockObject
     */
    private $classGenerator;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->configGenerator = new ConfigurationGenerator();
        /** @var ClassGenerator&MockObject $mock */
        $mock = $this->createMock(ClassGenerator::class);
        $this->classGenerator = $mock;
    }

    /**
     * @test
     */
    public function configClassWithoutAnAnnotationThrowsException(): void
    {
        self::expectException(InvalidProxiedClassException::class);

        $reflClass = new \ReflectionClass(InvalidConfiguration::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function passingInterfaceAsConfigClassThrowsException(): void
    {
        self::expectException(InvalidProxiedClassException::class);

        $reflClass = new \ReflectionClass(InterfaceConfiguration::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function missingBeanAnnotationThrowsException(): void
    {
        self::expectException(InvalidProxiedClassException::class);

        $reflClass = new \ReflectionClass(MissingBeanAnnotationConfiguration::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function missingReturnTypeOfBeanDeclarationThrowsException(): void
    {
        self::expectException(InvalidProxiedClassException::class);

        $reflClass = new \ReflectionClass(MissingReturnTypeConfiguration::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function nonExistentClassInReturnTypeThrowsException(): void
    {
        self::expectException(InvalidProxiedClassException::class);

        $reflClass = new \ReflectionClass(NonExistentReturnTypeConfiguration::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function sameAliasUsedForMultipleBeansThrowsException(): void
    {
        self::expectException(InvalidProxiedClassException::class);

        $reflClass = new \ReflectionClass(BeanConfigurationWithConflictingAliases::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function unknownAnnotationThrowsException(): void
    {
        self::expectException(InvalidProxiedClassException::class);
        self::expectExceptionMessageMatches('/^\[Semantical Error\] The annotation "@foo"/');

        /**
         * @foo
         */
        $configObject = new class
        {
            public function foo(): string
            {
                return 'foo';
            }
        };
        $reflClass = new \ReflectionObject($configObject);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function parsingConfigurationWithoutAnyErrorsSucceeds(): void
    {
        $this->classGenerator->expects(self::atLeastOnce())
            ->method('addMethodFromGenerator');

        $reflClass = new \ReflectionClass(BeanConfiguration::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function subclassedConfigurationIsAllowedToOverrwriteParentAlias(): void
    {
        $this->classGenerator->expects(self::atLeastOnce())
            ->method('addMethodFromGenerator');

        $reflClass = new \ReflectionClass(ExtendedBeanConfigurationOverwritingParentAlias::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }

    /**
     * @test
     */
    public function parsingConfigurationWithConflictingAliasesInParentConfigurationFails(): void
    {
        self::expectException(InvalidProxiedClassException::class);

        $reflClass = new \ReflectionClass(BeanConfigurationWithConflictingAliasesInParentClass::class);
        $this->configGenerator->generate($reflClass, $this->classGenerator);
    }
}
