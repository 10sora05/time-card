<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function 名前が未入力の場合_バリデーションエラーが表示される()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function メールアドレスが未入力の場合_バリデーションエラーが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワードが8文字未満の場合_バリデーションエラーが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードが一致しない場合_バリデーションエラーが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpass',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードが未入力の場合_バリデーションエラーが表示される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function 全ての項目が正しく入力されている場合_登録に成功しデータが保存される()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/attendance'); // リダイレクト先を実際のルートに合わせて変更
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
