<?php
declare(strict_types=1);

namespace App\Module\Oauth\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GeneratePasswordCommand
 * @package App\Module\Oauth\Console
 */
class GeneratePasswordCommand extends SymfonyCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'oauth:generate-password';

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $password = shell_exec('vendor/bin/generate-defuse-key');
        $fp = fopen('key/dsign-oauth-password.txt', 'wr');
        fwrite($fp, $password);
        fclose($fp);
        chmod( 'key/dsign-oauth-password.txt' , 0600 );
        return 0;
    }
}