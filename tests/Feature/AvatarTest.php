<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_and_remove_avatar(): void
    {
        Storage::fake('public');
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create(['name' => 'Raghad']);

        $this->post('/profile/avatar', ['avatar' => UploadedFile::fake()->image('a.png')])->assertRedirect('/login');

        $this->actingAs($user)->from('/')->post('/profile/avatar', ['avatar' => UploadedFile::fake()->image('a.png')])->assertRedirect('/');
        $path = $user->fresh()->avatar;
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertOk()->assertSee('storage/'.$path, false);

        // a new picture replaces the old file
        $this->post('/profile/avatar', ['avatar' => UploadedFile::fake()->image('b.jpg')]);
        Storage::disk('public')->assertMissing($path);

        // not an image
        $this->post('/profile/avatar', ['avatar' => UploadedFile::fake()->create('x.pdf', 10)])->assertSessionHasErrors('avatar');

        $this->delete('/profile/avatar');
        $this->assertNull($user->fresh()->avatar);
        $this->get('/')->assertOk()->assertSee('>R</span>', false);
    }
}
