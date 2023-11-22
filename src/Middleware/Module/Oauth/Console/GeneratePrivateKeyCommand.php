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
class GeneratePrivateKeyCommand extends SymfonyCommand
{
    /**
     * @var string
     */
    public static $NAME_FILE = 'dsign-oauth-private.key';

    /**
     * @var string
     */
    protected static $defaultName = 'oauth:generate-private-key';

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
        $passphraseString = '';
        if ($passphrase) {
            $passphraseString = '-passout pass:' . $passphrase .' ' ;
        }

        shell_exec('openssl genrsa ' . $passphraseString . '-out key/' . GeneratePrivateKeyCommand::$NAME_FILE . ' 2048');
        chmod(  'key/' . GeneratePrivateKeyCommand::$NAME_FILE, 0600 );
        // TODO catch error
        return 0;
    }
}