<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('api/users', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'test'
        ])->assertStatus(201)->assertJson([
            "data" => [
                "id" => 1,
                "username" => "test",
                "name" => "test"
            ]
        ]);
    }

    public function testRegisterFailed()
    {
        $this->post('api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "username" => [
                    "The username field is required."
                ],
                "password" => [
                    "The password field is required."
                ],
                "name" => [
                    "The name field is required."
                ]
            ]
        ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('api/users', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'test'
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "username" => [
                    "The username has already been taken."
                ]
            ]
        ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->assertStatus(200)->assertJson([
            "data" => [
                "id" => 1,
                "username" => "test",
                "name" => "test"
            ]
        ]);

        $user = User::where('username', 'test')->first();
        assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post('api/users/login', [
            'username' => 'test2',
            'password' => 'test',
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "username or password inccorect"
                ]
            ]
        ]);
    }

    public function testLoginPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('api/users/login', [
            'username' => 'test',
            'password' => 'test2',
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "username or password inccorect"
                ]
            ]
        ]);
    }
}
