<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Console;

use App\Storage\StorageInterface;
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
                    "name" => "single-dish",
                    'order' => 3,
                    "translation" => [
                        'it' => 'Piatto unico',
                        'en' => 'Single dish'
                    ]
                ],
                [
                    "name" => "plateau",
                    'order' => 4,
                    "translation" => [
                        'it' => 'Plateau',
                        'en' => 'Plateau'
                    ]
                ],
                [
                    "name" => "raw-fish",
                    'order' => 5,
                    "translation" => [
                        'it' => 'Crudo di pesce',
                        'en' => 'Raw fish'
                    ]
                ],
                [
                    "name" => "pizza",
                    'order' => 6,
                    "translation" => [
                        'it' => 'Pizza',
                        'en' => 'Pizza'
                    ]
                ],
                [
                    "name" => "sandwiches",
                    'order' => 7,
                    "translation" => [
                        'it' => 'Panini',
                        'en' => 'Sandwiches'
                    ]
                ],
                [
                    "name" => "soups",
                    'order' => 8,
                    "translation" => [
                        'it' => 'Zuppe',
                        'en' => 'Soups'
                    ]
                ],
                [
                    "name" => "side-dishes",
                    'order' => 9,
                    "translation" => [
                        'it' => 'Contorni',
                        'en' => 'Side dishes'
                    ]
                ],
                [
                    "name" => "uramaki",
                    'order' => 10,
                    "translation" => [
                        'it' => 'Uramaki',
                        'en' => 'Uramaki'
                    ]
                ],
                [
                    "name" => "hosomaki",
                    'order' => 11,
                    "translation" => [
                        'it' => 'Hosomaki',
                        'en' => 'Hosomaki'
                    ]
                ],
                [
                    "name" => "futomaki",
                    'order' => 12,
                    "translation" => [
                        'it' => 'Futomaki',
                        'en' => 'Futomaki'
                    ]
                ],
                [
                    "name" => "temaki",
                    'order' => 13,
                    "translation" => [
                        'it' => 'Temaki',
                        'en' => 'Temaki'
                    ]
                ],
                [
                    "name" => "sashimi",
                    'order' => 14,
                    "translation" => [
                        'it' => 'Sashimi',
                        'en' => 'Sashimi'
                    ]
                ],
                [
                    "name" => "nigiri",
                    'order' => 15,
                    "translation" => [
                        'it' => 'Nigiri',
                        'en' => 'Nigiri'
                    ]
                ],
                [
                    "name" => "gunkan",
                    'order' => 16,
                    "translation" => [
                        'it' => 'Gunkan',
                        'en' => 'Gunkan'
                    ]
                ],
                [
                    "name" => "onigiri",
                    'order' => 17,
                    "translation" => [
                        'it' => 'Onigiri',
                        'en' => 'Onigiri'
                    ]
                ],
                [
                    "name" => "tempura",
                    'order' => 18,
                    "translation" => [
                        'it' => 'Tempura',
                        'en' => 'Tempura'
                    ]
                ],
                [
                    "name" => "desserts",
                    'order' => 19,
                    "translation" => [
                        'it' => 'Dolci',
                        'en' => 'Desserts'
                    ]
                ],
                [
                    "name" => "beverage",
                    'order' => 20,
                    "translation" => [
                        'it' => 'Bevande',
                        'en' => 'Beverage'
                    ]
                ],
                [
                    "name" => "beer",
                    'order' => 21,
                    "translation" => [
                        'it' => 'Birra',
                        'en' => 'Beer'
                    ]
                ],
                [
                    "name" => "wine",
                    'order' => 22,
                    "translation" => [
                        'it' => 'Vini',
                        'en' => 'Wine'
                    ]
                ],

                [
                    "name" => "cocktail",
                    'order' => 23,
                    "translation" => [
                        'it' => 'Cocktail',
                        'en' => 'Cocktail'
                    ]
                ],
                [
                    "name" => "bitter",
                    'order' => 24,
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