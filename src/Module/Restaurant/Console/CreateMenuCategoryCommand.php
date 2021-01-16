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
                    "name" => "pizza",
                    'order' => 3,
                    "translation" => [
                        'it' => 'Pizza',
                        'en' => 'Pizza'
                    ]
                ],
                [
                    "name" => "sandwiches",
                    'order' => 4,
                    "translation" => [
                        'it' => 'Panini',
                        'en' => 'Sandwiches'
                    ]
                ],
                [
                    "name" => "soups",
                    'order' => 5,
                    "translation" => [
                        'it' => 'Zuppe',
                        'en' => 'Soups'
                    ]
                ],
                [
                    "name" => "side-dishes",
                    'order' => 6,
                    "translation" => [
                        'it' => 'Contorni',
                        'en' => 'Side dishes'
                    ]
                ],
                [
                    "name" => "uramaki",
                    'order' => 7,
                    "translation" => [
                        'it' => 'Uramaki',
                        'en' => 'Uramaki'
                    ]
                ],
                [
                    "name" => "hosomaki",
                    'order' => 8,
                    "translation" => [
                        'it' => 'Hosomaki',
                        'en' => 'Hosomaki'
                    ]
                ],
                [
                    "name" => "futomaki",
                    'order' => 9,
                    "translation" => [
                        'it' => 'Futomaki',
                        'en' => 'Futomaki'
                    ]
                ],
                [
                    "name" => "temaki",
                    'order' => 10,
                    "translation" => [
                        'it' => 'Temaki',
                        'en' => 'Temaki'
                    ]
                ],
                [
                    "name" => "sashimi",
                    'order' => 11,
                    "translation" => [
                        'it' => 'Sashimi',
                        'en' => 'Sashimi'
                    ]
                ],
                [
                    "name" => "nigiri",
                    'order' => 12,
                    "translation" => [
                        'it' => 'Nigiri',
                        'en' => 'Nigiri'
                    ]
                ],
                [
                    "name" => "gunkan",
                    'order' => 13,
                    "translation" => [
                        'it' => 'Gunkan',
                        'en' => 'Gunkan'
                    ]
                ],
                [
                    "name" => "onigiri",
                    'order' => 14,
                    "translation" => [
                        'it' => 'Onigiri',
                        'en' => 'Onigiri'
                    ]
                ],
                [
                    "name" => "tempura",
                    'order' => 15,
                    "translation" => [
                        'it' => 'Tempura',
                        'en' => 'Tempura'
                    ]
                ],
                [
                    "name" => "desserts",
                    'order' => 16,
                    "translation" => [
                        'it' => 'Dolci',
                        'en' => 'Desserts'
                    ]
                ],
                [
                    "name" => "beverage",
                    'order' => 17,
                    "translation" => [
                        'it' => 'Bevande',
                        'en' => 'Beverage'
                    ]
                ],
                [
                    "name" => "beer",
                    'order' => 18,
                    "translation" => [
                        'it' => 'Birra',
                        'en' => 'Beer'
                    ]
                ],
                [
                    "name" => "wine",
                    'order' => 19,
                    "translation" => [
                        'it' => 'Vini',
                        'en' => 'Wine'
                    ]
                ],

                [
                    "name" => "cocktail",
                    'order' => 20,
                    "translation" => [
                        'it' => 'Cocktail',
                        'en' => 'Cocktail'
                    ]
                ],
                [
                    "name" => "bitter",
                    'order' => 21,
                    "translation" => [
                        'it' => 'Amaro',
                        'en' => 'Bitter'
                    ]
                ]
            ]

        /*
            'starters' => [
                'order' => 0,
                'it' => 'Antipasti',
                'en' => 'Starters'
            ],
            'first-courses' => [
                'order' => 1,
                'it' => 'Primi piatti',
                'en' => 'First courses'
            ],
            'main-courses' => [
                'order' => 2,
                'it' => 'Secondi piatti',
                'en' => 'Main courses'
            ],

            'pizza' => [
                'order' => 3,
                'it' => 'Pizza',
                'en' => 'Pizza'
            ],
            'sandwiches' => [
                'order' => 4,
                'it' => 'Panini',
                'en' => 'Sandwiches'
            ],


            'soups' => [
                'order' => 5,
                'it' => 'Zuppe',
                'en' => 'Soups'
            ],
            'side-dishes' => [
                'order' => 6,
                'it' => 'Contorni',
                'en' => 'Side dishes'
            ],
            'uramaki' => [
                'order' => 7,
                'it' => 'Uramaki',
                'en' => 'Uramaki'
            ],
            'hosomaki' => [
                'order' => 8,
                'it' => 'Hosomaki',
                'en' => 'Hosomaki'
            ],
            'futomaki' => [
                'order' => 9,
                'it' => 'Futomaki',
                'en' => 'Futomaki'
            ],
            'temaki' => [
                'order' => 10,
                'it' => 'Temaki',
                'en' => 'Temaki'
            ],
            'sashimi' => [
                'order' => 11,
                'it' => 'Sashimi',
                'en' => 'Sashimi'
            ],
            'nigiri' => [
                'order' => 12,
                'it' => 'Nigiri',
                'en' => 'Nigiri'
            ],
            'gunkan' => [
                'order' => 13,
                'it' => 'Gunkan',
                'en' => 'Gunkan'
            ],
            'onigiri' => [
                'order' => 14,
                'it' => 'Onigiri',
                'en' => 'Onigiri'
            ],
            'tempura' => [
                'order' => 15,
                'it' => 'Tempura',
                'en' => 'Tempura'
            ],
            'desserts' => [
                'order' => 16,
                'it' => 'Dolci',
                'en' => 'Desserts'
            ],
            'beverage' => [
                'order' => 17,
                'it' => 'Bevande',
                'en' => 'Beverage'
            ],
            'beer' => [
                'order' => 18,
                'it' => 'Birra',
                'en' => 'Beer'
            ],
            'wine' => [
                'order' => 19,
                'it' => 'Vini',
                'en' => 'Wine'
            ],
            'cocktail' => [
                'order' => 20,
                'it' => 'Cocktail',
                'en' => 'Cocktail'
            ],
            'bitter' => [
                'order' => 21,
                'it' => 'Amaro',
                'en' => 'Bitter'
            ]

        */
        ];

        return $cateory;
    }
}