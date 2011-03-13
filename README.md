Symfony2 applications deployment made easy
==========================================


**Untested bundle, watchout (maybe full of typos)**


A few words
-----------


###Description

-  Deploy your project usinc rsync (must be installed) in ssh mode.
-  Setup deployment on many servers.
-  Easily create rules for rsync (ignore / force files).
-  Schedule commands to run on ditant server via ssh (SSH2 PHP extension required).


###Links

-  Stable releases : [https://github.com/BeSimple/DeploymentBundle](https://github.com/BeSimple/DeploymentBundle)
-  Nightly builds : [https://github.com/jfsimon/DeploymentBundle](https://github.com/jfsimon/DeploymentBundle)
-  Rest documentation : *will come later*


###Requirements

-  Rsync package : [http://samba.anu.edu.au/rsync/](http://samba.anu.edu.au/rsync/)
-  SSH2 extension : [http://fr.php.net/manual/en/book.ssh2.php](http://fr.php.net/manual/en/book.ssh2.php)


How to install
--------------


1.  Get the sources via GIT

    - Use clone method if not using GIT for your project

        git clone git://github.com/BeSimple/DeploymentBundle.git vendor/BeSimple/DeploymentBundle
        
    - Use submodule method if this is the case
    
        git submodule add git://github.com/BeSimple/DeploymentBundle.git vendor/BeSimple/DeploymentBundle


2.  Register bundle in `AppKernel` class

        // app/AppKernel.php
        
        $bundles = array(
            // ...
            new BeSimple\DeploymentBundle\BeSimpleDeploymentBundle(),
            // ...
        );
        

3.  Add `besimple_deployment` entry to your config file

        # app/config.yml
        
        besimple_deployment:
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

    deployment:
    
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
    

###Rsync events

Subject of these events is the `besimple.rsync` service.


**besimple_deployment.rsync.start**: fired when rsync is started. Come with the following parameters:

-  command: The command line


**besimple_deployment.rsync.success**: fired on rsync success. Come with the following parameters:

-  lines: The `stdout` lines as array


**besimple_deployment.rsync.error**: fired when rsync enconter an error. Come with the following parameters:

-  code: The error code
-  message: The error message


**besimple_deployment.rsync.line**: fired on each rsync `stdout` or `stderr` line. Come with the following parameters:

-  type: `out` or `err`
-  line: The text line


###SSH events

Subject of these events is the `besimple.ssh` service


**besimple_deployment.ssh.start**: fired when ssh session is started. Come with the following parameters:

-  shell: The shell connection resource


**besimple_deployment.ssh.success**: fired on ssh commands success. Come with the following parameters:

-  lines: The `stdout` lines as array


**besimple_deployment.ssh.error**: fired when ssh encounter an error. Come with the following parameters:

-  code: The error code
-  message: The error message


**besimple_deployment.ssh.command**: fired on each ssh command. Come with the following parameters:

-  command: The command line
-  stdout: The `stdout` lines as array
-  stderr: The `stderr` lines as array