<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OAuthTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->providerName = 'google';
    }

    /**
     * @test
     */
    public function Googleの認証画面を表示できる()
    {
        // URLをコール
        $this->get(route('socialOAuth', ['provider' => $this->providerName]))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function Googleアカウントでユーザー登録できる()
    {
        // URLをコール
        $this->get(route('oauthCallback', ['provider' => $this->providerName]))
            ->assertStatus(200);
    }
}