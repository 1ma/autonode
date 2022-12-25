# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/jammy64"

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

  config.vm.network "forwarded_port", guest: 3000, host: 3000
  config.vm.network "forwarded_port", guest: 3003, host: 3003
  config.vm.network "forwarded_port", guest: 50002, host: 50002
end
