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
        delete:     true
      rules:
        eclipse:
          ignore:   [.settings, .buildpath, .project]
        netbeans:
          ignore:   [nbproject]
        phpstorm:
          ignore:   [.idea]
        git:
          ignore:   [.git, .git*]
        svn:
          ignore:   [.svn]
        symfony:
          ignore:   [/app/cache/*, /app/logs/*, /app/config/parameters.yml, /web/bundles/*, /web/uploads/*, /web/js/*, /web/css/*]
        hosting:
          ignore:   [/.htaccess, /.htpasswd, /web/.htaccess, /web/.user.ini, /web/manage.php, /web/phpinfo.php, /web/ntunnel_mysql.php]
        system:
          ignore:   [._*, .DS_Store]
      commands:
        cache_clear:
          type:     symfony
          command:  cache:clear
        assetic_dump:
          type:     symfony
          command:  assetic:dump
        assets_install:
          type:     symfony
          command:  assets:install
      ssh:
        connect_methods:
          server_to_client:
             crypt: rijndael-cbc@lysator.liu.se, aes256-cbc, aes192-cbc, aes128-cbc, 3des-cbc, blowfish-cbc, cast128-cbc, arcfour
          client_to_server:
             crypt: rijndael-cbc@lysator.liu.se, aes256-cbc, aes192-cbc, aes128-cbc, 3des-cbc, blowfish-cbc, cast128-cbc, arcfour
      servers:
        dev:
          host:         dev.server.ch
          username:     username

          pubkey_file:  %deploy_dev_pubkey_file%
          privkey_file: %deploy_dev_privkey_file%
          passphrase:   %deploy_dev_passphrase%

          path:         /home/user/www/dev.project.com/
          rules:        [eclipse, netbeans, phpstorm, git, svn, symfony, hosting, system]
          commands:     [cache_clear, assetic_dump, assets_install]

          symfony_command: php -c web/.user.ini app/console --env=dev
        prod:
          host:         prod.server.ch
          username:     username

          pubkey_file:  %deploy_prod_pubkey_file%
          privkey_file: %deploy_prod_privkey_file%
          passphrase:   %deploy_prod_passphrase%

          path:         /home/user/www/prod.project.com/
          rules:        [eclipse, netbeans, phpstorm, git, svn, symfony, hosting, system]
          commands:     [cache_clear_dev, assetic_dump, assets_install]

          symfony_command: php -c web/.user.ini app/console --env=prod

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
