<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Organization\Entity\Embedded\Address\Address;
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
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use DI\ContainerBuilder;
use Laminas\Filter\StringToLower;
use Laminas\Filter\ToInt;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\Input;
use App\InputFilter\InputFilter;
use Laminas\Validator\Digits;
use Laminas\Validator\InArray;
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
            $input = new Input('name');

            $input->getFilterChain()
                ->attach(new StringToLower());

            $input->getValidatorChain()
                ->attach($c->get(UniqueNameOrganization::class)->setFindIdInRequest(true));

            $inputFilter->add($input);

            $input = new Input('logo');
            $input->setRequired(false);
            $inputFilter->add($input);

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
        HasOrganization::class => function(ContainerInterface $c) {
            return new HasOrganization($c->get(OrganizationStorageInterface::class), $c);
        }
    ])->addDefinitions([
        SlugifyInterface::class => function(ContainerInterface $c) {
            return new GenericSlugify();
        }
    ])->addDefinitions([
        'OrganizationReferenceStorageHydrator' => function(ContainerInterface $c) {
            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            return $organizationHydrator;
        }
    ])->addDefinitions([
        'OrganizationReferenceRestHydrator' => function(ContainerInterface $c) {
            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            return $organizationHydrator;
        }
    ]);
};

/**
 * TODO Add in validator
 *
 * @return array
 */
function getPrefix() {
    return   [
        "+44",
        "+1",
        "+213",
        "+376",
        "+244",
        "+1264",
        "+1268",
        "+54",
        "+374",
        "+297",
        "+61",
        "+43",
        "+994",
        "+1242",
        "+973",
        "+880",
        "+1246",
        "+375",
        "+32",
        "+501",
        "+229",
        "+1441",
        "+975",
        "+591",
        "+387",
        "+267",
        "+55",
        "+673",
        "+359",
        "+226",
        "+257",
        "+855",
        "+237",
        "+238",
        "+1345",
        "+236",
        "+56",
        "+86",
        "+57",
        "+269",
        "+242",
        "+682",
        "+506",
        "+385",
        "+53",
        "+90392",
        "+357",
        "+42",
        "+45",
        "+253",
        "+1809",
        "+593",
        "+20",
        "+503",
        "+240",
        "+291",
        "+372",
        "+251",
        "+500",
        "+298",
        "+679",
        "+358",
        "+33",
        "+594",
        "+689",
        "+241",
        "+220",
        "+7880",
        "+49",
        "+233",
        "+350",
        "+30",
        "+299",
        "+1473",
        "+590",
        "+671",
        "+502",
        "+224",
        "+245",
        "+592",
        "+509",
        "+504",
        "+852",
        "+36",
        "+354",
        "+91",
        "+62",
        "+98",
        "+964",
        "+353",
        "+972",
        "+39",
        "+1876",
        "+81",
        "+962",
        "+7",
        "+254",
        "+686",
        "+850",
        "+82",
        "+965",
        "+996",
        "+856",
        "+371",
        "+961",
        "+266",
        "+231",
        "+218",
        "+417",
        "+370",
        "+352",
        "+853",
        "+389",
        "+261",
        "+265",
        "+60",
        "+960",
        "+223",
        "+356",
        "+692",
        "+596",
        "+222",
        "+52",
        "+691",
        "+373",
        "+377",
        "+976",
        "+1664",
        "+212",
        "+258",
        "+95",
        "+264",
        "+674",
        "+977",
        "+31",
        "+687",
        "+64",
        "+505",
        "+227",
        "+234",
        "+683",
        "+672",
        "+670",
        "+47",
        "+968",
        "+680",
        "+507",
        "+675",
        "+595",
        "+51",
        "+63",
        "+48",
        "+351",
        "+1787",
        "+974",
        "+262",
        "+40",
        "+250",
        "+378",
        "+239",
        "+966",
        "+221",
        "+381",
        "+248",
        "+232",
        "+65",
        "+421",
        "+386",
        "+677",
        "+252",
        "+27",
        "+34",
        "+94",
        "+290",
        "+1869",
        "+1758",
        "+249",
        "+597",
        "+268",
        "+46",
        "+41",
        "+963",
        "+886",
        "+66",
        "+228",
        "+676",
        "+1868",
        "+216",
        "+90",
        "+993",
        "+1649",
        "+688",
        "+256",
        "+380",
        "+971",
        "+598",
        "+678",
        "+379",
        "+58",
        "+84",
        "+681",
        "+969",
        "+967",
        "+260",
        "+263",
    ];
}
