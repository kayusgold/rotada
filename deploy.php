<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'my_project');

// Project repository
set('repository', 'git@github.com:kayusgold/rotada.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

set('ssh_multiplexing', false);

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts

host('eazibank.ml')
    ->user('eazibank')
    ->port(22)
    ->stage('production')
    #->identityFile('C:/Users/Loveycom/.ssh/id_rsa')
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->set('deploy_path', '/public_html');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

//before('deploy:symlink', 'artisan:migrate');
