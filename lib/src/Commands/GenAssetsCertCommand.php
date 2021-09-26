<?php

namespace Lib\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenAssetsCertCommand extends Command
{
    protected static $defaultName = 'gen-assets-cert';
    protected static $defaultDescription = 'Generate keypair for assets signing';

    protected function configure()
    {
        $this->addOption('rewrite');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('rewrite') && is_file(DATA_DIR . '/yggdrasil_session_private.pem')) {
            $output->writeln('Private key file already exist and --rewrite option is not specified');
            return self::FAILURE;
        }

        $private_key_res = openssl_pkey_new(array(
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        //Save private key
        if (openssl_pkey_export($private_key_res, $export)) {
            file_put_contents(DATA_DIR . '/yggdrasil_session_private.pem', $export);
        } else {
            $output->writeln(openssl_error_string());
            return self::FAILURE;
        }

        //Save public key
        $details = openssl_pkey_get_details($private_key_res);

        //Save in PEM format just in case
        file_put_contents(DATA_DIR . '/yggdrasil_session_public.pem', $details['key']);

        //Convert PEM > DER
        $lines = explode("\n", $details['key']);
        array_shift($lines); array_pop($lines); array_pop($lines);
        file_put_contents(DATA_DIR . '/yggdrasil_session_public.der', base64_decode(implode('', $lines)));

        $output->writeln('Successfully generated certificate files');
        $output->writeln('Data dir: ' . DATA_DIR);
        return self::SUCCESS;
    }
}