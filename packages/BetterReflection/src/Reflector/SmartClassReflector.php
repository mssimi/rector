<?php declare(strict_types=1);

namespace Rector\BetterReflection\Reflector;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use Rector\BetterReflection\Reflection\ReflectionClass;
use Rector\BetterReflection\Reflector\Exception\IdentifierNotFound;
use Rector\Configuration\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Throwable;

final class SmartClassReflector
{
    /**
     * @var ClassReflector
     */
    private $classReflector;

    /**
     * @var ReflectionClass[]
     */
    private $perClassNameClassReflections = [];

    /**
     * @var ClassReflectorFactory
     */
    private $classReflectorFactory;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var string[]
     */
    private $lastSource = [];

    public function __construct(ClassReflectorFactory $classReflectorFactory, ParameterProvider $parameterProvider)
    {
        $this->classReflectorFactory = $classReflectorFactory;
        $this->parameterProvider = $parameterProvider;
    }

    public function reflect(string $className): ?ReflectionClass
    {
        if (isset($this->perClassNameClassReflections[$className])) {
            return $this->perClassNameClassReflections[$className];
        }

        if (! $this->isValidClassName($className)) {
            return null;
        }

        // correct native class typos
        if ($className === 'DomDocument') {
            $className = 'DOMDocument';
        }

        try {
            return $this->perClassNameClassReflections[$className] = $this->getClassReflector()->reflect($className);
        } catch (IdentifierNotFound $throwable) {
            return null;
        }

        // @todo
        // throw exception or rather error only on classes, that were requested by isType*() on NodeAnalyzers
        // it doesn't make sense to reflect any other...
    }

    /**
     * @todo validate at least one is passed, or split to 2 methods?
     * @return string[]
     */
    public function getClassParents(?string $className = null, ?ClassLike $classLikeNode = null): array
    {
        // anonymous class
        if ($className === null) {
            if ($classLikeNode && property_exists($classLikeNode, 'extends')) {
                return [$classLikeNode->extends->toString()];
            }

            return [];
        }

        try {
            $classReflection = $this->reflect($className);

            return $classReflection->getParentClassNames();
        } catch (Throwable $throwable) {
            // intentionally empty
        }

        if ($classLikeNode) {
            return $this->resolveClassParentsFromNode($classLikeNode);
        }

        return [];
    }

    /**
     * @return string[]
     */
    public function resolveClassInterfaces(ReflectionClass $reflectionClass): array
    {
        try {
            return array_keys($reflectionClass->getInterfaces());
        } catch (IdentifierNotFound $identifierNotFoundException) {
            // @todo check if type is needed by any Rector
            return [];
        }
    }

    /**
     * @return string[]
     */
    public function resolveClassParents(ReflectionClass $reflectionClass): array
    {
        try {
            return $reflectionClass->getParentClassNames();
        } catch (IdentifierNotFound $identifierNotFoundException) {
            // @todo check if type is needed by any Rector
            return [];
        }
    }

    /**
     * @return string[]
     */
    private function resolveClassParentsFromNode(ClassLike $classLikeNode): array
    {
        if (! property_exists($classLikeNode, 'extends')) {
            return [];
        }

        if ($classLikeNode instanceof Class_) {
            return [$classLikeNode->extends->toString()];
        }

        if ($classLikeNode instanceof Interface_) {
            return array_map(function (Name $interface): string {
                return $interface->toString();
            }, $classLikeNode->extends);
        }
    }

    /**
     * Rebuilds when source changes, so it reflects current scope.
     * Useful mainly for tests.
     */
    private function getClassReflector(): ClassReflector
    {
        $currentSource = $this->parameterProvider->provideParameter(Option::SOURCE);
        if ($this->lastSource === $currentSource) {
            return $this->classReflector;
        }

        if ($currentSource) {
            $this->lastSource = $currentSource;
            return $this->classReflector = $this->classReflectorFactory->createWithSource($currentSource);
        }

        return $this->classReflector = $this->classReflectorFactory->create();
    }

    private function isValidClassName(string $className): bool
    {
        if (empty($className)) {
            return false;
        }

        // invalid class types
        if (in_array($className, ['this', 'static', 'self', 'null', 'array', 'string', 'bool'], true)) {
            return false;
        }

        // is function
        if (is_callable($className)) {
            return false;
        }

        // is constant
        return ! defined($className);
    }
}
