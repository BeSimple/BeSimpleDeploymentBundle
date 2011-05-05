Symfony2 applications deployment made easy
==========================================


*Up to date thanks to jonaswouters*


A few words
-----------


###Description

-  Deploy your project usinc rsync (must be installed) in ssh mode.
-  Setup deployment on many servers.
-  Easily create rules for rsync (ignore / force files).
-  Schedule commands to run on ditant server via ssh (SSH2 PHP extension required).


###Links

-  Stable releases : [https://github.com/besimple/DeploymentBundle](https://github.com/besimple/DeploymentBundle)
-  Nightly builds : [https://github.com/jfsimon/DeploymentBundle](https://github.com/jfsimon/DeploymentBundle)
-  Rest documentation : *will come later*


###Requirements

-  Rsync package : [http://samba.anu.edu.au/rsync/](http://samba.anu.edu.au/rsync/)
-  SSH2 extension : [http://fr.php.net/manual/en/book.ssh2.php](http://fr.php.net/manual/en/book.ssh2.php)


How to install
--------------


1.  Get the sources via GIT

    - Use clone method if not using GIT for your project

        git clone git://github.com/besimple/DeploymentBundle.git vendor/BeSimple/DeploymentBundle

    - Use submodule method if this is the case

        git submodule add git://github.com/besimple/DeploymentBundle.git vendor/BeSimple/DeploymentBundle


2.  Register bundle in `AppKernel` class

        // app/AppKernel.php

        $bundles = array(
            // ...
            new BeSimple\DeploymentBundle\BeSimpleDeploymentBundle(),
            // ...
        );


3.  Add `besimple_deployment` entry to your config file

        # app/config.yml

        be_simple_deployment:
            rsync:    ~
            ssh:      ~
            rules:    ~
            commands: ~
            servers:  ~


4.  Add `BeSimple` namespace to autoload

        // app/autoload.php

        $loader->registerNamespaces(array(
            // ...
            'BeSimple' => __DIR__.'/../vendor',
            // ...
        ));


How to configure
----------------


###An example

    be_simple_deployment:

        rsync:
            delete:       true

        ssh:
            pubkey_file:  /home/me/.ssh/id_rsa.pub
            privkey_file: /home/me/.ssh/id_rsa
            passwphrase:  secret

        rules:
            eclipse:
                ignore:   [.settings, .buildpath, .project]
            git:
                ignore:   [.git, .git*, .svn]
            symfony:
                ignore:   [/app/logs/*, /app/cache/*, /web/uploads/*, /web/*_dev.php]

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


###Servers configuration

Here is the full list of parameters :

-  host :
-  rsync_port :
-  ssh_port :
-  username
-  password :
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

You can use the verbose option (`-v`) to get all feedback from rsync and
remote ssh commands.


###Using the service

You can also use the deployment feature within your controller
by invoking the 'deployment' service :

    // Test your deployment :
    $this->get('besimple_deployment')->test([$server]);

    // Launch your deployment :
    $this->get('besimple_deployment')->launch([$server]);

You can connect many events to know what's happening.

###Deployer events

-  **onDeploymentStart**   : fired on deployment start.
-  **onDeploymentSuccess** : fired on deployment success.


###Rsync events

-  **onDeploymentRsyncStart**    : fired when rsync is started.
-  **onDeploymentRsyncFeedback** : fired on each rsync `stdout` or `stderr` line.
-  **onDeploymentRsyncSuccess**  : fired on rsync success.


###SSH events

-  **onDeploymentSshStart**    : fired when SSH command run.
-  **onDeploymentSshFeedback** : fired on each SSH `stdout` or `stderr` line.
-  **onDeploymentSshSuccess**  : fired on SSH command success.
