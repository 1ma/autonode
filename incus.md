Incus commands for development:

```shell
incus launch images:debian/12/cloud autonode --config=cloud-init.user-data="$(cat incus.yml)"
incus exec autonode -- tail -200f /var/log/cloud-init-output.log
incus exec autonode -- journalctl -o cat -n 100 -f -u bitcoind.service
incus shell autonode
incus rm --force autonode
```
