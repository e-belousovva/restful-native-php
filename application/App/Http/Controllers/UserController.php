<?php

declare(strict_types=1);


namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Firebase\JWT\JWT;

class UserController extends Controller
{
    /**
     * @return void Anonymous
     */
    public function register(): void
    {
        $request = $_REQUEST;

        $validationObject = [
            (object)[
                'validator' => 'required',
                'data' => $request['name'] ?? '',
                'key' => 'Name'
            ],
            (object)[
                'validator' => 'string',
                'data' => $request['name'] ?? '',
                'key' => 'Name'
            ],
            (object)[
                'validator' => 'emailExists',
                'data' => $request['email'] ?? '',
                'key' => 'Email'
            ],
        ];

        $validationBag = parent::validation($validationObject);

        if ($validationBag['status']) {
            foreach ($validationBag['errors'] as $error) {
                echo $error['message'];
                echo "\n";
            }
            die();
        }

        $payload = [
            'name' => htmlspecialchars(stripcslashes(strip_tags($request['name']))),
            'email' => stripcslashes(strip_tags($request['email'])),
            'password' => password_hash($request['password'], PASSWORD_BCRYPT),
        ];

        try {
            $userData = (new User())::createUser($payload);

            if ($userData['status']) {
                $tokenSecret = parent::JWTSecret();
                $tokenPayload = [
                    'iat' => time(),
                    'iss' => 'PHP_MINI_REST_API',
                    'exp' => strtotime('+ 7 Days'),
                    'user_id' => (int)$userData['data']['user_id']
                ];

                $jwt = JWT::encode($tokenPayload, $tokenSecret);

                $this->json(201, ['token' => $jwt]);
            }
        } catch (Exception $e) {
            $this->json((int)$e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    /**
     * @return void Anonymous
     */
    public function login(): void
    {
        $request = $_REQUEST;

        $validationObject = [
            (object)[
                'validator' => 'required',
                'data' => $data->email ?? '',
                'key' => 'Email'
            ],
            (object)[
                'validator' => 'required',
                'data' => $data->password ?? '',
                'key' => 'Password'
            ]
        ];

        $validationBag = parent::validation($validationObject);

        if ($validationBag['status']) {
            foreach ($validationBag['errors'] as $error) {
                echo $error['message'];
                echo "\n";
            }
            die();
        }

        $payload = [
            'email' => stripcslashes(strip_tags($request['email'])),
            'password' => $request['password'],
        ];

        try {
            $userData = (new User())::checkEmail($payload['email']);

            if ($userData['status']) {
                if (password_verify($payload['password'], $userData['data']['password'])) {
                    $tokenSecret = parent::JWTSecret();
                    $tokenPayload = array(
                        'iat' => time(),
                        'iss' => 'PHP_MINI_REST_API', //!!Modify:: Modify this to come from a constant
                        "exp" => strtotime('+ 7 Days'),
                        "user_id" => $userData['data']['id']
                    );
                    $jwt = JWT::encode($tokenPayload, $tokenSecret);

                    $this->json(201, ['token' => $jwt]);
                }
                $this->json(401, ['message' => 'Please, check your Email and Password and try again.']);
            }
            $this->json(404, ['message' => 'User not found. Check email please']);
        } catch (Exception $e) {
            $this->json($e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}