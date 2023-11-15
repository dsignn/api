<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Filter\DefaultFilter;
use App\Filter\File\FileTransform;
use App\Filter\StringToArray;
use App\Hydrator\MapHydrator;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\UnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Resource\Entity\AudioResourceEntity;
use App\Module\Resource\Entity\Embedded\Dimension;
use App\Module\Resource\Entity\ImageResourceEntity;
use App\Module\Resource\Entity\VideoResourceEntity;
use App\Module\Resource\Event\MetadataEvent;
use App\Module\Resource\Event\S3DeleteEvent;
use App\Module\Resource\Event\S3UploaderEvent;
use App\Module\Resource\Http\QueryString\ResourceQueryString;
use App\Module\Resource\Storage\ResourceStorage;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\MultiEntityPrototype;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use App\Validator\File\FileMimeType;
use App\Validator\File\FileSize;
use App\Validator\Mongo\ObjectIdValidator;
use Aws\S3\S3Client;
use DI\ContainerBuilder;
use Laminas\Filter\ToInt;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\NotEmpty;
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

            $storage->getEventManager()->attach(
                Storage::$BEFORE_DELETE,
                new S3DeleteEvent($c->get('S3Client'), $c->get('settings')['s3Resource']['bucket'])
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
            )->addEntityPrototype(
                'video/webm',
                new VideoResourceEntity()
            )->addEntityPrototype(
                'audio/ogg',
                new AudioResourceEntity()
            )->addEntityPrototype(
                'audio/mp3',
                new AudioResourceEntity()
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

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $imageHydrator = new ClassMethodsHydrator();
            $imageHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $imageHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $imageHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $imageHydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));

            $videoHydrator = new ClassMethodsHydrator();
            $videoHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $videoHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $videoHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $videoHydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));

            $strategyDimension = new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Dimension()));

            $imageHydrator->addStrategy('dimension', $strategyDimension);
            $videoHydrator->addStrategy('dimension', $strategyDimension);

            $audioHydrator = new ClassMethodsHydrator();
            $audioHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $audioHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $audioHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $audioHydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));


            $hydrator->addHydrator(
                'image/jpeg',
                $imageHydrator
            )->addHydrator(
                'image/png',
                $imageHydrator
            )->addHydrator(
                'video/mp4',
                $videoHydrator
            )->addHydrator(
                'video/webm',
                $videoHydrator
            )->addHydrator(
                'audio/ogg',
                $audioHydrator
            )->addHydrator(
                'audio/mp3',
                $audioHydrator
            );

            return $hydrator;
        }
    ])->addDefinitions([
        'RestResourceEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new MapHydrator();
            $hydrator->setTypeField('mimeType');
            $hydrator->setEntityPrototype($c->get('ResourceEntityPrototype'));

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $imageHydrator = new ClassMethodsHydrator();
            $imageHydrator->setNamingStrategy(new CamelCaseStrategy());
            $imageHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $imageHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $imageHydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));


            $videoHydrator = new ClassMethodsHydrator();
            $videoHydrator->setNamingStrategy(new CamelCaseStrategy());
            $videoHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $videoHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $videoHydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));


            $strategyDimension = new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new Dimension()));
            $imageHydrator->addStrategy('dimension', $strategyDimension);
            $videoHydrator->addStrategy('dimension', $strategyDimension);

            $audioHydrator = new ClassMethodsHydrator();
            $audioHydrator->setNamingStrategy(new CamelCaseStrategy());
            $audioHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $audioHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $audioHydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));

            $hydrator->addHydrator(
                'image/jpeg',
                $imageHydrator
            )->addHydrator(
                'image/png',
                $imageHydrator
            )->addHydrator(
                'video/mp4',
                $videoHydrator
            )->addHydrator(
                'video/webm',
                $videoHydrator
            )->addHydrator(
                'audio/ogg',
                $audioHydrator
            )->addHydrator(
                'audio/mp3',
                $audioHydrator
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
        'ResourcePostValidator' => function(ContainerInterface $c) {

            $inputFilter = new InputFilter();

            // Name field
            $input = new Input('file');
            $input->getFilterChain()->attach(new FileTransform());
            $input->getValidatorChain()->attach(new FileSize(['max' => '20MB',]));
            $input->getValidatorChain()->attach(new FileMimeType(
                ['mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg', 'video/mp4', 'video/webm', 'audio/ogg'],])
            );
            $inputFilter->add($input);

            $input = new Input('name');
            $input->setRequired(false);
            $inputFilter->add($input);

            $input = new Input('tags');
            $input->setRequired(false);
            $input->getFilterChain()->attach(new StringToArray());
            $inputFilter->add($input);

            $organizationReference = new InputFilter();

            $id = new Input('id');
            $id->getValidatorChain()
                ->attach(new NotEmpty())
                ->attach(new ObjectIdValidator());
            
            $organizationReference->add($id);

            $collection = new Input('collection');
            $collection->setRequired(false);
            $collection->getFilterChain()
                ->attach(new DefaultFilter('collection'));

            $organizationReference->add($collection);
            $inputFilter->add($organizationReference, 'organizationReference');

            return $inputFilter;
        }
    ])->addDefinitions([
        'ResourceValidator' => function(ContainerInterface $c) {

            $inputFilter = new InputFilter();

            // Name field
            $input = new Input('file');
            $input->setRequired(false);
            $input->getFilterChain()->attach(new FileTransform());
            $input->getValidatorChain()->attach(new FileSize(['max' => '20MB',]));
            $input->getValidatorChain()->attach(new FileMimeType(
                    ['mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg', 'video/mp4', 'video/webm', 'audio/ogg'],])
            );
            $inputFilter->add($input);

            $input = new Input('name');
            $input->setRequired(false);
            $inputFilter->add($input);

            $input = new Input('tags');
            $input->setRequired(false);
            $input->getFilterChain()->attach(new StringToArray());
            $inputFilter->add($input);

            $dimension = new InputFilter();

            $input = new Input('height');
            $input->setRequired(false);
            $input->getFilterChain()->attach(new ToInt());
            $dimension->add($input);

            $input = new Input('width');
            $input->setRequired(false);
            $input->getFilterChain()->attach(new ToInt());
            $dimension->add($input);

            $inputFilter->add($dimension, 'dimension');


            $organizationReference = new InputFilter();

            $id = new Input('id');
            $id->getValidatorChain()
                ->attach(new NotEmpty())
                ->attach(new ObjectIdValidator());
            
            $organizationReference->add($id);

            $collection = new Input('collection');
            $collection->setRequired(false);
            $collection->getFilterChain()
                ->attach(new DefaultFilter('resource'));

            $organizationReference->add($collection);
            $inputFilter->add($organizationReference, 'organizationReference');

            return $inputFilter;
        }
    ])->addDefinitions([
        ResourceQueryString::class => function(ContainerInterface $c) {
            return new ResourceQueryString();
        }
    ]);
};

