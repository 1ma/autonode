<?php

declare(strict_types=1);

namespace AutoNode\Handlers\GenerateTemplate;

final class Input
{
    public bool $x86install;
    public string $admin;
    public string $hostname;
    public string $locale;
    public array $sshKeys;
    public bool $withSparrow;
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

        $input->x86install = ($form['x86-install'] ?? '') === 'on';
        $input->admin = $form['admin-username'];
        $input->hostname = $form['hostname'];
        $input->locale = $form['locale'];
        $input->withSparrow = ($form['with-sparrow'] ?? '') === 'on';

        self::parseSSH($input, array_filter($form, fn (string $key): bool => str_starts_with($key, 'ssh-'), \ARRAY_FILTER_USE_KEY));

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

    private static function parseSSH(self $input, array $form): void
    {
        $types = array_filter($form, fn (string $key): bool => str_starts_with($key, 'ssh-type-'), \ARRAY_FILTER_USE_KEY);
        ksort($types);

        $data = array_filter($form, fn (string $key): bool => str_starts_with($key, 'ssh-data-'), \ARRAY_FILTER_USE_KEY);
        ksort($data);

        $types = array_values($types);
        $data = array_values($data);

        for ($i = 0; $i < \count($types); ++$i) {
            $input->sshKeys[] = [
                'type' => $types[$i],
                'data' => $data[$i],
            ];
        }
    }
}
