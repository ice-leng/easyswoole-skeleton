<?php

namespace EasySwoole\Skeleton\Utility\ErrorCode;

use EasySwoole\Skeleton\BaseObject;
use EasySwoole\Skeleton\Helpers\Arrays\ArrayHelper;
use EasySwoole\Skeleton\Helpers\StringHelper;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;

class MergeErrorCode extends BaseObject
{
    /**
     * @var array
     */
    protected $constantValue = [];

    /**
     * paths
     * @var array
     */
    private $path = [];

    /**
     * class name
     * @var string
     */
    private $classname;

    /**
     * class namespace
     * @var string
     */
    private $classNamespace;

    /**
     *  output
     * @var string
     */
    private $output;

    /**
     * @var string
     */
    private $stub;

    /**
     * @return array
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @param array $path
     *
     * @return MergeErrorCode
     */
    public function setPath(array $path): MergeErrorCode
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassname(): string
    {
        return $this->classname;
    }

    /**
     * @param string $classname
     *
     * @return MergeErrorCode
     */
    public function setClassname(string $classname): MergeErrorCode
    {
        $this->classname = $classname;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassNamespace(): string
    {
        return $this->classNamespace;
    }

    /**
     * @param string $classNamespace
     *
     * @return MergeErrorCode
     */
    public function setClassNamespace(string $classNamespace): MergeErrorCode
    {
        $this->classNamespace = $classNamespace;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @param string $output
     *
     * @return MergeErrorCode
     */
    public function setOutput(string $output): MergeErrorCode
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @param string $namespace
     * @param string $classname
     *
     * @return string
     */
    protected function getPrefix(string $namespace, string $classname): string
    {
        $names = StringHelper::explode($namespace, '\\', true, true);
        array_unshift($names, 'ERROR');
        $names[] = $classname;
        $names = array_map(function ($name) {
            return StringHelper::strtoupper($name);
        }, $names);
        return implode('_', $names);
    }

    /**
     * @param string $stub
     *
     * @return $this
     */
    public function setStub(string $stub): MergeErrorCode
    {
        $this->stub = $stub;
        return $this;
    }

    /**
     * @return string
     */
    public function getStub(): string
    {
        return  $this->stub ?? __DIR__ . '/stubs/error-code.stub';
    }

    /**
     * @return string
     */
    protected function generatePath(): string
    {
        return $this->getOutput() . '/' . $this->getClassname() . '.php';
    }

    protected function replaceNamespace(string &$stub, string $namespace): self
    {
        $stub = str_replace('%NAMESPACE%', $namespace, $stub);

        return $this;
    }

    protected function replaceClass(string &$stub, string $classname): self
    {
        $stub = str_replace('%CLASSNAME%', $classname, $stub);
        return $this;
    }

    protected function replaceConstant(string &$stub, string $name): self
    {
        $stub = str_replace('%CONSTANT%', $name, $stub);
        return $this;
    }

    /**
     * @param array $data
     *
     * @return bool|int
     */
    protected function buildClass(array $data)
    {
        $stub = file_get_contents($this->getStub());

        $this->replaceNamespace($stub, $this->getClassNamespace())
            ->replaceClass($stub, $this->getClassname())
            ->replaceConstant($stub, implode(PHP_EOL, $data));

        return file_put_contents($this->generatePath(), $stub);
    }

    public function generate()
    {
        $data = [];
        $classLoader = include EASYSWOOLE_ROOT . '/vendor/autoload.php';
        $betterReflection = new BetterReflection();
        $astLocator = $betterReflection->astLocator();
        $reflector  = new ClassReflector(new AggregateSourceLocator([
            new ComposerSourceLocator($classLoader,$astLocator),
            new DirectoriesSourceLocator($this->getPath(), $astLocator),
            new PhpInternalSourceLocator($astLocator, $betterReflection->sourceStubber()),
        ]));

        $classes = $reflector->getAllClasses();
        foreach ($classes as $class) {
            $prefix = $this->getPrefix($class->getNamespaceName(), $class->getShortName());
            $constants = $class->getReflectionConstants();
            foreach ($constants as $constant) {
                $name = $prefix . '_' . $constant->getName();
                $data[] = implode(PHP_EOL . "   ", [
                    "    " . implode(PHP_EOL . "    ", explode(PHP_EOL, $constant->getDocComment())),
                    "const {$name} = '{$constant->getValue()}';",
                    '',
                ]);
                $const = "{$class->getName()}::{$constant->getName()}";
                if (ArrayHelper::isValidValue($this->constantValue, $constant->getValue())) {
                    throw new \RuntimeException("Constant {$this->constantValue[$constant->getValue()]} and {$const} value repeat");
                }
                $this->constantValue[$constant->getValue()] = $const;
            }
        }
        return $this->buildClass($data);
    }
}
