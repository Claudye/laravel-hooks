<?php

namespace LaravelHooks\Traits;

use BadMethodCallException;

/**
 * Trait HasControllerHooks
 *
 * This trait provides functionality for executing hooks (callbacks) before and after calling methods.
 */
trait HasControllerHooks
{
    /**
     * Array to store callbacks to be executed before calling methods.
     *
     * @var array
     */
    protected $beforeActions = [];

    /**
     * Array to store callbacks to be executed after calling methods.
     *
     * @var array
     */
    protected $afterActions = [];

    /**
     * Registers a callback to be executed before calling the specified methods.
     *
     * @param array $methods An array of method names to register the callback for.
     * @param callable $callback The callback function to be executed.
     *
     * @return $this
     */
    protected function beforeCalling(array $methods, $callback)
    {
        foreach ($methods as $method) {
            $this->beforeActions[$method][] = $callback;
        }

        return $this;
    }

    /**
     * Registers a callback to be executed after calling the specified methods.
     *
     * @param array $methods An array of method names to register the callback for.
     * @param callable $callback The callback function to be executed.
     *
     * @return $this
     */
    protected function afterCalling(array $methods, $callback)
    {
        foreach ($methods as $method) {
            $this->afterActions[$method][] = $callback;
        }
        return $this;
    }

    /**
     * Calls a method with optional before and after hooks.
     *
     * @param string $method The name of the method to call.
     * @param array $parameters An array of parameters to pass to the method.
     *
     * @return mixed The result of the called method.
     *
     * @throws BadMethodCallException If the method does not exist.
     */
    public function callAction($method, $parameters)
    {
        $params = $parameters;
        // Check if the 'useHooks' method exists and call it if it does.
        if (method_exists($this, 'useHooks')) {
            $params[] = $method;
            $this->useHooks();
        }

        // Check if the method exists in the controller.
        if (!method_exists($this, $method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        // Execute all before actions, if they exist.
        if (isset($this->beforeActions[$method])) {
            foreach ($this->beforeActions[$method] as $callback) {
                $callback(...$params);
            }
        }

        // Call the method with the provided parameters.
        $result = $this->$method(...$parameters);

        // Execute all after actions, if they exist.
        if (isset($this->afterActions[$method])) {
            foreach ($this->afterActions[$method] as $callback) {
                $result = $callback($result, ...$params);
            }
        }

        return $result;
    }
}
