Symfony2 applications deployment made easy
==========================================

**This bundle is still under developpment, unusable for now!**

DeploymentBundle supports many types of file synchronisation (rsync, ftp, sftp etc ...),
many server configuration for one application and other usefull features.


How to install
--------------


###1. Get the code (using git) :

    git submodule add git://github.com/jfsimon/DeploymentBundle.git src/Bundle/DeploymentBundle


###2. Add it to your `AppKernell` class :

    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\DeployBundle\DeployBundle(),
            // ...
        );
    }
    

How to configure
----------------





How to use
----------


###Use the commands (with the console)

1.  Test your deployment :

    php app/console deployment:test [server]
    
2.  Launch your deployment :

    php app/console deployment:launch [server]
    
    
###Use the service (within your controller)

1.  Test your deployment :

    $this->get('deployment')->test([$server]);
    
2.  Launch your deployment :

    $this->get('deployment')->launch([$server]);