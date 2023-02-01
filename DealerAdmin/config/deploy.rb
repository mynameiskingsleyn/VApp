# config valid for current version and patch releases of Capistrano
lock "~> 3.13.0"

set :application, "fca-ore"
#set :repo_url, "git@209.117.133.194:FCA/ORE.git"
#set :scm, "git"

# set :default_env, { path: "~/.rbenv/shims:~/.rbenv/bin:$PATH" }

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
#set :deploy_to, "/var/www/html/ore/"
set :deploy_via, :remote_cache
#set :repository_cache, "git_cache"

# Default value for :format is :airbrussh.
set :format, :airbrussh
#set :laravel_dotenv_file, '/var/www/html/secrets/.env'
#set :rvm_ruby_string, '1.29.4@ore'
#set :rvm_type, :user

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
set :pty, true

# Default value for :linked_files is []
# append :linked_files, "config/database.yml"
set :linked_files, %w{.env}

# Default value for linked_dirs is []
# append :linked_dirs, 
#     	"storage/app", 
#	"storage/debugbar", 
#	"storage/framework", 
#	"storage/logs"

# Default value for default_env is {}
 set :default_env, { path: "/usr/bin/ruby/:$PATH" }

# Default value for local_user is ENV['USER']
# set :local_user, -> { `git config user.name`.chomp }

# Default value for keep_releases is 5
 set :keep_releases, 5

# Uncomment the following to require manually verifying the host key before first deploy.
# set :ssh_options, verify_host_key: :secure

namespace :deploy do
    desc 'Get stuff ready prior to symlinking'
    task :compile_assets do
    end
    after :updated, :compile_assets
end

namespace :fpm do
  desc "Restart the PHP-FPM"
  task :restart do
     on roles(:web) do
          execute:sudo, "systemctl restart php-fpm"
    end
  end
end

namespace :nginx do
  desc "Restart Nginx Server"
  task :restart do
     on roles(:web) do 
     execute:sudo, "chown -R deployer:deployer /var/www/html"
     execute:sudo, "systemctl restart nginx"
    end
  end
end

namespace :deploy do
    after :published, "fpm:restart"
    after :published, "nginx:restart"
end


