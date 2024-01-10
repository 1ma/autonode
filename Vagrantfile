# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/noble64"

  # Reads .env file from the current directory
  config.env.enable

  config.vm.provider "virtualbox" do |vb|
    vb.cpus = 12
    vb.memory = 16384
  end

  config.vm.cloud_init :user_data do |cloud_init|
    cloud_init.content_type = "text/cloud-config"
    cloud_init.path = "./config.yml"
  end

  config.vm.network "forwarded_port", guest: 3010, host: 3010
  config.vm.network "forwarded_port", guest: 3012, host: 3012
  config.vm.network "forwarded_port", guest: 50011, host: 50011
end
