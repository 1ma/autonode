# AutoNode

A [cloud-init](https://cloud-init.io) template to build Bitcoin nodes automatically.

## Motivation

> "Ain't nobody got time to sit through [RaspiBolt](https://raspibolt.org/) more than once. Once you've learned how to build a Bitcoin node with your hands the next ones should just build themselves."
>
> — Me

## Node Services

| Service              | Version  | Local Ports (127.0.0.1) | Tor Hidden Service | Nginx TLS (0.0.0.0) | Depends on            |
|----------------------|----------|-------------------------|--------------------|---------------------|-----------------------|
| [Bitcoin Core]       | v23.1    | :8333 :8332 (rpc)       | :8333              | No                  | Nothing               |
| [Electrs Server]     | v0.9.10  | :50001                  | :50001             | :50011              | Bitcoin Core          |
| [BTC RPC Explorer]   | v3.3.0   | :3002                   | :80                | :3012               | Bitcoin Core, Electrs |
| [Core Lightning]     | v22.11.1 | :9736                   | :9736              | No                  | Bitcoin Core          |
| [c-lightning-REST]   | v0.9.0   | :3001 :4001 (doc)       | :3001 :4001 (doc)  | :3011 :4011 (doc)   | Core Lightning        |
| [Ride The Lightning] | v0.13.3  | :3000                   | :80                | :3010               | Core Lightning        |

The OpenSSH server is also exposed as a Tor hidden service on port 22.

### Other Software

* Sparrow Server 1.7.1
* WireGuard
* tmux
* rust-teos Watchtower plugin for Core Lightning

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

In all cases using AutoNode involves providing the YAML template the first time the machine boots.
The specific procedure depends on where you want to use AutoNode.

### Use it on a Raspberry Pi (easy)

#### Requirements
* A Raspberry Pi 3 or 4
* An external disk of 1TB or more
* A microSD card


### Use it on a cloud provider (easy)

#### Requirements
* An account at AWS or any other cloud provider that supports cloud-init.

cloud-init was originally invented to provision Ubuntu servers on AWS EC2, accordingly this is one
of the easiest installation procedures.

From the EC2 panel, launch a new instance and provision enough extra storage for the blockchain.
When you see the "User Data" form simply copy-paste the YAML template into it. Done.

| ![AWS EC2 User Data form](docs/images/user-data-field.png) |
|:--:|
| *AWS user data form* |

### Use it on Vagrant (medium)

#### Requirements
* [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
* [Vagrant](https://developer.hashicorp.com/vagrant/downloads)

Vagrant is a good option to test your custom modifications to the cloud-init template before using it on
real hardware, or simply to get familiar with the installation process.

By default Vagrant creates virtual machines with 40GB of disk. Obviously this won't be enough to do a
full sync, but there's enough leeway to tinker with it, even for hours.

1. After installing Vagrant and VirtualBox, install the Vagrant env plugin: `vagrant plugin install vagrant-env`
2. Clone the AutoNode repo and cd into it.
3. Tweak the config.yml to your liking and the `vb.cpus` and `vb.memory` options from the Vagrantfile (might be too hefty for your machine).
4. Run `vagrant up`. This will take some time. While you wait you can open another terminal and log in as satoshi and tail the `/var/log/cloud-init-output.log` file.
5. After cloud-init completes `vagrant up` will end its execution with an error. This is normal, because cloud-init will reboot the virtual machine and Vagrant does not expect that.
6. After the second boot the node is ready to log in again. All services should be running.
7. When you're done, exit the virtual machine and run `vagrant destroy` on the same project directory.

### Use it on bare metal (hard)

#### Requirements
* Any sort of physical x86_64 machine

`python3 -m http.server 3000`
`autoinstall ds=nocloud-net\;s=http://192.168.1.62:3000/ ---`

## Post Installation

After the installation completes and the node boots for a second time you can log in with the admin user `satoshi`.
SSH password authentication is disabled out of the box, you must use a public SSH key to authenticate.

```shell
ssh satoshi@192.168.1.100
```

On Vagrant the SSH service will be exposed at localhost port 2222:

```shell
ssh -o UserKnownHostsFile=/dev/null -p 2222 satoshi@localhost
```

The `UserKnownHostsFile=/dev/null` option will prevent you from polluting your user's `.ssh/known_hosts`
file with ephemeral node public keys. You don't need to use this option when logging in to a real node.

You can also log in during the first boot and tail the cloud-init log file to be able to see what the node is exactly doing.
For that to work you usually need to wait a few seconds for cloud-init to register your public SSH key into the admin user.

```shell
ssh satoshi@192.168.1.100

# inside the node
sudo tail -f /var/log/cloud-init-output.log
```

### Check Services Health

After the second boot check the health of all services that AutoNode built:

```shell
systemctl status bitcoin.service
systemctl status electrs.service
systemctl status btcexp.service
systemctl status cln.service
systemctl status rtl.service

bitcoin-cli -getinfo
lightning-cli getinfo
```

If anything looks amiss you'll find any installation errors in these cloud-init log files:

```
/var/log/cloud-init-output.log
/var/log/cloud-init.log
```

From outside the node, you can try accessing a few of the HTTP services with your web browser.
Ride The Lightning and BTC RPC Explorer should be available straight away.
On Vagrant, they'll be accessible from `https://localhost:3010` and `https://localhost:3012`.
On a real installation you'll have to use the node IP.

### Lock down admin user

The default administrator account is created without a password.
This is useful during the cloud-init phase of bootstrapping the node, but not a very good practice.
You should set its password once you log in for the first time.

```shell
sudo passwd satoshi
```

Additionally, satoshi can use sudo without being asked for the password by default (even when you set one).
To make Ubuntu ask for your password every time you use sudo, remove the following file:

```shell
sudo rm /etc/sudoers.d/90-cloud-init-users
```

### Move Bitcoin and Electrs data directories to dedicated storage (Optional)


| ![Raspberry Pi 4 Bitcoin Node](docs/images/raspberry-node.jpg) |
|:--:|
| *A typical low end node sitting on its USB external drive* |

A lot of Bitcoin nodes run their OS on small disks and have a dedicated external drive to store the blockchain.
Since it is impossible for AutoNode to know where you want to store your data beforehand, it acts as if the
OS disk is large enough and sets up all services to store their data in their home folders.
If that's not the case for you you'll need to migrate Bitcoin and Electrs data directories to the dedicated disk drive
before you run out of space. You'll usually have plenty of time to do this unless you forget about it for a day or two.

[Formatting the external disk] and making sure that it [mounts automatically on each boot] is outside the scope of this guide.
In this walkthrough we assume that the external drive is already formatted and automatically mounted at boot at `/mnt/ssd`.

First, log in as the admin user and stop the bitcoin and electrs services:

```shell
satoshi@node01:~$ sudo systemctl stop bitcoin.service
satoshi@node01:~$ sudo systemctl stop electrs.service
```

With the services stopped, move their data directories to their new location.
The mv command shouldn't change the file ownership but double check anyway:

```shell
satoshi@node01:~$ sudo mv /home/bitcoin/data /mnt/ssd/bitcoin_data
satoshi@node01:~$ sudo mv /home/electrs/data /mnt/ssd/electrs_data

satoshi@node01:~$ ls -l /mnt/ssd/
total 16
drwxr-xr-x 4 root    root    4096 Dec 28 11:25 ./
drwxr-xr-x 3 root    root    4096 Dec 28 11:22 ../
drwxrwxr-x 5 bitcoin bitcoin 4096 Dec 28 11:22 bitcoin_data/
drwxrwxr-x 3 electrs electrs 4096 Dec 28 10:34 electrs_data/

satoshi@node01:~$ ls -l /mnt/ssd/electrs_data/
total 12
drwxrwxr-x 3 electrs electrs 4096 Dec 28 10:34 ./
drwxr-xr-x 4 root    root    4096 Dec 28 11:25 ../
drwxr-xr-x 2 electrs electrs 4096 Dec 28 10:35 bitcoin/

satoshi@node01:~$ ls -l /mnt/ssd/bitcoin_data/
total 736
drwxrwxr-x 5 bitcoin bitcoin   4096 Dec 28 11:22 ./
drwxr-xr-x 4 root    root      4096 Dec 28 11:25 ../
-rw------- 1 bitcoin bitcoin      0 Dec 28 10:34 .lock
-rw------- 1 bitcoin bitcoin    131 Dec 28 11:22 anchors.dat
-rw------- 1 bitcoin bitcoin     31 Dec 28 10:34 banlist.json
drwx------ 3 bitcoin bitcoin   4096 Dec 28 11:21 blocks/
drwx------ 2 bitcoin bitcoin   4096 Dec 28 11:22 chainstate/
-rw------- 1 bitcoin bitcoin 247985 Dec 28 11:22 fee_estimates.dat
-rw------- 1 bitcoin bitcoin    679 Dec 28 10:34 i2p_private_key
drwx------ 3 bitcoin bitcoin   4096 Dec 28 10:34 indexes/
-rw------- 1 bitcoin bitcoin     18 Dec 28 11:22 mempool.dat
-rw------- 1 bitcoin bitcoin     99 Dec 28 10:34 onion_v3_private_key
-rw------- 1 bitcoin bitcoin 458743 Dec 28 11:22 peers.dat
-rw-r--r-- 1 bitcoin bitcoin      4 Dec 28 10:34 settings.json
```

Now impersonate each service user and create a softlink where the `data` directory used to be, pointing to its new location.

```shell
satoshi@node01:~$ sudo su - bitcoin 

bitcoin@node01:~$ ln -s /mnt/ssd/bitcoin_data data
bitcoin@node01:~$ ls -l
total 10952
-rw-rw-r--  1 bitcoin bitcoin 11193071 Dec 20 20:01 bitcoin-23.1.tar.gz
-rw-r-----  1 bitcoin bitcoin      584 Dec 28 10:02 bitcoin.conf
-rwxr-xr-x  1 bitcoin bitcoin     1298 Dec 28 10:02 bitcoin-setup.sh
drwxrwxr-x 15 bitcoin bitcoin     4096 Dec 28 10:04 code
lrwxrwxrwx  1 bitcoin bitcoin       21 Dec 28 11:28 data -> /mnt/ssd/bitcoin_data
-rw-rw-r--  1 bitcoin bitcoin     2842 Dec 20 19:57 SHA256SUMS
-rw-rw-r--  1 bitcoin bitcoin     3569 Dec 20 19:57 SHA256SUMS.asc
bitcoin@node01:~$ exit
logout

satoshi@node01:~$ sudo su - electrs 

electrs@node01:~$ ln -s /mnt/ssd/electrs_data data
electrs@node01:~$ ls -l
total 12
drwxrwxr-x 12 electrs electrs 4096 Dec 28 10:10 code
lrwxrwxrwx  1 electrs electrs   21 Dec 28 11:30 data -> /mnt/ssd/electrs_data
-rw-r--r--  1 electrs electrs  260 Dec 28 10:02 electrs.conf
-rwxr-xr-x  1 electrs electrs  423 Dec 28 10:02 electrs-setup.sh
electrs@node01:~$ exit
logout
```

Start the services again and check their status and/or their log. They should resume normally.
Using this softlink approach you don't need to edit any path on any configuration file.

```shell
satoshi@node01:~$ sudo systemctl start bitcoin.service
satoshi@node01:~$ sudo systemctl start electrs.service

satoshi@node01:~$ systemctl status bitcoin.service
satoshi@node01:~$ systemctl status electrs.service

satoshi@node01:~$ journalctl -f -u bitcoin.service
satoshi@node01:~$ journalctl -f -u electrs.service
```

## FAQ

### Supported OS

I only test and support AutoNode on the latest Ubuntu Server LTS version (currently 22.04).
However cloud-init is supposed to work with a wide range of OSes and virtual cloud providers.

I'm interested in PRs that provide support for other OSes as long as they don't break Ubuntu Server.

### How long does the installation take?

The installation involves downloading software from the internet and compiling several binaries.
Therefore ample internet bandwidth, a powerful CPU with many cores and fast storage all make an impact.

While developing AutoNode I've seen installation runtimes as low as 5 minutes on my local workstation, to
about an hour on a low powered x86 box I used to test.

### Where is Bitcoin's debug.log file?

Bitcoin Core as configured by AutoNode will not create this file.
Instead, its output will be printed to standard output and picked by systemd, like any other system service.

If you want to peek at its output in real time just use journalctl:

```shell
satoshi@node01:~$ journalctl -f -u bitcoin.service
```

This approach has a couple of advantages. First, systemd takes care of managing the total log size like it
does for any other service. And second, the logs can be [queried in more useful ways], for instance:

```shell
satoshi@node01:~$ journalctl -u bitcoin.service --since "2022-12-27 15:10:00" --until "2022-12-27 15:15:00"
```


[Bitcoin Core]: https://github.com/bitcoin/bitcoin
[Electrs Server]: https://github.com/romanz/electrs
[BTC RPC Explorer]: https://github.com/janoside/btc-rpc-explorer
[Core Lightning]: https://github.com/ElementsProject/lightning
[c-lightning-REST]: https://github.com/Ride-The-Lightning/c-lightning-REST
[Ride The Lightning]: https://github.com/Ride-The-Lightning/RTL
[Formatting the external disk]: https://linuxconfig.org/how-to-add-new-disk-to-existing-linux-system
[mounts automatically on each boot]: https://linuxconfig.org/how-fstab-works-introduction-to-the-etc-fstab-file-on-linux
[queried in more useful ways]: https://linuxhandbook.com/journalctl-command/
