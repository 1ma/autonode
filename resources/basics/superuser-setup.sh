#!/usr/bin/env bash

set -euo pipefail

# Disable apt news
sudo pro config set apt_news=false

# Set up firewall
sudo ufw enable
sudo ufw allow 22/tcp comment 'allow SSH connections'
