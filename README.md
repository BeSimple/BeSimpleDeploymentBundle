Symfony2 applications deployment made easy
==========================================


**This bundle is still under developpment, unusable for now!**

-  DeploymentBundle supports many types of file synchronisation (rsync and ftp for now).
-  You can setup many deployment configuration for many servers.
-  You can provide `ignore` and `force` rules to control wich files are synchronized (in an easy way).
-  You can schedule commands executed after the deployment (on the remote server) ; some usefull commands are provided.


How to install
--------------


###Get the sources (using git)

Use the submodule git command if your project is under git control, if not, just use the clone command.

    git submodule add git://github.com/jfsimon/DeploymentBundle.git src/Bundle/DeploymentBundle


###Add it to your application

You have to add the bundle to your `AppKernel` class.
Notice that the service is loaded only if you setted up the servers configuration in yous config file.

    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\DeploymentBundle\DeploymentBundle(),
            // ...
        );
    }
    

How to configure
----------------


###An example

    deployment.rules:
        eclipse:
            ignore:   [.settings, .buildpath, .project]
        versioning:
            ignore:   [.git, .git*, .svn]
        symfony:
            ignore:   [/app/logs, /app/cache, /web/uploads, /web/*_dev.php]
            commands: [deployment:clearcache, deployment:fixperms]

    deployment.servers:
        staging:
            type:     rsync
            host:     localhost
            username: login
            password: passwd
            path:     /path/to/project
            rules:    [eclipse, symfony]
            ignore:   [/bin/*]
        production:
            type:     ftp
            // ...
            

###Rules configuration

Rules can be declared as templates for reuse in your servers configuration.
Some templates are already bundled by default. The following parameters can be used :

-  ignore : masks for the files to be ignored
-  force : ignored files can be forced this way
-  commands : a list of commands to execute on the remote server


###Servers configuration

Here is the full list of parameters :

-  type : synchronisation type (FTP and Rsync for now)
-  host / username / password : login informations
-  path : the path for your application root on the remote server
-  rules : list of rules templates to apply
-  ignore / force / commands : local rules for convenience


How to use
----------


###Using the commands

The simpliest way to deploy your application is to use the command line,
go into your project root folder and type the following commands :

    # Test your deployment :
    php app/console deployment:test [server]
    
    # Launch your deployment :
    php app/console deployment:launch [server]
    
    
###Using the service

You can also use the deployment feature within your controller
by invoking the 'deployment' service :

    // Test your deployment :
    $this->get('deployment')->test([$server]);
    
    // Launch your deployment :
    $this->get('deployment')->launch([$server]);