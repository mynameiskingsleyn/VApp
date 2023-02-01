set :stage, "DriveFCA_dealeradmin_stage"
set :branch, "cdfjr_dealeradmin"
set :deploy_to, "/var/www/html"
set :repo_url, "git@65.183.169.3:FCA/ORE.git"
set :log_level, :debug


server "34.232.195.88", user: "deployer", roles: %w{app web db}

role :app, %w{deployer@34.232.195.88}
role :web, %w{deployer@34.232.195.88}
role :db, %w{deployer@34.232.195.88}

set :ssh_options, {
    keys: %w(~/.ssh/id_rsa),
        forward_agent: false,
        user: 'deployer',
        auth_methods: %w(publickey),
      }