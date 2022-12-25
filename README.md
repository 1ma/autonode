# AutoNode

A [cloud-init](https://cloud-init.io) template to build Bitcoin nodes automatically.

## Motivation

> "Ain't nobody got time to sit through [RaspiBolt](https://raspibolt.org/) more than once. Once you've learned how to build a Bitcoin node with your hands the next ones should just build themselves."
>
> — Me

## Node Services

| Service              | Version  | Local Ports (127.0.0.1) | Tor Hidden Service | Nginx (0.0.0.0, TLS) | Depends on            |
|----------------------|----------|-------------------------|--------------------|----------------------|-----------------------|
| [Bitcoin Core]       | v23.1    | :8333 :8332 (rpc)       | :8333              | No                   | Nothing               |
| [Electrs Server]     | v0.9.10  | :50001                  | :50001             | :50002               | Bitcoin Core          |
| [BTC RPC Explorer]   | v3.3.0   | :3002                   | :80                | :3003                | Bitcoin Core, Electrs |
| [Core Lightning]     | v22.11.1 | :9736                   | :9736              | No                   | Bitcoin Core          |
| [c-lightning-REST]   | v0.9.0   | :3001 :4001 (doc)       | :3001              | :3004                | Core Lightning        |
| [Ride The Lightning] | v0.13.3  | :2999                   | :80                | :3000                | Core Lightning        |

The OpenSSH server is also exposed as a Tor hidden service on port 22.

## Organization

```
/home/
├── bitcoin/
│   ├── bitcoin.conf
│   ├── bitcoin-setup.sh
│   ├── code/
│   └── data/
├── btcexp/
│   └── code/
├── electrs/
│   ├── code/
│   ├── data/
│   ├── electrs.conf
│   └── electrs-setup.sh
└── satoshi/
    └── superuser-setup.sh
```

## Installation

### Use it on a Raspberry Pi (easy)

### Use it on a cloud provider (easy)

### Use it on Vagrant (medium)

### Use it on bare metal (hard)

## Post Installation

### Check Services Health

```shell
systemctl status bitcoin.service
systemctl status electrs.service
systemctl status btcexp.service
systemctl status cln.service

bitcoin-cli -getinfo
lightning-cli getinfo
```

### Lock down admin user

```shell
sudo passwd satoshi
sudo rm /etc/sudoers.d/90-cloud-init-users
```

### Move Bitcoin and Electrs data directories to dedicated storage (Optional)

### Disable Bitcoin Core settings for the Initial Block Download (Post IBD)

## FAQ

### Supported OSes

Ubuntu Server 22.04

### cloud-init primer

#### Create and set up new users

#### Write files

#### Run arbitrary commands

[Bitcoin Core]: https://github.com/bitcoin/bitcoin
[Electrs Server]: https://github.com/romanz/electrs
[BTC RPC Explorer]: https://github.com/janoside/btc-rpc-explorer
[Core Lightning]: https://github.com/ElementsProject/lightning
[c-lightning-REST]: https://github.com/Ride-The-Lightning/c-lightning-REST
[Ride The Lightning]: https://github.com/Ride-The-Lightning/RTL
