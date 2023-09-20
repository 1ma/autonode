export SPARROW_VERSION=1.7.9

# TODO the architecture is not necessarily amd64

wget -q -O- https://keybase.io/craigraw/pgp_keys.asc | gpg --import
wget -q https://github.com/sparrowwallet/sparrow/releases/download/${SPARROW_VERSION}/sparrow-server_${SPARROW_VERSION}-1_amd64.deb
wget -q https://github.com/sparrowwallet/sparrow/releases/download/${SPARROW_VERSION}/sparrow-${SPARROW_VERSION}-manifest.txt
wget -q https://github.com/sparrowwallet/sparrow/releases/download/${SPARROW_VERSION}/sparrow-${SPARROW_VERSION}-manifest.txt.asc
gpg --verify sparrow-${SPARROW_VERSION}-manifest.txt.asc sparrow-${SPARROW_VERSION}-manifest.txt
sha256sum --ignore-missing --check sparrow-${SPARROW_VERSION}-manifest.txt

sudo dpkg -i sparrow-server_${SPARROW_VERSION}-1_amd64.deb
sudo ln -s /opt/sparrow/bin/Sparrow /usr/local/bin/Sparrow
