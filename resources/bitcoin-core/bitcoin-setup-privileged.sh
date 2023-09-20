# Allow super user to use bitcoin-cli
mkdir ~/.bitcoin
echo "datadir=/home/bitcoin/data" > ~/.bitcoin/bitcoin.conf

# Allow bitcoin service to use tor
sudo usermod -aG debian-tor bitcoin

# Install bitcoin binaries
sudo make -C /home/bitcoin/code install

# Enable systemd service
sudo systemctl enable bitcoin.service
