<?php
declare(strict_types=1);

namespace Migrations\Middleware;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\Core\InstanceConfigTrait;
use Migrations\Config\Config;
use Migrations\Migration\Manager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MigrationsMiddleware implements MiddlewareInterface
{
    use InstanceConfigTrait;

    protected array $_defaultConfig = [
        'paths' => [
            'migrations' => ROOT . DS . 'config' . DS . 'Migrations' . DS,
        ],
        'environment' => [
            'adapter' => null,
            'connection' => 'default',
            'database' => null,
            'migration_table' => 'phinxlog',
        ],
    ];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Process method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @throws \Cake\Core\Exception\CakeException
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!Configure::read('debug')) {
            return $handler->handle($request);
        }

        if (!$this->hasPendingMigrations()) {
            return $handler->handle($request);
        }

        throw new CakeException('Pending migrations need to be run: `bin/cake migrations migrate`.', 503);
    }

    /**
     * @return bool
     */
    protected function hasPendingMigrations(): bool
    {
        $config = new Config($this->_config);
        $manager = new Manager($config, new ConsoleIo());

        $migrations = $manager->getMigrations();
        foreach ($migrations as $migration) {
            if (!$manager->isMigrated($migration->getVersion())) {
                return true;
            }
        }

        return false;
    }
}
