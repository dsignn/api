<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Filter\DefaultFilter;
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
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
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

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
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
                new AttachDateTimeCallback('lastpdateAt')
            );

            return $storage;
        }
    ])->addDefinitions([
        'RestOrderEntityHydrator' => function(ContainerInterface $c) {


            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('createdAt', $c->get('EntityDateRestStrategy'));
            $hydrator->addStrategy('lastpdateAt', $c->get('EntityDateRestStrategy'));
            $hydrator->addStrategy('organization', new HydratorStrategy($c->get('ReferenceRestHydrator'), new SingleEntityPrototype(new Reference())));

            return $hydrator;
        }
    ])->addDefinitions([
        'StorageOrderEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('createdAt', new MongoDateStrategy());
            $hydrator->addStrategy('lastpdateAt', new MongoDateStrategy());
            $hydrator->addStrategy('organization', new HydratorStrategy($c->get('ReferenceRestHydrator'), new SingleEntityPrototype(new Reference())));

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

            
            return $inputFilter;
        }
    ]);
};

