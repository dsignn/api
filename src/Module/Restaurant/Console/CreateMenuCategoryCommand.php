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
        $this->setDescription('Create/Update the list of menu category');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        try {
            $this->storage->delete('category');
            $this->storage->getStorageAdapter()->update($this->getCategory());
            $output->writeln('Menu category updated');
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return 1;
        }
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
                    "name" => "nigiri",
                    'order' => 1,
                    "translation" => [
                        'it' => 'Nighiri',
                        'en' => 'Nighiri'
                    ]
                ],
                [
                    "name" => "gunkan",
                    'order' => 2,
                    "translation" => [
                        'it' => 'Gunkan',
                        'en' => 'Gunkan'
                    ]
                ],
                [
                    "name" => "hosomaki",
                    'order' => 3,
                    "translation" => [
                        'it' => 'Hossomaki',
                        'en' => 'Hossomaki'
                    ]
                ],
                [
                    "name" => "futomaki",
                    'order' => 4,
                    "translation" => [
                        'it' => 'Futomaki',
                        'en' => 'Futomaki'
                    ]
                ],
                [
                    "name" => "uramaki",
                    'order' => 5,
                    "translation" => [
                        'it' => 'Uramaki',
                        'en' => 'Uramaki'
                    ]
                ],
                [
                    "name" => "sashimi",
                    'order' => 6,
                    "translation" => [
                        'it' => 'Sashimi',
                        'en' => 'Sashimi'
                    ]
                ],
                [
                    "name" => "raw-fish",
                    'order' => 7,
                    "translation" => [
                        'it' => 'Crudo di pesce',
                        'en' => 'Raw fish'
                    ]
                ],
                [
                    "name" => "plateau",
                    'order' => 8,
                    "translation" => [
                        'it' => 'Plateau',
                        'en' => 'Plateau'
                    ]
                ],
                [
                    "name" => "temaki",
                    'order' => 9,
                    "translation" => [
                        'it' => 'Temaki',
                        'en' => 'Temaki'
                    ]
                ],
                [
                    "name" => "onigiri",
                    'order' => 10,
                    "translation" => [
                        'it' => 'Onighiri',
                        'en' => 'Onighiri'
                    ]
                ],
                [
                    "name" => "tempura",
                    'order' => 11,
                    "translation" => [
                        'it' => 'Tempura',
                        'en' => 'Tempura'
                    ]
                ],
                [
                    "name" => "first-courses",
                    'order' => 12,
                    "translation" => [
                        'it' => 'Primi piatti',
                        'en' => 'First courses'
                    ]
                ],
                [
                    "name" => "main-courses",
                    'order' => 13,
                    "translation" => [
                        'it' => 'Secondi piatti',
                        'en' => 'Main courses'
                    ]
                ],
                [
                    "name" => "single-dish",
                    'order' => 14,
                    "translation" => [
                        'it' => 'Piatto unico',
                        'en' => 'Single dish'
                    ]
                ],
                [
                    "name" => "pizza",
                    'order' => 15,
                    "translation" => [
                        'it' => 'Pizza',
                        'en' => 'Pizza'
                    ]
                ],
                [
                    "name" => "sandwiches",
                    'order' => 16,
                    "translation" => [
                        'it' => 'Panini',
                        'en' => 'Sandwiches'
                    ]
                ],
                [
                    "name" => "hamburger",
                    'order' => 17,
                    "translation" => [
                        'it' => 'Hamburger',
                        'en' => 'Hamburger'
                    ]
                ],
                [
                    "name" => "soups",
                    'order' => 18,
                    "translation" => [
                        'it' => 'Zuppe',
                        'en' => 'Soups'
                    ]
                ],
                [
                    "name" => "side-dishes",
                    'order' => 19,
                    "translation" => [
                        'it' => 'Contorni',
                        'en' => 'Side dishes'
                    ]
                ],
               
                [
                    "name" => "desserts",
                    'order' => 20,
                    "translation" => [
                        'it' => 'Dolci',
                        'en' => 'Desserts'
                    ]
                ],
                [
                    "name" => "beverage",
                    'order' => 21,
                    "translation" => [
                        'it' => 'Bevande',
                        'en' => 'Beverage'
                    ]
                ],
                [
                    "name" => "beer",
                    'order' => 22,
                    "translation" => [
                        'it' => 'Birra',
                        'en' => 'Beer'
                    ]
                ],
                [
                    "name" => "wine",
                    'order' => 23,
                    "translation" => [
                        'it' => 'Vini',
                        'en' => 'Wine'
                    ]
                ],
                [
                    "name" => "bubbles",
                    'order' => 24,
                    "translation" => [
                        'it' => 'Bollicine',
                        'en' => 'Bubbles'
                    ]
                ],
                [
                    "name" => "cocktail",
                    'order' => 25,
                    "translation" => [
                        'it' => 'Cocktail',
                        'en' => 'Cocktail'
                    ]
                ],
                [
                    "name" => "bitter",
                    'order' => 26,
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