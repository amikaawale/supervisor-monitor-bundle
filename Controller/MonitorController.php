<?php

namespace ZO\Bundle\SupervisorMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpXmlRpc\Value;
use PhpXmlRpc\Request;
use PhpXmlRpc\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

class MonitorController extends AbstractController
{
    public function indexAction()
    {
    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->getServersListVersion();

        $services = $res ? $res['services'] : null;
        $version = $res ? $res['version'] : null;


        return $this->render('@ZOSupervisorMonitor/index.html.twig', array(
        	'servers' => $supervisorClient->getServers(),
        	'services' => $services,
        	'version' => $version,
        ));
    }

    public function startAllAction($name)
    {
    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->startAllService($name);

    	if($res){
    		return new JsonResponse(array('message'=> 'All services has been started.'));
    	}else{
    		return new JsonResponse(array('message'=> 'Unable to start all services.'), 500);
    	}

    	// return $this->redirectToRoute('zo_supervisor_monitor_index');
    }

    public function restartAllAction($name)
    {
    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->restartAllService($name);

		if($res){
    		return new JsonResponse(array('message'=> 'All services has been restarted.'));
    	}else{
    		return new JsonResponse(array('message'=> 'Unable to restart all services.'), 500);
    	}
    	// return $this->redirectToRoute('zo_supervisor_monitor_index');

    }

    public function stopAllAction($name)
    {
    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->stopAllService($name);

    	if($res){
    		return new JsonResponse(array('message'=> 'All services has been stoped.'));
    	}else{
    		return new JsonResponse(array('message'=> 'Unable to stop all services.'), 500);
    	}

    	// return $this->redirectToRoute('zo_supervisor_monitor_index');
    }

    public function startAction($name, $worker)
    {
    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->startService($name, $worker);

    	if($res){
    		return new JsonResponse(array('message'=> $worker.' has been started.'));
    	}else{
    		return new JsonResponse(array('message'=> 'Unable to start '.$worker.'.'), 500);
    	}

    	// return $this->redirectToRoute('zo_supervisor_monitor_index');
    }

    public function restartAction($name, $worker)
    {
    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->restartService($name, $worker);

    	if($res){
    		return new JsonResponse(array('message'=> $worker.' has been restarted.'));
    	}else{
    		return new JsonResponse(array('message'=> 'Unable to restart '.$worker.'.'), 500);
    	}

    	// return $this->redirectToRoute('zo_supervisor_monitor_index');

    }

    public function stopAction($name, $worker)
    {

    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->stopService($name, $worker);
    	if($res){
    		return new JsonResponse(array('message'=> $worker.' has been stopped.','service'=> $res));
    	}else{
    		return new JsonResponse(array('message'=> 'Unable to stop '.$worker.'.'), 500);
    	}

    	// return $this->redirectToRoute('zo_supervisor_monitor_index');
    }

    public function clearLogAction($name, $worker)
    {
    	$supervisorClient = $this->container->get('zo_supervisor_monitor.util.client');
    	$res = $supervisorClient->clearServiceLog($name, $worker);

    	if($res){
    		return new JsonResponse(array('message'=> $worker.' log has been cleared.'));
    	}else{
    		return new JsonResponse(array('message'=> 'Unable to clear log of  '.$worker.'.'), 500);
    	}
    	// return $this->redirectToRoute('zo_supervisor_monitor_index');
    }
}
