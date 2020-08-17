<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Hydrator\MapHydrator;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\UnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Organization\Validator\UniqueNameOrganization;
use App\Module\Resource\Entity\Embedded\Dimension;
use App\Module\Resource\Entity\ImageResourceEntity;
use App\Module\Resource\Entity\VideoResourceEntity;
use App\Module\Resource\Event\MetadataEvent;
use App\Module\Resource\Event\S3UploaderEvent;
use App\Module\Resource\Filter\FileUnpacking;
use App\Module\Resource\Filter\StringToArray;
use App\Module\Resource\Storage\ResourceStorage;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\MultiEntityPrototype;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use Aws\S3\S3Client;
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
        ResourceStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['resource'];

            $hydrator = $c->get('ResourceStorageHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('ResourceEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('ResourceEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new ResourceStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('ResourceEntityPrototype'));

            $storage->getEventManager()->attach(
                Storage::$BEFORE_SAVE,
                new MetadataEvent($c->get('settings')['ffmpeg']['binary'])
            );

            $storage->getEventManager()->attach(
                Storage::$BEFORE_UPDATE,
                new MetadataEvent($c->get('settings')['ffmpeg']['binary'])
            );

            $storage->getEventManager()->attach(
                Storage::$BEFORE_UPDATE,
                new S3UploaderEvent($c->get('S3Client'), $c->get('settings')['s3Resource']['bucket'])
            );

            $storage->getEventManager()->attach(
                Storage::$BEFORE_SAVE,
                new S3UploaderEvent($c->get('S3Client'), $c->get('settings')['s3Resource']['bucket'])
            );

            return $storage;
        }
    ])->addDefinitions([
        'ResourceEntityPrototype' => function(ContainerInterface $c) {

            $multiEntityPrototype = new MultiEntityPrototype('mimeType');
            $multiEntityPrototype->addEntityPrototype(
                'image/jpeg',
                new ImageResourceEntity()
            )->addEntityPrototype(
                'image/png',
                new ImageResourceEntity()
            )->addEntityPrototype(
                'video/mp4',
                new VideoResourceEntity()
            );

            return $multiEntityPrototype;
        }
    ])->addDefinitions([
        'ResourceStorageHydrator' => function(ContainerInterface $c) {

            $hydrator = new MapHydrator();
            $hydrator->setTypeField('mimeType');
            $hydrator->setEntityPrototype(
                $c->get('ResourceEntityPrototype')
            );

            $imageHydrator = new ClassMethodsHydrator();
            $imageHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $imageHydrator->addStrategy('id', new MongoIdStrategy());

            $videoHydrator = new ClassMethodsHydrator();
            $videoHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $videoHydrator->addStrategy('id', new MongoIdStrategy());

            $strategyDimension = new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Dimension()));

            $imageHydrator->addStrategy('dimension', $strategyDimension);
            $videoHydrator->addStrategy('dimension', $strategyDimension);

            $hydrator->addHydrator(
                'image/jpeg',
                $imageHydrator
            )->addHydrator(
                'image/png',
                $imageHydrator
            )->addHydrator(
                'video/mp4',
                $videoHydrator
            );

            return $hydrator;
        }
    ])->addDefinitions([
        'RestResourceEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new MapHydrator();
            $hydrator->setTypeField('mimeType');
            $hydrator->setEntityPrototype(
                $c->get('ResourceEntityPrototype')
            );

            $imageHydrator = new ClassMethodsHydrator();
            $imageHydrator->setNamingStrategy(new CamelCaseStrategy());
            $imageHydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));

            $videoHydrator = new ClassMethodsHydrator();
            $videoHydrator->setNamingStrategy(new CamelCaseStrategy());
            $videoHydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));

            $strategyDimension = new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Dimension()));
            $imageHydrator->addStrategy('dimension', $strategyDimension);
            $videoHydrator->addStrategy('dimension', $strategyDimension);

            $hydrator->addHydrator(
                'image/jpeg',
                $imageHydrator
            )->addHydrator(
                'image/png',
                $imageHydrator
            )->addHydrator(
                'video/mp4',
                $videoHydrator
            );

            return $hydrator;
        }
    ])->addDefinitions([
        'S3Client' => function(ContainerInterface $c) {

            $setting = $c->get('settings')['s3Resource'];
            $client = new S3Client( $setting['client']);

            return $client;
        }
    ])->addDefinitions([
        'ResourceValidator' => function(ContainerInterface $c) {

            $inputFilter = new InputFilter();

            // Name field
            $file = new Input('file');
            $file->setRequired(false);
            $file->getFilterChain()->attach(new FileUnpacking());
            $inputFilter->add($file);

            $name = new Input('name');
            $name->setRequired(false);
            $inputFilter->add($name);

            $tags = new Input('tags');
            $tags->setRequired(false);
            $tags->getFilterChain()->attach(new StringToArray());
            $inputFilter->add($tags);

            return $inputFilter;
        }
    ]);
};

