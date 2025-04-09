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
3. Routing annotation
	
3. Set Container
   ```
       # config/services.yaml

      ZO\Bundle\SupervisorMonitorBundle\Controller\MonitorController:
        calls:
            - method: setContainer
              arguments: [ '@service_container' ]

   ```

Find the supervisor monitor page at /supervisor/monitor.
