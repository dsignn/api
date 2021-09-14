<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Console;

use App\Storage\StorageInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMenuAllergensCommand extends SymfonyCommand {
    /**
     * @var string
     */
    protected static $defaultName = 'restaurant:create-allergens';

    /**
     * CreateClientCommand constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage) {

        $this->storage = $storage;
        parent::__construct();
        $this->setDescription('Create/update the list of menu allergens');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        try {
            $this->storage->delete('allergens');
            $this->storage->getStorageAdapter()->update($this->getAllergens());
            $output->writeln('Menu allergens updated');
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return 1;
        }
    }

    /**
     * @return array
     */
    protected function getAllergens() {

        $allergens = [
            "_id" => "allergens",
            "allergens" => [
                [
                    "name" => "cereals",
                    'order' => 0,
                    "translation" => [
                        'it' => 'Cereali e derivati',
                        'en' => 'Cereals and derivatives'
                    ]
                ],
                [
                    "name" => "crustaceans",
                    'order' => 1,
                    "translation" => [
                        'it' => 'Crostacei',
                        'en' => 'Crustaceans'
                    ]
                ],
                [
                    "name" => "egg",
                    'order' => 2,
                    "translation" => [
                        'it' => 'Uova',
                        'en' => 'Egg'
                    ]
                ],
                [
                    "name" => "fish",
                    'order' => 3,
                    "translation" => [
                        'it' => 'Pesce',
                        'en' => 'Fish'
                    ]
                ],
                [
                    "name" => "peanuts",
                    'order' => 4,
                    "translation" => [
                        'it' => 'Arachidi',
                        'en' => 'Peanuts'
                    ]
                ],
                [
                    "name" => "soy",
                    'order' => 5,
                    "translation" => [
                        'it' => 'Soia',
                        'en' => 'Soy'
                    ]
                ],
                [
                    "name" => "nuts",
                    'order' => 6,
                    "translation" => [
                        'it' => 'Frutta a guscio',
                        'en' => 'Nuts'
                    ]
                ],
                [
                    "name" => "milk",
                    'order' => 7,
                    "translation" => [
                        'it' => 'Latte',
                        'en' => 'Milk'
                    ]
                ],
                [
                    "name" => "celery",
                    'order' => 8,
                    "translation" => [
                        'it' => 'Sedano',
                        'en' => 'Celery'
                    ]
                ],
                [
                    "name" => "mustard",
                    'order' => 9,
                    "translation" => [
                        'it' => 'Senape',
                        'en' => 'Mustard'
                    ]
                ],
                [
                    "name" => "sesame",
                    'order' => 10,
                    "translation" => [
                        'it' => 'Sesamo',
                        'en' => 'Sesame'
                    ]
                ],
                [
                    "name" => "sulfur-dioxide",
                    'order' => 11,
                    "translation" => [
                        'it' => 'Anidride solforosa e solfiti',
                        'en' => 'Sulfur dioxide and sulphites'
                    ]
                ],
                [
                    "name" => "lupins",
                    'order' => 12,
                    "translation" => [
                        'it' => 'Lupini',
                        'en' => 'Lupins'
                    ]
                ],
                [
                    "name" => "clams",
                    'order' => 13,
                    "translation" => [
                        'it' => 'Molluschi',
                        'en' => 'Clams'
                    ]
                ]
            ]
        ];

        return $allergens;
    }
}