<?php

namespace ZO\Bundle\SupervisorMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use ZO\Bundle\SupervisorMonitorBundle\Util\SupervisorClient;

class MonitorController extends AbstractController
{
    private SupervisorClient $supervisorClient;

    public function __construct(SupervisorClient $supervisorClient)
    {
        $this->supervisorClient = $supervisorClient;
    }

    #[Route('/monitor', name: 'zo_supervisor_monitor_index')]
    public function indexAction(): Response
    {
        $res = $this->supervisorClient->getServersListVersion();

        return $this->render('@ZOSupervisorMonitor/index.html.twig', [
            'servers' => $this->supervisorClient->getServers(),
            'services' => $res['services'] ?? null,
            'version' => $res['version'] ?? null,
        ]);
    }

    #[Route('/monitor/start/{name}/{worker}', name: 'zo_supervisor_monitor_start')]
    public function startAction(string $name, string $worker): JsonResponse
    {
        return $this->handleAction(fn() => $this->supervisorClient->startService($name, $worker), "$worker started.");
    }

    #[Route('/monitor/restart/{name}/{worker}', name: 'zo_supervisor_monitor_restart')]
    public function restartAction(string $name, string $worker): JsonResponse
    {
        return $this->handleAction(fn() => $this->supervisorClient->restartService($name, $worker), "$worker restarted.");
    }

    #[Route('/monitor/stop/{name}/{worker}', name: 'zo_supervisor_monitor_stop')]
    public function stopAction(string $name, string $worker): JsonResponse
    {
        return $this->handleAction(fn() => $this->supervisorClient->stopService($name, $worker), "$worker stopped.");
    }

    #[Route('/monitor/clear-log/{name}/{worker}', name: 'zo_supervisor_monitor_clear_log')]
    public function clearLogAction(string $name, string $worker): JsonResponse
    {
        return $this->handleAction(fn() => $this->supervisorClient->clearServiceLog($name, $worker), "$worker log cleared.");
    }

    #[Route('/monitor/start-all/{name}', name: 'zo_supervisor_monitor_start_all')]
    public function startAllAction(string $name): JsonResponse
    {
        return $this->handleAction(fn() => $this->supervisorClient->startAllService($name), "All services started.");
    }

    #[Route('/monitor/restart-all/{name}', name: 'zo_supervisor_monitor_restart_all')]
    public function restartAllAction(string $name): JsonResponse
    {
        return $this->handleAction(fn() => $this->supervisorClient->restartAllService($name), "All services restarted.");
    }

    #[Route('/monitor/stop-all/{name}', name: 'zo_supervisor_monitor_stop_all')]
    public function stopAllAction(string $name): JsonResponse
    {
        return $this->handleAction(fn() => $this->supervisorClient->stopAllService($name), "All services stopped.");
    }

    private function handleAction(callable $action, string $successMessage): JsonResponse
    {
        return $action() ? new JsonResponse(['message' => $successMessage]) : new JsonResponse(['message' => 'Operation failed.'], 500);
    }
}
