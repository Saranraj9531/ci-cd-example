<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'my_project');

// Project repository
set('repository', 'https://github.com/Saranraj9531/ci-cd-example.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts
host('54.166.28.191') // Name of the server
    ->hostname('54.166.28.191') // Hostname or IP address
    ->stage('production') // Deployment stage (production, staging, etc)
    ->user('root') // SSH user
    ->set('deploy_path', '/var/www/html'); // Deploy path    
    
after('deploy:failed', 'deploy:unlock'); // Unlock after failed deploy

desc('Deploy the application');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync', // Deploy code & built assets
    'deploy:secrets', // Deploy secrets
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link', // |
    'artisan:view:cache',   // |
    'artisan:config:cache', // | Laravel specific steps 
    'artisan:optimize',     // |
    'artisan:migrate',      // |
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

