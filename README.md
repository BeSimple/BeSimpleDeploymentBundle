Symfony2 applications deployment made easy
==========================================


**This bundle is still under developpment, unusable for now!**

DeploymentBundle supports many types of file synchronisation (rsync, ftp, sftp etc ...),
many server configuration for one application and other usefull features to come.


How to install
--------------


**1. Get the code (using git) :**

    git submodule add git://github.com/jfsimon/DeploymentBundle.git src/Bundle/DeploymentBundle


**2. Add it to your `AppKernell` class :**

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


Here is the full example in the YAML format :

    deployment.rules:
        versioning: { ignore: [.git, .gitmodules, .gitignore, .svn] }
        symfony: { ignore: [/app/logs, /app/cache, /web/uploads] }

    deployment.servers:
        staging:
            type:     rsync
            host:     localhost
            username: login
            password: passwd
            path:     /path/to/project
            rules:    [versioning, symfony]
        production:
            type:     ftp
            // ...


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
    
    // 2. Launch your deployment :
    $this->get('deployment')->launch([$server]);