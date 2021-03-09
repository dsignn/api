<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Filter\ToStringFilter;
use App\Hydrator\Filter\PropertyFilter;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Restaurant\Entity\CategoryEntity;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\Embedded\Price\Price;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Module\Restaurant\Event\DisableMenu;
use App\Module\Restaurant\Storage\Adapeter\Mongo\MenuMongoAdapter;
use App\Module\Restaurant\Storage\MenuCategoryStorage;
use App\Module\Restaurant\Storage\MenuCategoryStorageInterface;
use App\Module\Restaurant\Storage\MenuStorage;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use App\Module\User\Mail\UserMailerInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use App\Validator\Mongo\ObjectIdValidator;
use DI\ContainerBuilder;
use Laminas\Filter\Callback;
use Laminas\Filter\ToInt;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\NotEmpty;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MenuCategoryStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['menu-category'];

            $hydrator = $c->get('StorageMenuCategoryEntityHydrator');
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('MenuCategoryEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('MenuCategoryEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MenuCategoryStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('MenuCategoryEntityPrototype'));

            return $storage;

        }
    ])->addDefinitions([
        MenuStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['menu'];

            $hydrator = $c->get('StorageMenuEntityHydrator')
;
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('MenuEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('MenuEntityPrototype'));

            $mongoAdapter = new MenuMongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MenuStorage($mongoAdapter);
            $storage->setHydrator($hydrator);

            $storage->getEventManager()->attach(
                Storage::$AFTER_SAVE,
                new DisableMenu($storage)
            );

            $storage->getEventManager()->attach(
                Storage::$AFTER_UPDATE,
                new DisableMenu($storage)
            );

            $storage->setEntityPrototype($c->get('MenuEntityPrototype'));

            return $storage;
        }
    ])->addDefinitions([
        'RpcMenuCategoryEntityHydrator' => function(ContainerInterface $c) {
            $hydrator = new ObjectPropertyHydrator();
            $hydrator->addFilter('_id', new PropertyFilter('_id'),  FilterComposite::CONDITION_AND);
            return $hydrator;
        }
    ])->addDefinitions([
        'StorageMenuCategoryEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ObjectPropertyHydrator();
            return $hydrator;
        }
    ])->addDefinitions([
        'StorageMenuEntityHydrator' => function(ContainerInterface $c) {

            $menuItemHydrator = new ClassMethodsHydrator();
            $menuItemHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $menuItemHydrator->addStrategy('_id', new MongoIdStrategy(true));
            $menuItemHydrator->addStrategy('id', new MongoIdStrategy(true));
            $menuItemHydrator->addStrategy('price', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Price())));
            $menuItemHydrator->addStrategy('photos', new HydratorArrayStrategy($c->get('ReferenceMongoHydrator'), new SingleEntityPrototype(new Reference())));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('organization', new HydratorStrategy($menuItemHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('items', new HydratorArrayStrategy($menuItemHydrator, new SingleEntityPrototype(new MenuItem())));

            return $hydrator;
        }
    ])->addDefinitions([
        'RestMenuEntityHydrator' => function(ContainerInterface $c) {

            $menuItemHydrator = new ClassMethodsHydrator();
            $menuItemHydrator->setNamingStrategy(new CamelCaseStrategy());
            $menuItemHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $menuItemHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $menuItemHydrator->addStrategy('price', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Price())));
            $menuItemHydrator->addStrategy('photos', new HydratorArrayStrategy($c->get('ReferenceRestHydrator'), new SingleEntityPrototype(new Reference())));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('organization', new HydratorStrategy($c->get('ReferenceRestHydrator'), new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('items', new HydratorArrayStrategy($menuItemHydrator, new SingleEntityPrototype(new MenuItem())));

            return $hydrator;
        }
    ])->addDefinitions([
        'RestMenuEntityWithResourceHydrator' => function(ContainerInterface $c) {

            $menuItemHydrator = new ClassMethodsHydrator();
            $menuItemHydrator->setNamingStrategy(new CamelCaseStrategy());
            $menuItemHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $menuItemHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $menuItemHydrator->addStrategy('price', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Price())));
            $menuItemHydrator->addStrategy('photos', new HydratorArrayStrategy($c->get('RestResourceEntityHydrator'), $c->get('ResourceEntityPrototype')));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('organization', new HydratorStrategy($c->get('ReferenceRestHydrator'), new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('items', new HydratorArrayStrategy($menuItemHydrator, new SingleEntityPrototype(new MenuItem())));

            return $hydrator;
        }
    ])->addDefinitions([
        'MenuEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new MenuEntity());
        }
    ])->addDefinitions([
        'MenuCategoryEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new CategoryEntity());
        }
    ])->addDefinitions([
        'MenuValidation' => function(ContainerInterface $container) {

            $organization = new InputFilter();
            $input = new Input('id');
            $organization->add($input, 'id');
            $input = new Input('collection');
            $input->setRequired(false);
            $organization->add($input, 'collection');

            $filter = new Callback([], ['collection' => 'organization']);
            $filter->setCallback(function ($value) {

                if (!$value) {
                    $value = 'organization';
                }
                return $value;
            });

            $input->getFilterChain()->attach($filter);

            $price = new InputFilter();
            $input = new Input('value');
            $input->setRequired(false);
            $input->getFilterChain()->attach(new ToInt());
            $price->add($input, 'value');

            $menuItem = new InputFilter();
            $menuItem->add($price, 'price');

            $input = new Input('id');
            $input->setRequired(false);
            $menuItem->add($input, 'id');

            $input = new Input('name');
            $menuItem->add($input, 'name');

            $input = new Input('description');
            $input->setRequired(false);
            $menuItem->add($input, 'description');

            $input = new Input('category');
            $menuItem->add($input, 'category');

            $input = new Input('status');
            $menuItem->add($input, 'status');

            $input = new Input('photos');
            $input->setRequired(false);
            $menuItem->add($input, 'photos');

            $collectionItem = new CollectionInputFilter();
            $collectionItem->setInputFilter($menuItem);

            $inputFilter = new InputFilter();
            $inputFilter->add($organization, 'organization');
            $inputFilter->add($collectionItem, 'items');

            $input = new Input('name');
            $inputFilter->add($input, 'name');

            $input = new Input('layoutType');
            $inputFilter->add($input, 'layoutType');

            $input = new Input('backgroundHeader');
            $inputFilter->add($input, 'backgroundHeader');

            $input = new Input('colorHeader');
            $inputFilter->add($input, 'colorHeader');

            $input = new Input('enable');
            $inputFilter->add($input, 'enable');

            $validator = new NotEmpty([
                NotEmpty::INTEGER,
                NotEmpty::FLOAT,
                NotEmpty::STRING,
                NotEmpty::ZERO,
                NotEmpty::EMPTY_ARRAY,
                NotEmpty::SPACE,
                NotEmpty::OBJECT,
                NotEmpty::OBJECT_STRING,
                NotEmpty::OBJECT_COUNT
            ]);

            $input->getValidatorChain()->attach($validator);

            $input = new Input('note');
            $input->setRequired(false);
            $input->getFilterChain()->attach(new ToStringFilter());
            $inputFilter->add($input, 'note');

            return $inputFilter;
        }
    ])->addDefinitions([
        'ResourceMenuItemValidation' => function(ContainerInterface $container) {

            $inputFilter = new InputFilter();

            $input = new Input('menu_id');
            $input->getValidatorChain()->attach(new ObjectIdValidator());
            $inputFilter->add($input, 'menu_id');

            $input = new Input('resource_menu_id');
            $input->getValidatorChain()->attach(new ObjectIdValidator());
            $inputFilter->add($input, 'resource_menu_id');

            $input = new Input('file');
            $inputFilter->add($input, 'file');

            return $inputFilter;
        }
    ])->addDefinitions([
        'ResourceMenuItemDeleteValidation' => function(ContainerInterface $container) {

            $inputFilter = new InputFilter();

            $input = new Input('menu_id');
            $input->getValidatorChain()->attach(new ObjectIdValidator());
            $inputFilter->add($input, 'menu_id');

            $input = new Input('resource_menu_id');
            $input->getValidatorChain()->attach(new ObjectIdValidator());
            $inputFilter->add($input, 'resource_menu_id');

            return $inputFilter;
        }
    ]);
};
