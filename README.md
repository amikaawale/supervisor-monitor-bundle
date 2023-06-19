ZOSupervisorMonitorBundle
=============

This bundle provides a way to monitor supervisor process and control those states.

- Configure multiple supervisor server services.
- Start, Stop, Restart individual or all services.

Config
------

1. Enable the bundle on config/bundles.php 
2. Configure the bundle 
	```
	# config/packages/zo_supervisor_monitor.yaml

	zo_supervisor_monitor:
	    servers:
	        local:
	            host: http://localhost
	            port: 9001
	            username: null
	            password: null
	        test:
	            host: http://localhost
	            port: 9001
	            username: null
	            password: null

	```
3. Register routes for bundles
	```
		# config/routes.yaml

		zo_supervisor_monitor:
		    resource: "@ZOSupervisorMonitorBundle/Resources/config/routing.yml"
		    prefix:   /supervisor/

	```

   ```
3. Set Container
   ```
       # config/services.yaml

      ZO\Bundle\SupervisorMonitorBundle\Controller\MonitorController:
        calls:
            - method: setContainer
              arguments: [ '@service_container' ]

   ```

Find the supervisor monitor page at /supervisor/monitor.

Widget
------

1. Supervisor client is exposed as service `zo_supervisor_monitor.util.client`.
2. Supervisor configured servers process information can be fetched as,
	```
		$client = $this->get('zo_supervisor_monitor.util.client');
		$res = $this->client->getServersListVersion();
		// $res = array('services' => $services, 'version' => $version);
	```
3. Supervisor monitor view can be found at `ZOSupervisorMonitorBundle::services.html.twig`. **Remember to pass `services` and `version` variable to the view.**