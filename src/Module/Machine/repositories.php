<?php
declare(strict_types=1);

use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\InputFilter\Input;
use App\Module\Machine\Entity\MachineEntity;
use App\Module\Machine\Storage\MachineStorage;
use App\Module\Machine\Storage\MachineStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\SingleEntityPrototype;
use DI\ContainerBuilder;
use App\InputFilter\InputFilter as AppInputFilter;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Validator\NotEmpty;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MachineStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['machine'];

            $hydrator = $c->get('StorageMachineEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
           // $resultSet->setHydrator($hydrator);
           // $resultSet->setEntityPrototype($c->get('MachineEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            //$resultSetPaginator->setHydrator($hydrator);
            //$resultSetPaginator->setEntityPrototype($c->get('MonitorEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MachineStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('MachineEntityPrototype'));

            return $storage;
        }
    ])->addDefinitions([
        'MachineEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new MachineEntity());
        }
    ])
    ->addDefinitions([
        'RestMachineEntityHydrator' => function(ContainerInterface $c) {
        
            $hydrator = new ObjectPropertyHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

           
           
            return $hydrator;
        }
    ])->addDefinitions([
        'StorageMachineEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ObjectPropertyHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());

            $hydrator->addStrategy('date', new MongoDateStrategy());
            return $hydrator;
        }
    ])
    ->addDefinitions([
        'MachinePostValidator' => function(ContainerInterface $c) {

       
            $inputFilter = new AppInputFilter();

            $id = new Input('id');
            $id->getValidatorChain()->attach(new NotEmpty());

            $inputFilter->add($id);

            return $inputFilter;
        }
    ])
    ;
};
