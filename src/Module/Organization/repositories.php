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
use Laminas\Validator\EmailAddress;
use Psr\Container\ContainerInterface;
use function DI\get;

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

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $settings['storage']['name'], $serviceSetting['collection']);
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

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));
            $hydrator->addStrategy('qrCode', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Reference())));
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
            $inputFilter->add($qrCode);

            return $inputFilter;
        }
    ])->addDefinitions([
        'StorageOrganizationEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addStrategy('qrCode', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Reference())));

            return $hydrator;
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
