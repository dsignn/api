<?php
declare(strict_types=1);

namespace App\Module\Oauth\Repository;

use App\Module\Oauth\Entity\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $scopes = [
            'basic' => [
                'description' => 'Basic details about you',
            ],
            'email' => [
                'description' => 'Your email address',
            ],
        ];
        if (\array_key_exists($identifier, $scopes) === false) {
            return;
        }
        $scope = new ScopeEntity();
        $scope->setIdentifier($identifier);
        return $scope;
    }

    /**
     * @inheritDoc
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        // Example of programatically modifying the final scope of the access token
        if ((int) $userIdentifier === 1) {
            $scope = new ScopeEntity();
            $scope->setIdentifier('email');
            $scopes[] = $scope;
        }
        return $scopes;
    }
}