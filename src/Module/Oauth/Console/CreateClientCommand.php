<?php
declare(strict_types=1);

namespace App\Module\Oauth\Console;

use App\Crypto\CryptoInterface;
use App\Module\Oauth\Entity\ClientEntity;
use App\Storage\StorageInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * Class CreateClientCommand
 * @package App\Module\Oauth\Console
 */
class CreateClientCommand extends SymfonyCommand {

    /**
     * @var string
     */
    protected static $defaultName = 'oauth:create-client';

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var CryptoInterface
     */
    protected $crypto;

    /**
     * CreateClientCommand constructor.
     * @param StorageInterface $storage
     * @param CryptoInterface $crypto
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(StorageInterface $storage, CryptoInterface $crypto) {

        $this->storage = $storage;
        $this->crypto = $crypto;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the client oauth');
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The identifier of the client oauth');
        $this->addArgument('password', InputArgument::REQUIRED, 'The password of the client oauth');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $identifier = $input->getArgument('identifier');
        $password = $input->getArgument('password');

        $client = new ClientEntity();
        $client->setName($name);
        $client->setIdentifier($identifier);
        $client->setPassword($this->crypto->crypto($password));

        try {
            $this->storage->save($client);
            $output->writeln('Client ' . $name . ' saved');
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return 1;
        }
    }
}