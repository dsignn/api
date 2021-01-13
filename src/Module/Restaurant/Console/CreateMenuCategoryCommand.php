<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Console;

use App\Crypto\CryptoInterface;
use App\Storage\StorageInterface;
use Laminas\Hydrator\HydratorInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMenuCategoryCommand extends SymfonyCommand {
    /**
     * @var string
     */
    protected static $defaultName = 'restaurant:create-category';

    /**
     * CreateClientCommand constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage) {

        $this->storage = $storage;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $categories = $this->storage->getHydrator()->hydrate(
            $this->getCategory(),
            $this->storage->getEntityPrototype()->getPrototype()
        );

        $this->storage->update($categories);
        return 0;
    }

    /**
     * @return array
     */
    protected function getCategory() {

        $cateory = [
            '_id' => 'category',
            'starters' => [
                'it' => 'Antipasti',
                'en' => 'Starters'
            ],
            'first-courses' => [
                'it' => 'Primi piatti',
                'en' => 'First courses'
            ],
            'main-courses' => [
                'it' => 'Secondi piatti',
                'en' => 'Main courses'
            ],
            'pizza' => [
                'it' => 'Pizza',
                'en' => 'Pizza'
            ],
            'sandwiches' => [
                'it' => 'Panini',
                'en' => 'Sandwiches'
            ],
            'soups' => [
                'it' => 'Zuppe',
                'en' => 'Soups'
            ],
            'side-dishes' => [
                'it' => 'Contorni',
                'en' => 'Side dishes'
            ],
            'desserts' => [
                'it' => 'Dolci',
                'en' => 'Desserts'
            ],
            'beverage' => [
                'it' => 'Bevande',
                'en' => 'Beverage'
            ],
            'beer' => [
                'it' => 'Birra',
                'en' => 'Beer'
            ],
            'wine' => [
                'it' => 'Vini',
                'en' => 'Wine'
            ],
            'cocktail' => [
                'it' => 'Cocktail',
                'en' => 'Cocktail'
            ],
            'bitter' => [
                'it' => 'Amaro',
                'en' => 'Bitter'
            ],
        ];

        return $cateory;
    }
}