<?php

namespace ZO\Bundle\SupervisorMonitorBundle\Util;

use Exception;
use PhpXmlRpc\Response;
use PhpXmlRpc\Value;
use PhpXmlRpc\Request;
use PhpXmlRpc\Client;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SupervisorClient
{
    private LoggerInterface $logger;
    private ContainerInterface $container;
    private $servers = [];

    /**
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        $this->logger = $logger;
        $this->servers = $container->getParameter('zo_supervisor_monitor.servers');
    }

    /**
     * @return array
     */
    public function getServers(): array
    {
        return $this->servers;
    }

    /**
     * @return array[]|null
     */
    public function getServersListVersion(): ?array
    {
        if (empty($this->servers)) {
            return null;
        }

        $list = [];
        $version = [];

        foreach ($this->servers as $name => $config) {
            try {
                $client = $this->createServerClient($config);
                $sRes = $this->sendRequest($client, 'getAllProcessInfo');
                $vRes = $this->sendRequest($client, 'getSupervisorVersion');

                if ((isset($sRes->errno) && $sRes->errno !== 0) || (isset($vRes->errno) && $vRes->errno !== 0)) {
                    throw new RuntimeException("Supervisor returned an error.");
                }

                $list[$name] = $sRes;
                $version[$name] = $vRes;
            } catch (Exception $e) {
                $this->logger->error("Error fetching Supervisor data for $name: " . $e->getMessage());

                return null;
            }
        }

        return [
            'version' => $version,
            'services' => $list,
        ];
    }

    /**
     * @param string $server
     * @return bool
     */
    public function startAllService(string $server): bool
    {
        return $this->executeCommand($server, 'startAllProcesses', [new Value(1)]);
    }

    /**
     * @param string $server
     * @return bool
     */
    public function stopAllService(string $server): bool
    {
        return $this->executeCommand($server, 'stopAllProcesses', [new Value(1)]);
    }

    /**
     * @param string $server
     * @return bool
     */
    public function restartAllService(string $server): bool
    {
        if ($this->stopAllService($server)) {
            sleep(2);

            return $this->startAllService($server);
        }

        return false;
    }

    /**
     * @param string $server
     * @param string $worker
     * @return bool
     */
    public function startService(string $server, string $worker): bool
    {
        return $this->executeCommand($server, 'startProcess', [new Value($worker)]);
    }

    /**
     * @param string $server
     * @param string $worker
     * @return bool
     */
    public function stopService(string $server, string $worker): bool
    {
        return $this->executeCommand($server, 'stopProcess', [new Value($worker)]);
    }

    /**
     * @param string $server
     * @param string $worker
     * @return bool
     */
    public function restartService(string $server, string $worker): bool
    {
        if ($this->stopService($server, $worker)) {
            sleep(2);

            return $this->startService($server, $worker);
        }

        return false;
    }

    /**
     * @param string $server
     * @param string $worker
     * @return bool
     */
    public function clearServiceLog(string $server, string $worker): bool
    {
        return $this->executeCommand($server, 'clearProcessLogs', [new Value($worker)]);
    }

    /**
     * @param string $server
     * @param string $method
     * @param array $params
     * @return bool
     */
    private function executeCommand(string $server, string $method, array $params = []): bool
    {
        $serverConfig = $this->servers[$server] ?? null;
        if (!$serverConfig) {
            $this->logger->error("Server $server not found in configuration.");

            return false;
        }

        try {
            $client = $this->createServerClient($serverConfig);
            $res = $this->sendRequest($client, $method, $params);

            return isset($res->errno) && $res->errno === 0;
        } catch (Exception $e) {
            $this->logger->error("Error executing $method on $server: " . $e->getMessage());

            return false;
        }
    }

    /**
     * @param array $serverConfig
     * @return Client
     */
    private function createServerClient(array $serverConfig): Client
    {
        return $this->createClient(
            $serverConfig['host'] ?? '',
            $serverConfig['port'] ?? null,
            $serverConfig['username'] ?? null,
            $serverConfig['password'] ?? null
        );
    }

    /**
     * @param string $host
     * @param int|null $port
     * @param string|null $username
     * @param string|null $password
     * @return Client
     */
    private function createClient(string $host, ?int $port = null, ?string $username = null, ?string $password = null): Client
    {
        $path = $host . ($port ? ":$port" : '') . '/RPC2';
        $client = new Client($path);

        if ($username || $password) {
            $client->setCredentials($username, $password);
        }

        return $client;
    }

    /**
     * @param Client $client
     * @param string $method
     * @param array $params
     * @return array|Response
     */
    private function sendRequest(Client $client, string $method, array $params = []): array|Response
    {
        return $client->send(new Request('supervisor.' . $method, $params));
    }
}