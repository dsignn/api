<?php
declare(strict_types=1);

use App\Filter\DefaultFilter;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\InputFilter\Input;
use App\Module\Monitor\Entity\MonitorContainerEntity;
use App\Module\Monitor\Entity\MonitorEntity;
use App\Module\Monitor\Storage\MonitorStorage;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\SingleEntityPrototype;
use DI\ContainerBuilder;
use Laminas\Hydrator\ClassMethodsHydrator;
use MongoDB\Client;
use Psr\Container\ContainerInterface;
use App\InputFilter\InputFilter as AppInputFilter;
use App\Module\Monitor\Http\QueryString\MonitorQueryString;
use App\Storage\Entity\Reference;
use App\Validator\Mongo\ObjectIdValidator;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\NotEmpty;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MonitorStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['monitor'];

            $hydrator = $c->get('StorageMonitorEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('MonitorEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('MonitorEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MonitorStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('MonitorEntityPrototype'));

            return $storage;
        }
    ])->addDefinitions([
        'MonitorEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new MonitorContainerEntity());
        }
    ])->addDefinitions([
        'RestMonitorEntityHydrator' => function(ContainerInterface $c) {

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $monitorHydrator = new ClassMethodsHydrator();
            $monitorHydrator->setNamingStrategy(new CamelCaseStrategy());
            $monitorHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));
            $hydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));

            return $hydrator;
        }
    ])->addDefinitions([
        'StorageMonitorEntityHydrator' => function(ContainerInterface $c) {

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $monitorHydrator = new ClassMethodsHydrator();
            $monitorHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $monitorHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            return $hydrator;
        }
    ])->addDefinitions([
        'MonitorPostValidation' => function(ContainerInterface $c) {
            $inputFilter = new AppInputFilter();

            $name = new Input('name');

            $monitors = new Input('monitors');

            $description = new Input('description');

            $organizationReference = new InputFilter();

            $id = new Input('id');
            $id->getValidatorChain()
                ->attach(new NotEmpty())
                ->attach(new ObjectIdValidator());
            
            $organizationReference->add($id);

            $collection = new Input('collection');
            $collection->getFilterChain()
                ->attach(new DefaultFilter('collection'));

            $organizationReference->add($collection);

            $inputFilter
                ->add($name)
                ->add($monitors)
                ->add($description)
                ->add($organizationReference, 'organizationReference')
                ;

            return $inputFilter;
        }
    ])->addDefinitions([
        MonitorQueryString::class => function(ContainerInterface $c) {
            return new MonitorQueryString();
        }
    ]);
};
