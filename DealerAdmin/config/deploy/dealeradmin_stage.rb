set :stage, "dealeradmin_stage"
set :branch, "dealeradmin"
set :deploy_to, "/var/www/html/dealeradmin"
set :repo_url, "git@65.183.169.3:FCA/ORE.git"
set :log_level, :debug


server "100.24.129.237", user: "deployer", roles: %w{app web db}

role :app, %w{deployer@100.24.129.237}
role :web, %w{deployer@100.24.129.237}
role :db, %w{deployer@100.24.129.237}

set :ssh_options, {
    keys: %w(~/.ssh/id_rsa),
        forward_agent: false,
	user: 'deployer',
        auth_methods: %w(publickey),
      }