# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/jammy64"

  config.vm.synced_folder ".", "/vagrant", disabled: true

  config.vm.provider "virtualbox" do |vb|
    vb.cpus = 12
    vb.memory = 16384
  end

  config.vm.cloud_init :user_data do |cloud_init|
    cloud_init.content_type = "text/cloud-config"
    cloud_init.path = "./config.yml"
  end

  config.vm.hostname = "autonode.local"
  config.vm.network "public_network"
end
