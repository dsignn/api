<?php
declare(strict_types=1);

namespace App\Module\Oauth\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeneratePrivateKeyCommand
 * @package App\Module\Oauth\Console
 */
class GeneratePublicKeyCommand extends SymfonyCommand
{
    /**
     * @var string
     */
    public static $NAME_FILE = 'dsign-oauth-public.key';

    /**
     * @var string
     */
    protected static $defaultName = 'oauth:generate-public-key';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->addArgument('passphrase', InputArgument::OPTIONAL, 'passphrase');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $passphrase = $input->getArgument('passphrase');
        $passphraseString = ' ';
        if ($passphrase) {
            $passphraseString = ' -passin pass:' . $passphrase  ;
        }
        shell_exec('openssl rsa -in key/' . GeneratePrivateKeyCommand::$NAME_FILE  . $passphraseString . '-pubout -out key/' . GeneratePublicKeyCommand::$NAME_FILE);
        chmod(  'key/' . GeneratePublicKeyCommand::$NAME_FILE, 0600 );
        // TODO catch error
        return 0;
    }
}