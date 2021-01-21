<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Console;

use App\Crypto\CryptoInterface;
use App\Storage\Entity\EntityInterface;
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

        $this->storage->delete('category');
        $this->storage->getStorageAdapter()->update($this->getCategory());
        return 0;
    }

    /**
     * @return array
     */
    protected function getCategory() {

        $cateory = [
            "_id" => "category",
            "plates" => [
                [
                    "name" => "starters",
                    'order' => 0,
                    "translation" => [
                        'it' => 'Antipasti',
                        'en' => 'Starters'
                    ]
                ],
                [
                    "name" => "first-courses",
                    'order' => 0,
                    "translation" => [
                        'it' => 'Primi piatti',
                        'en' => 'First courses'
                    ]
                ],
                [
                    "name" => "main-courses",
                    'order' => 2,
                    "translation" => [
                        'it' => 'Secondi piatti',
                        'en' => 'Main courses'
                    ]
                ],
                [
                    "name" => "plateau",
                    'order' => 3,
                    "translation" => [
                        'it' => 'Plateau',
                        'en' => 'Plateau'
                    ]
                ],
                [
                    "name" => "raw-fish",
                    'order' => 4,
                    "translation" => [
                        'it' => 'Crudo di pesce',
                        'en' => 'Raw fish'
                    ]
                ],
                [
                    "name" => "pizza",
                    'order' => 5,
                    "translation" => [
                        'it' => 'Pizza',
                        'en' => 'Pizza'
                    ]
                ],
                [
                    "name" => "sandwiches",
                    'order' => 6,
                    "translation" => [
                        'it' => 'Panini',
                        'en' => 'Sandwiches'
                    ]
                ],
                [
                    "name" => "soups",
                    'order' => 7,
                    "translation" => [
                        'it' => 'Zuppe',
                        'en' => 'Soups'
                    ]
                ],
                [
                    "name" => "side-dishes",
                    'order' => 8,
                    "translation" => [
                        'it' => 'Contorni',
                        'en' => 'Side dishes'
                    ]
                ],
                [
                    "name" => "uramaki",
                    'order' => 9,
                    "translation" => [
                        'it' => 'Uramaki',
                        'en' => 'Uramaki'
                    ]
                ],
                [
                    "name" => "hosomaki",
                    'order' => 10,
                    "translation" => [
                        'it' => 'Hosomaki',
                        'en' => 'Hosomaki'
                    ]
                ],
                [
                    "name" => "futomaki",
                    'order' => 11,
                    "translation" => [
                        'it' => 'Futomaki',
                        'en' => 'Futomaki'
                    ]
                ],
                [
                    "name" => "temaki",
                    'order' => 12,
                    "translation" => [
                        'it' => 'Temaki',
                        'en' => 'Temaki'
                    ]
                ],
                [
                    "name" => "sashimi",
                    'order' => 13,
                    "translation" => [
                        'it' => 'Sashimi',
                        'en' => 'Sashimi'
                    ]
                ],
                [
                    "name" => "nigiri",
                    'order' => 14,
                    "translation" => [
                        'it' => 'Nigiri',
                        'en' => 'Nigiri'
                    ]
                ],
                [
                    "name" => "gunkan",
                    'order' => 15,
                    "translation" => [
                        'it' => 'Gunkan',
                        'en' => 'Gunkan'
                    ]
                ],
                [
                    "name" => "onigiri",
                    'order' => 16,
                    "translation" => [
                        'it' => 'Onigiri',
                        'en' => 'Onigiri'
                    ]
                ],
                [
                    "name" => "tempura",
                    'order' => 17,
                    "translation" => [
                        'it' => 'Tempura',
                        'en' => 'Tempura'
                    ]
                ],
                [
                    "name" => "desserts",
                    'order' => 18,
                    "translation" => [
                        'it' => 'Dolci',
                        'en' => 'Desserts'
                    ]
                ],
                [
                    "name" => "beverage",
                    'order' => 19,
                    "translation" => [
                        'it' => 'Bevande',
                        'en' => 'Beverage'
                    ]
                ],
                [
                    "name" => "beer",
                    'order' => 20,
                    "translation" => [
                        'it' => 'Birra',
                        'en' => 'Beer'
                    ]
                ],
                [
                    "name" => "wine",
                    'order' => 21,
                    "translation" => [
                        'it' => 'Vini',
                        'en' => 'Wine'
                    ]
                ],

                [
                    "name" => "cocktail",
                    'order' => 22,
                    "translation" => [
                        'it' => 'Cocktail',
                        'en' => 'Cocktail'
                    ]
                ],
                [
                    "name" => "bitter",
                    'order' => 23,
                    "translation" => [
                        'it' => 'Amaro',
                        'en' => 'Bitter'
                    ]
                ]
            ]
        ];

        return $cateory;
    }
}