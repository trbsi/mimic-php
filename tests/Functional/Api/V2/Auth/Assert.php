<?php
namespace Tests\Functional\Api\V2\Auth;

use Tests\Assert\AssertInterface;
use Tests\Assert\AssertAbstract;

class Assert extends AssertAbstract implements AssertInterface
{

    /**
     * @inheritdoc
     */
    public function getAssertJsonStructureOnSuccess(?string $type = null): array
    {
        return [
            'username',
            'token',
            'user_id',
            'email'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAssertJsonOnSuccess(array $data, ?string $type = null): array
    {
        return [
            'username' => $data['username'],
            'email' => $data['email'],
        ];
    }
}
