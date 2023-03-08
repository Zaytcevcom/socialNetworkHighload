<?php

declare(strict_types=1);

namespace App\Components;

use DateTimeZone;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Exception;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;

class JWTParser
{
    use CryptTrait;

    private Configuration $jwtConfiguration;

    public function __construct(
        private readonly CryptKey $publicKey,
        /** @var non-empty-string $contents */
        private readonly string $contents = 'z',
    ) {
        $this->initJwtConfiguration();
    }

    /**
     * @throws OAuthServerException
     */
    public function parse(string $jwt): DataSet
    {
        try {
            /** @var Plain $token */
            $token = $this->jwtConfiguration->parser()->parse($jwt);
        } catch (Exception $exception) {
            throw OAuthServerException::accessDenied($exception->getMessage(), null, $exception);
        }

        try {
            $constraints = $this->jwtConfiguration->validationConstraints();
            $this->jwtConfiguration->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated) {
            throw OAuthServerException::accessDenied('Access token could not be verified');
        }

        return $token->claims();
    }

    /**
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     */
    public function parseRefreshToken(string $refreshToken): array
    {
        $password = (!\is_string($this->encryptionKey)) ? $this->encryptionKey?->getRawBytes() ?? '' : $this->encryptionKey;

        /**
         * @var array{
         *     refresh_token_id:string
         * }
         */
        return json_decode(Crypto::decryptWithPassword($refreshToken, $password), true);
    }

    private function initJwtConfiguration(): void
    {
        $this->jwtConfiguration = Configuration::forSymmetricSigner(
            signer: new Sha256(),
            key: InMemory::plainText(
                $this->contents
            )
        );

        /** @var non-empty-string $contents */
        $contents = $this->publicKey->getKeyContents();

        $this->jwtConfiguration->setValidationConstraints(
            new StrictValidAt(new SystemClock(new DateTimeZone(date_default_timezone_get()))),
            new SignedWith(
                new Sha256(),
                InMemory::plainText(
                    contents: $contents,
                    passphrase: $this->publicKey->getPassPhrase() ?? ''
                )
            )
        );
    }
}
