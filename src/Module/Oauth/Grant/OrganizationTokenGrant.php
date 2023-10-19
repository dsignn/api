<?php
declare(strict_types=1);

namespace App\Module\Oauth\Grant;

use App\Module\Oauth\Entity\AccessTokenEntity;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Storage\StorageInterface;
use League\OAuth2\Server\Grant\AbstractGrant;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use MongoDB\BSON\ObjectId;

use function DI\value;

/**
 * Class OrganizationTokenGrant
 * @package App\Module\Oauth\Grant
 */
class OrganizationTokenGrant extends AbstractGrant {

    /**
     * @var StorageInterface
     */
    protected $organizationStorage;

    public function __construct(
        AccessTokenRepositoryInterface $accessTokenRepository,
        ScopeRepositoryInterface $scopeRepository,
        StorageInterface $organizationStorage
    ) {
        $this->setAccessTokenRepository($accessTokenRepository);
        $this->setScopeRepository($scopeRepository);
        $this->organizationStorage = $organizationStorage;
    }

    public function getIdentifier() {
        return 'organization-token';
    }

    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ) {

        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $this->checkScopes($scopes);
      
        $organizationId = $this->getRequestParameter('organization_id', $request);

        try {
            $id = new ObjectId($organizationId);
        } catch(Exception $e) {

            throw OAuthServerException::invalidRequest('organization_id');
        }

        /** @var OrganizationEntity $organization  */
        $organization = $this->organizationStorage->get($organizationId);
        if (!$organization) {
            throw new OAuthServerException(
                'The requested organization is not found',
                5, 
                'invalid_resource',
                404,
                "Organization not found"
            );
        }

        $accessToken = $this->accessTokenRepository->getByIdentifier($organization->getIdentifier());

        echo '<pre>';

        if ($accessToken) {
            // TODO alter expiration date :)
            $accessToken->setPrivateKey($this->privateKey);          
          
        } else {

            $accessToken = new AccessTokenEntity();
            foreach ($scopes as $scope) {
                $accessToken->addScope($scope);
            }
            $now = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
            $expired = (DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')))->add($accessTokenTTL);

            $accessToken->setUserIdentifier($organization->getIdentifier());
            $accessToken->setExpiryDateTime($expired);
            $accessToken->setStartDateTime($now);
            $accessToken->setPrivateKey($this->privateKey);
            $accessToken->setIdentifier($this->generateUniqueIdentifier());


            $this->accessTokenRepository->persistNewAccessToken($accessToken);
            // TODO workaround
            $accessToken = $this->accessTokenRepository->getByIdentifier($organization->getIdentifier());
            $accessToken->setPrivateKey($this->privateKey);

            $organization->setOauthToken((string) $accessToken);
            $this->organizationStorage->update($organization);
        }   


        $responseType->setAccessToken($accessToken);
   
        return $responseType;
    }

    /**
     * Undocumented function
     *
     * @param array $scopes
     * @return void
     */
    protected function checkScopes($scopes) {

        $check = false;
        foreach ($scopes as $scopeItem) {

            if ($scopeItem->getIdentifier() === 'client') {
                $check = true;
                break;
            }
        }

        if (!$check) {

            throw new OAuthServerException(
                'The requested scope is invalid, unknown, or malformed',
                5, 
                'invalid_scope',
                400,
                "The client must be 'client'"
            );
        }
    }
}