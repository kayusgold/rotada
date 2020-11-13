<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/rsync.php';

set('application', 'Rotada');
set('ssh_multiplexing', true);

set('rsync_src', function () {
    return __DIR__;
});


add('rsync', [
    'exclude' => [
        '.git',
        '/.env',
        '/storage/',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
    ],
]);

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path') . '/shared');
});

host('eazibank.ml')
  ->hostname('162.0.233.202')
  ->stage('production')
  ->user('eazibank')
  ->set('deploy_path', '~/public_html');

host('sandbox.eazibank.ml')
  ->hostname('162.0.233.202')
  ->stage('staging')
  ->user('root')
  ->set('deploy_path', '~/public_html');

after('deploy:failed', 'deploy:unlock');

desc('Deploy the application');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:secrets',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'artisan:queue:restart',
    #'artisan:passport:install',
    #'deploy:symlink',
    'deploy:copy:cp -R ~/public_html ~/public_html/current',
    'deploy:unlock',
    'cleanup',
]);

// task('deploy:update_code', function () {
//     upload('.', '~/public_html');
// });
//task('deploy:copy', 'cp -R ~/public_html ~/public_html/current');