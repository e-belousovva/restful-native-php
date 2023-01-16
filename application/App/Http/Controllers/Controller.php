<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Exception;

class Controller
{
    /**
     * @param $payloads
     * @return array $response
     */
    protected static function validation($payloads): array
    {
        $response = [];
        foreach ($payloads as $payload) {

            if ($payload->validator === 'required') {
                if ($payload->data === null || $payload->data = '' || !isset($payload->data)) {
                    $response[] = [
                        'key' => $payload->key,
                        'message' => "The $payload->key field is required"
                    ];
                }
            }

            if ($payload->validator === 'string') {
                if (preg_match('/[^A-Za-z]/', (string)$payload->data)) {
                    $response[] = [
                        'key' => $payload->key,
                        'message' => "$payload->key expects an Alphabet."
                    ];
                }
            }

            if ($payload->validator === 'numeric') {
                if (preg_match('/[^\d_]/', (string)$payload->data)) {
                    $response[] = [
                        'key' => $payload->key,
                        'message' => "$payload->key expects a Number."
                    ];
                }
            }

            if ($payload->validator === 'boolean') {
                if (strtolower(gettype($payload->data)) !== 'boolean') {
                    $response[] = [
                        'key' => $payload->key,
                        'message' => "$payload->key expects a Boolean."
                    ];
                }
            }

            if ($payload->validator === 'emailExists') {
                try {
                    $checkEmail = (new User())::checkEmail((string)$payload->data);

                    if ($checkEmail['status']) {
                        $response[] = [
                            'key' => $payload->key,
                            'message' => "$payload->key already exists. Please try with a different Email."
                        ];
                    }
                } catch (Exception $e) {
                    /** */
                }
            }
        }

        $validationErrors = [];

        if (count($response) < 1) {
            $validationErrors['status'] = false;
            $validationErrors['errors'] = [];
        } else {
            $validationErrors['status'] = true;
            $validationErrors['errors'] = $response;
        }

        return $validationErrors;
    }

    /**
     * @param void
     * @return string
     */
    protected static function JWTSecret(): string
    {
        return 'K-lyniEXe8Gm-WOA7IhUd5xMrqCBSPzZFpv02Q6sJcVtaYD41wfHRL3';
    }

    /**
     * @param int $status
     * @param array $array
     * @return void
     */
    public function json(int $status, array $array): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($array);
        die();
    }

    /**
     * @param null $name
     * @return mixed $name
     */
    public function PUT($name = null): mixed
    {
        $lines = file('php://input');
        $keyLinePrefix = 'Content-Disposition: form-data; name="';

        $_PUT = [];

        foreach ($lines as $num => $line) {
            if (str_contains($line, $keyLinePrefix)) {
                $_PUT[substr($line, strlen($keyLinePrefix), -3)] = trim($lines[$num + 2]);
            }
        }

        if ($name) {
            return $_PUT[$name];
        }

        return $_PUT;
    }
}
