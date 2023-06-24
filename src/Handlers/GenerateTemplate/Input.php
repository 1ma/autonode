<?php

declare(strict_types=1);

namespace AutoNode\Handlers\GenerateTemplate;

final class Input
{
    public string $admin;
    public string $hostname;
    public string $locale;
    public array $sshKeys;
    public bool $withTor;
    public bool $withNginx;
    public bool $withSparrow;
    public bool $withWireGuard;
    public array $bitcoinCore;
    public array $electrs;
    public array $btcRpcExplorer;
    public array $coreLightning;
    public array $rideTheLightning;

    private function __construct()
    {
    }

    public static function fromForm(array $form): self
    {
        $input = new self();

        $input->admin = $form['admin-username'];
        $input->hostname = $form['hostname'];
        $input->locale = $form['locale'];
        $input->withTor = ($form['with-tor-services'] ?? '') === 'on';
        $input->withNginx = ($form['with-nginx'] ?? '') === 'on';
        $input->withSparrow = ($form['with-sparrow'] ?? '') === 'on';
        $input->withWireGuard = ($form['with-wireguard'] ?? '') === 'on';

        $input->bitcoinCore = [];
        if ('none' !== $form['bitcoin-core-version']) {
            $input->bitcoinCore['version'] = $form['bitcoin-core-version'];
            $input->bitcoinCore['dbcache'] = (int) $form['bitcoin-core-dbcache'];
        }

        $input->electrs = [];
        if ('none' !== $form['electrs-version']) {
            $input->electrs['version'] = $form['electrs-version'];
        }

        $input->btcRpcExplorer = [];
        if ('none' !== $form['btcexp-version']) {
            $input->btcRpcExplorer['version'] = $form['btcexp-version'];
            $input->btcRpcExplorer['currency'] = $form['btcexp-currency'];
            $input->btcRpcExplorer['theme'] = $form['btcexp-theme'];
        }

        $input->coreLightning = [];
        if ('none' !== $form['cln-version']) {
            $input->coreLightning['cln'] = $form['electrs-version'];
        }

        $input->rideTheLightning = [];
        if ('none' !== $form['rtl-version']) {
            $input->rideTheLightning['version'] = $form['rtl-version'];
        }

        return $input;
    }
}
