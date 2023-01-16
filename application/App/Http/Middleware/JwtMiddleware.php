<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use DomainException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Exception;
use App\Models\Token;
use App\Models\User;


class JwtMiddleware
{
    protected static User $user;
    protected static string $token;
    protected static int $user_id;


    public function handle(): bool
    {
        return self::getAndDecodeToken();
    }

    /**
     * JWTSecret
     *
     * Returns a JWT Secret...
     *
     * @param void
     * @return  string
     */
    private static function JWTSecret(): string
    {
        return 'K-lyniEXe8Gm-WOA7IhUd5xMrqCBSPzZFpv02Q6sJcVtaYD41wfHRL3';
    }

    /**
     * getToken
     *
     * Fetches and return the JWT Token from the request Header
     *
     * @param void
     * @return  string
     */
    protected static function getToken(): string
    {
        self::$token = $_SERVER['HTTP_AUTHORIZATION'];

        return $_SERVER['HTTP_AUTHORIZATION'];
    }

    /**
     * validateToken
     *
     * Validates the JWT Token and returns a boolean true...
     *
     * @return string|bool
     */
    protected static function validateToken(): string|bool
    {
        self::getToken();

        if (self::$token == '' || self::$token == null) {
            return false;
        }

        try {
            $token = explode('Bearer ', self::$token);

            if (isset($token[1]) && $token == '') {
                return false;
            }

            return $token[1];
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * getAndDecodeToken
     *
     * Decodes and returns a boolean true or the user_id.
     *
     * @param void
     * @return  bool
     */
    public function getAndDecodeToken(): bool
    {
        $token = self::validateToken();

        try {
            if ($token) {
                $decodedToken = (array)JWT::decode($token, self::JWTSecret(), array('HS256'));

                if ($decodedToken['exp'] > time()) {
                    $_SESSION['user_id'] = $decodedToken['user_id'];

                    return true;
                }

                return false;
            }
            throw new Exception('bla');
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode('Invalid token');
            die();
        }
    }
}