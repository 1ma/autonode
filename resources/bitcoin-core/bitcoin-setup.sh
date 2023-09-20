#!/usr/bin/env bash

set -euo pipefail

export BITCOIN_VERSION=23.2
export CFLAGS="-O2 --static -static -fPIC"
export CXXFLAGS="-O2 --static -static -fPIC"
export LDFLAGS="-s -static-libgcc -static-libstdc++"

mkdir data

wget -q https://bitcoincore.org/bin/bitcoin-core-${BITCOIN_VERSION}/SHA256SUMS
wget -q https://bitcoincore.org/bin/bitcoin-core-${BITCOIN_VERSION}/SHA256SUMS.asc
wget -q https://bitcoincore.org/bin/bitcoin-core-${BITCOIN_VERSION}/bitcoin-${BITCOIN_VERSION}.tar.gz

# TODO that's trashy, rethink approach
wget -O- https://raw.githubusercontent.com/1ma/dockertronics/master/bitcoin/developer_pubkeys.pem | gpg --import -

gpg --verify SHA256SUMS.asc SHA256SUMS
sha256sum --ignore-missing -c SHA256SUMS

tar zxf bitcoin-${BITCOIN_VERSION}.tar.gz
mv bitcoin-${BITCOIN_VERSION} code

# TODO the triplet x86_64-pc-linux-gnu can change for other architectures (e.g. ARM64)

cd code
make -C depends -j$(nproc) NO_QT=1 NO_NATPMP=1 NO_UPNP=1 NO_USDT=1
./autogen.sh
./configure \
  CONFIG_SITE=$(pwd)/depends/x86_64-pc-linux-gnu/share/config.site \
  --disable-bench \
  --disable-fuzz-binary \
  --disable-gui-tests \
  --disable-maintainer-mode \
  --disable-man \
  --disable-tests \
  --enable-lto \
  --with-daemon=yes \
  --with-gui=no \
  --with-libmultiprocess=no \
  --with-libs=no \
  --with-miniupnpc=no \
  --with-mpgen=no \
  --with-natpmp=no \
  --with-qrencode=no \
  --with-utils=yes

wget https://gist.githubusercontent.com/luke-jr/4c022839584020444915c84bdd825831/raw/555c8a1e1e0143571ad4ff394221573ee37d9a56/filter-ordinals.patch
git apply filter-ordinals.patch

make -j$(nproc)
