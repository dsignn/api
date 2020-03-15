<?php
declare(strict_types=1);

namespace App\Module\User\Console;

use App\Crypto\CryptoInterface;
use App\Module\User\Entity\UserEntity;
use App\Storage\StorageInterface;
use Laminas\Hydrator\HydratorInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateUserCommand
 * @package App\Module\User\Console
 */
class CreateUserCommand extends SymfonyCommand {

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var CryptoInterface
     */
    protected $crypto;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var string
     */
    protected static $defaultName = 'user-repo:create';

    public function __construct(StorageInterface $storage, CryptoInterface $crypto)
    {
        $this->storage = $storage;
        $this->crypto = $crypto;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure() {
        $this->addArgument('email', InputArgument::REQUIRED, 'The email of the user entity');
        $this->addArgument('password', InputArgument::REQUIRED, 'The password of the user entity');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = new UserEntity();
        $user->setEmail($email);
        $user->setPassword($this->crypto->crypto($password));

        try {
            $this->storage->save($user);
            $output->writeln('User ' . $email . ' saved');
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return 1;
        }
    }
}