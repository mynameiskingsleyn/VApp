# server-based syntax
# ======================
# Defines a single server with a list of roles and multiple properties.
# You can define all roles on a single server, or split them:

set :stage, :BlackFriday
set :branch, :cdjfr
set :deploy_to, "/var/www/html"
set :repo_url, "git@65.183.169.3:FCA/ORE.git"
set :log_level, :debug


server "52.73.11.183", user: "deployer", roles: %w{app web db}

role :app, %w{deployer@52.73.11.183}
role :web, %w{deployer@52.73.11.183}
role :db, %w{deployer@52.73.11.183}

set :ssh_options, {
    keys: %w(~/.ssh/id_rsa),
        forward_agent: false,
	user: 'deployer',
        auth_methods: %w(publickey),
      }




