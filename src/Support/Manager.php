<?php

namespace Backstage\Laravel\AI\Support;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class Manager
{
    /**
     * The container instance.
     */
    protected Container $container;

    /**
     * The configuration repository instance.
     */
    protected Repository $config;

    /**
     * The registered custom driver creators.
     *
     * @var array<string, Closure>
     */
    protected array $customCreators = [];

    /**
     * The array of created "drivers".
     *
     * @var array<string, mixed>
     */
    protected array $drivers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = $container->make('config');
    }

    /**
     * Get the default driver name.
     *
     * There is no default driver; resolve one explicitly via driver('name').
     *
     * @throws InvalidArgumentException
     */
    public function getDefaultDriver(): string
    {
        throw new InvalidArgumentException(sprintf(
            'No default driver configured for [%s]. Resolve a driver explicitly via driver().', static::class
        ));
    }

    /**
     * Get a driver instance.
     *
     * @throws InvalidArgumentException
     */
    public function driver(?string $driver = null): mixed
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (is_null($driver)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].', static::class
            ));
        }

        return $this->drivers[$driver] ??= $this->createDriver($driver);
    }

    /**
     * Create a new driver instance.
     *
     * @throws InvalidArgumentException
     */
    protected function createDriver(string $driver): mixed
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $method = 'create'.Str::studly($driver).'Driver';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException("Driver [{$driver}] not supported.");
    }

    /**
     * Call a custom driver creator.
     */
    protected function callCustomCreator(string $driver): mixed
    {
        return $this->customCreators[$driver]($this->container);
    }

    /**
     * Register a custom driver creator Closure.
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get all of the created "drivers".
     *
     * @return array<string, mixed>
     */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * Get the container instance used by the manager.
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Set the container instance used by the manager.
     */
    public function setContainer(Container $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Forget all of the resolved driver instances.
     */
    public function forgetDrivers(): static
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Dynamically resolve a driver by method name (e.g. elevenLabs() => driver('eleven-labs')),
     * falling back to forwarding the call to the default driver instance.
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (method_exists($this, 'create'.Str::studly($method).'Driver')) {
            return $this->driver(Str::kebab($method));
        }

        return $this->driver()->$method(...$parameters);
    }
}
