<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function メールアドレスが未入力の場合_バリデーションエラーが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワードが未入力の場合_バリデーションエラーが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function 登録情報と一致しない場合_ログインに失敗する()
    {
        // 事前にユーザーは存在している
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        // 間違ったパスワードでログイン
        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login'); // ログイン画面に戻る
        $response->assertSessionHasErrors('email'); // Laravelはemailにエラー返す（セキュリティ上）
        $this->assertGuest(); // ログインされていないことを確認
    }
}
