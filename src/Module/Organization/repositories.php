<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Monitor\Entity\MonitorReference;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Event\SluggerNameEvent;
use App\Module\Organization\Storage\adapter\Mongo\OrganizationMongoAdapter;
use App\Module\Organization\Storage\OrganizationStorage;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Organization\Url\GenericSlugify;
use App\Module\Organization\Url\SlugifyInterface;
use App\Module\Organization\Validator\UniqueNameOrganization;
use App\Module\User\Event\UserPasswordEvent;
use App\Module\User\Validator\EmailExistValidator;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use DI\ContainerBuilder;
use Laminas\Filter\StringToLower;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use MongoDB\Client;
use Psr\Container\ContainerInterface;


return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        OrganizationStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['organization'];

            $hydrator = $c->get('StorageOrganizationEntityHydrator')
;
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('OrganizationEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('OrganizationEntityPrototype'));

            $mongoAdapter = new OrganizationMongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new OrganizationStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('OrganizationEntityPrototype'));

            $storage->getEventManager()->attach(Storage::$BEFORE_SAVE, new SluggerNameEvent($c->get(SlugifyInterface::class)));
            $storage->getEventManager()->attach(Storage::$BEFORE_UPDATE, new SluggerNameEvent($c->get(SlugifyInterface::class)));
            return $storage;
        }
    ])->addDefinitions([
        'RestOrganizationEntityHydrator' => function(ContainerInterface $c) {


            $referenceHydrator = new ClassMethodsHydrator();
            $referenceHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $referenceHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('qrCode', new HydratorStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('logo', new HydratorStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            return $hydrator;
        }
    ])->addDefinitions([
        'StorageOrganizationEntityHydrator' => function(ContainerInterface $c) {

            $referenceHydrator = new ClassMethodsHydrator();
            $referenceHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $referenceHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('qrCode', new HydratorStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('logo', new HydratorStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            return $hydrator;
        }
    ])->addDefinitions([
        'PostOrganizationValidator' => function(ContainerInterface $c) {

            $inputFilter = new InputFilter();

            // Name field
            $name = new Input('name');

            $name->getFilterChain()
                ->attach(new StringToLower());

            $name->getValidatorChain()
                ->attach($c->get(UniqueNameOrganization::class));

            $inputFilter->add($name);

            return $inputFilter;
        }
    ])->addDefinitions([
        'PutOrganizationValidator' => function(ContainerInterface $c) {

            $inputFilter = new InputFilter();

            // Name field
            $name = new Input('name');

            $name->getFilterChain()
                ->attach(new StringToLower());

            $name->getValidatorChain()
                ->attach($c->get(UniqueNameOrganization::class)->setFindIdInRequest(true));

            $inputFilter->add($name);

            $qrCode = new Input('qrCode');
            $qrCode->setRequired(false);
            $inputFilter->add($qrCode);


            $whatsappPhone = new Input('whatsappPhone');
            $whatsappPhone->setRequired(false);

            $inputFilter->add($whatsappPhone);

            return $inputFilter;
        }
    ])->addDefinitions([
        'OrganizationEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new OrganizationEntity());
        }
    ])->addDefinitions([
        UniqueNameOrganization::class => function(ContainerInterface $c) {
            return new UniqueNameOrganization($c->get(OrganizationStorageInterface::class), $c);
        }
    ])->addDefinitions([
        SlugifyInterface::class => function(ContainerInterface $c) {
            return new GenericSlugify();
        }
    ]);
};
