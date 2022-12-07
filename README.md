# AutoNode

A [cloud-init](https://cloud-init.io) template to build Bitcoin nodes automatically.

## Motivation

> "Ain't nobody got time to sit through [RaspiBolt](https://raspibolt.org/) more than once. Once you've learned how to build a Bitcoin node with your hands the next ones should just build themselves."
>
> — Me

## Node Services

| Service            | Version | Local Ports (127.0.0.1) | Tor Hidden Service | Nginx (0.0.0.0) | Depends on            |
|--------------------|---------|-------------------------|--------------------|-----------------|-----------------------|
| [Bitcoin Core]     | v23.0   | :8332 :8333             | :8333              | No              | Nothing               |
| [Electrs Server]   | v0.9.10 | :50001                  | :50001             | :50002 (TLS)    | Bitcoin Core          |
| [BTC RPC Explorer] | v3.3.0  | :3002                   | :80                | :3003 (TLS)     | Bitcoin Core, Electrs |

The OpenSSH server is also exposed as a Tor hidden service on port 22.

## Organization

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

journalctl -f -u bitcoin.service
journalctl -f -u electrs.service
journalctl -f -u btcexp.service

bitcoin-cli -netinfo 4
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
