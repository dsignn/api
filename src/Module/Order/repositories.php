<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Filter\DefaultFilter;
use App\Hydrator\MapHydrator;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Order\Entity\OrderEntity;
use App\Module\Order\Storage\OrderStorage;
use App\Module\Order\Storage\OrderStorageInterface;
use App\Module\Organization\Entity\Embedded\Phone\Phone;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Event\SluggerNameEvent;
use App\Module\Organization\Storage\adapter\Mongo\OrganizationMongoAdapter;
use App\Module\Organization\Storage\OrganizationStorage;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Organization\Url\GenericSlugify;
use App\Module\Organization\Url\SlugifyInterface;
use App\Module\Organization\Validator\HasOrganization;
use App\Module\Organization\Validator\UniqueNameOrganization;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Event\AttachDateTimeCallback;
use App\Storage\Storage;
use App\Validator\Mongo\ObjectIdValidator;
use DI\ContainerBuilder;
use Laminas\Filter\Callback;
use Laminas\Filter\StringToLower;
use Laminas\Filter\ToInt;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\Input;
use App\InputFilter\InputFilter;
use App\Module\Order\Entity\Embedded\CarOrder;
use App\Module\Order\Entity\Embedded\MenuOrder;
use App\Module\Order\Storage\Adapter\Mongo\OrderMongoAdapter;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Storage\Entity\Embedded\Price\Price;
use App\Storage\Entity\MultiEntityPrototype;
use Laminas\Validator\Digits;
use Laminas\Validator\InArray;
use MongoDB\Client;
use Psr\Container\ContainerInterface;


return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        OrderStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['order'];

            $hydrator = $c->get('StorageOrderEntityHydrator');
;
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('OrderEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('OrderEntityPrototype'));

            $mongoAdapter = new OrderMongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new OrderStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('OrderEntityPrototype'));

            $storage->getEventManager()->attach(
                Storage::$BEFORE_SAVE,
                new AttachDateTimeCallback('createdAt')
            );

            $storage->getEventManager()->attach(
                Storage::$BEFORE_UPDATE,
                new AttachDateTimeCallback('lastUpdateAt')
            );

            return $storage;
        }
    ])->addDefinitions([
        'OrderItemEntityPrototype' => function(ContainerInterface $c) {

            $multiEntityPrototype = new MultiEntityPrototype('type');
            $multiEntityPrototype->addEntityPrototype(
                MenuOrder::TYPE_MENU,
                new MenuOrder()
            )->addEntityPrototype(
                CarOrder::TYPE_MENU,
                new CarOrder()
            );

            return $multiEntityPrototype;
        }
    ])->addDefinitions([
        'RestOrderEntityHydrator' => function(ContainerInterface $c) {


            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('createdAt', $c->get('EntityDateRestStrategy'));
            $hydrator->addStrategy('lastUpdateAt', $c->get('EntityDateRestStrategy'));
            $hydrator->addStrategy('organization', new HydratorStrategy($c->get('ReferenceRestHydrator'), new SingleEntityPrototype(new Reference())));
         
            $objectHydrator = new ObjectPropertyHydrator();

            $orderedItemHydrator = new MapHydrator();
            $orderedItemHydrator->setTypeField('type');
            $orderedItemHydrator->setEntityPrototype(
                $c->get('OrderItemEntityPrototype')
            );

            $orderMenuHydrator = new ClassMethodsHydrator();
            $orderMenuHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $orderMenuHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $orderMenuHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $orderMenuHydrator->addStrategy('price', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Price())));

            $orderCarHydrator = new ClassMethodsHydrator();
            $orderCarHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $orderCarHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $orderCarHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $orderCarHydrator->addStrategy('price', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Price())));

            $orderedItemHydrator->addHydrator(MenuOrder::TYPE_MENU, $orderMenuHydrator)
                ->addHydrator(CarOrder::TYPE_MENU, $orderCarHydrator);

            $objectHydrator->addStrategy('orderedItem', new HydratorStrategy($orderedItemHydrator,  $c->get('OrderItemEntityPrototype')));
            
            $hydrator->addStrategy('items', new HydratorArrayStrategy($objectHydrator, new SingleEntityPrototype(new stdClass())));
            
         
            
            return $hydrator;
        }
    ])->addDefinitions([
        'StorageOrderEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('createdAt', new MongoDateStrategy());
            $hydrator->addStrategy('lastUpdateAt', new MongoDateStrategy());
            $hydrator->addStrategy('organization', new HydratorStrategy($c->get('ReferenceMongoHydrator'), new SingleEntityPrototype(new Reference())));

            $objectHydrator = new ObjectPropertyHydrator();

            $orderedItemHydrator = new MapHydrator();
            $orderedItemHydrator->setTypeField('type');
            $orderedItemHydrator->setEntityPrototype(
                $c->get('OrderItemEntityPrototype')
            );

            $orderMenuHydrator = new ClassMethodsHydrator();
            $orderMenuHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $orderMenuHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $orderMenuHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $orderMenuHydrator->addStrategy('price', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Price())));

            $orderCarHydrator = new ClassMethodsHydrator();
            $orderCarHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $orderCarHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $orderCarHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $orderCarHydrator->addStrategy('price', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Price())));

            $orderedItemHydrator->addHydrator(MenuOrder::TYPE_MENU, $orderMenuHydrator)
                ->addHydrator(CarOrder::TYPE_MENU, $orderCarHydrator);

            $objectHydrator->addStrategy('orderedItem', new HydratorStrategy($orderedItemHydrator,  $c->get('OrderItemEntityPrototype')));
            
            $hydrator->addStrategy('items', new HydratorArrayStrategy($objectHydrator, new SingleEntityPrototype(new stdClass())));
            
            return $hydrator;
        }
    ])->addDefinitions([
        'OrderEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new OrderEntity());
        }
    ])->addDefinitions([
        'OrderValidation' => function(ContainerInterface $c) {
          
            $organization = new InputFilter();
            
            $input = new Input('id');
            $input->getValidatorChain()->attach(new ObjectIdValidator(), true)
                ->attach($c->get(HasOrganization::class));
         
            $organization->add($input, 'id');

            $input = new Input('collection');
            $input->setRequired(false);
            $filter = new Callback([], ['collection' => 'organization']);
            $filter->setCallback(function ($value) {

                if (!$value) {
                    $value = 'organization';
                }
                return $value;
            });

            $input->getFilterChain()->attach($filter);
            $organization->add($input, 'collection');

            $inputFilter = new InputFilter();
            $inputFilter->add($organization, 'organization');

            $input = new Input('status');
            $input->getValidatorChain()->attach(new InArray([
                'haystack' => [
                    OrderEntity::STATUS_CHECK,
                    OrderEntity::STATUS_QUEUE,
                    OrderEntity::STATUS_PREPARATION,
                    OrderEntity::STATUS_DELIVERING,
                    OrderEntity::STATUS_DELIVERED,
                    OrderEntity::STATUS_INVALID,
                ]
            ]));
            $inputFilter->add($input);


            $priceInputFilter = new InputFilter();

            $input = new Input('value');
            $priceInputFilter->add($input, 'value');

            $orderItemInputFilter = new InputFilter();
            $orderItemInputFilter->add($priceInputFilter, 'price');

            $input = new Input('type');
            $orderItemInputFilter->add($input, 'type');

            $orderWrapperInputFilter = new InputFilter();
            $input = new Input('quantity');
          
            $orderWrapperInputFilter->add($input, 'quantity');
            $orderWrapperInputFilter->add( $orderItemInputFilter, 'orderedItem');
           
            $collectionItem = new CollectionInputFilter();
            $collectionItem->setInputFilter($orderWrapperInputFilter);

            $inputFilter->add($collectionItem, 'items');
            
            return $inputFilter;
        }
    ]);
};

