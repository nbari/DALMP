<?php
namespace DALMP;

/**
 * Abstract Dependecy Injector
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.2
 */
abstract class abstractDI
{

    /**
     * Container for the classes
     *
     * @var array
     */
    protected $c = array();

    /**
     * Share the classes
     *
     * @param  Closure $callable A closure to wrap for uniqueness
     * @return Closure The wrapped closure
     */
    public function share(\Closure $callable)
    {
        return function () use ($callable) {
            static $object = null;
            if (is_null($object)) {
                $object = call_user_func_array($callable, func_get_args());
            }

            return $object;
        };
    }

    /**
     * Dispatch the classes
     *
     * @param  string    $name
     * @param  string    $args
     * @return chainable object
     */
    public function __call($name, $args)
    {
        if (!isset($this->c[$name])) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $name));
        }

        $c = $this->c[$name];

        return  ($c instanceof \Closure) ? call_user_func_array($c, $args) : $c;
    }

    /**
     * return the Closures
     *
     * @param string Closure name
     * @return closure
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->c)) {
            return $this->c[$key];
        }
    }

    /**
     * Add an object to the container
     *
     * To share (load and later use) this can be used:
     * $di->addObject('name', $di->share(function () {
     *   return new Foo();
     * }));
     *
     * @param  string $name
     * @param  object $object
     * @return true   on success exception on failure
     */
    public function addObject($name, $object)
    {
        if (isset($this->c[$name])) {
            throw new \InvalidArgumentException(sprintf('Class "%s" already defined.', $name));
        }

        return $this->c[$name] = $object;
    }

}
