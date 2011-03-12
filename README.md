Symfony2 applications deployment made easy
==========================================


**This bundle is still under developpment, unusable for now!**

-  DeploymentBundle supports many types of file synchronisation (rsync in ssh mode).
-  You can setup many deployment configuration for many servers.
-  You can provide `ignore` and `force` rules to control wich files are synchronized (in an easy way).
-  You can schedule commands executed after the deployment (on the remote server).


How to install
--------------


###Get the sources (using git)

Use the submodule git command if your project is under git control, if not, just use the clone command.

-  For stable release:

    git submodule add git://github.com/BeSimple/DeploymentBundle.git src/BeSimple/DeploymentBundle
    
-  For nightly build:

    git submodule add git://github.com/jfsimon/DeploymentBundle.git src/BeSimple/DeploymentBundle


###Add it to your application

You have to add the bundle to your `AppKernel` class.
Notice that the service is loaded only if you setted up the servers configuration in yous config file.

    public function registerBundles()
    {
        return array(
            // ...
            new BeSimple\DeploymentBundle\BeSimpleDeploymentBundle(),
            // ...
        );
    }
    

How to configure
----------------


###An example

    deployment:
    
        rsync:
            delete:       true
    
        rules:
            eclipse:
                ignore:   [.settings, .buildpath, .project]
            git:
                ignore:   [.git, .git*, .svn]
            symfony:
                ignore:   [/app/logs, /app/cache, /web/uploads, /web/*_dev.php]
                
        commands:
            cache_warmup:
                type:     symfony
                command:  cache:warmup
            fix_perms:
                type:     shell
                command:  ./bin/fix_perms.sh

        servers:
            staging:
                host:     localhost
                username: login
                password: passwd
                path:     /path/to/project
                rules:    [eclipse, symfony]
                commands: [cache_warmup, fix_perms]
            production:
                # ...
            

###Rsync configuration

To be continued.


###SSH configuration

To be continued.


###Rules configuration

Rules can be declared as templates for reuse in your servers configuration.
Some templates are already bundled by default. The following parameters can be used :

-  ignore : masks for the files to be ignored
-  force : ignored files can be forced this way


###Commands configuration

To be continued.


###Servers configuration

Here is the full list of parameters :

-  host / rsync_port / ssh_port / username / password : rsync & ssh login informations
-  path : the path for your application root on the remote server
-  rules : list of rules templates to apply
-  commands : list of commands to trigger on destination server


How to use
----------


###Using the commands

The simpliest way to deploy your application is to use the command line,
go into your project root folder and type the following commands :

    # Test your deployment :
    ./app/console deployment:test [server]
    
    # Launch your deployment :
    ./app/console deployment:launch [server]
    
    
###Using the service

You can also use the deployment feature within your controller
by invoking the 'deployment' service :

    // Test your deployment :
    $this->get('besimple_deployment')->test([$server]);
    
    // Launch your deployment :
    $this->get('besimple_deployment')->launch([$server]);