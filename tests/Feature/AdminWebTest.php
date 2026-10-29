<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Materi;
use App\Models\Soal;
use App\Models\User;
use App\Services\UjianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminWebTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staf;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['roles' => User::ADMIN]);
        $this->staf = User::factory()->create(['roles' => User::STAFF]);
    }

    public function test_participant_cannot_log_into_panel_and_registration_is_closed(): void
    {
        $peserta = User::factory()->create(['roles' => User::PESERTA]);

        $this->post('/login', ['email' => $peserta->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->get('/register')->assertNotFound();

        $this->post('/login', ['email' => $this->staf->email, 'password' => 'password'])->assertRedirect('/home');
        $this->assertAuthenticatedAs($this->staf);
    }

    public function test_panel_pages_render_for_staff_and_user_management_is_admin_only(): void
    {
        Soal::factory()->count(3)->create(['kategori' => 'Numeric', 'kunci' => 'a']);
        $content = Content::create(['title' => 'Tentang', 'content' => 'Isi']);
        $peserta = User::factory()->create(['roles' => User::PESERTA]);
        $ujian = app(UjianService::class)->buatAtauLanjutkan($peserta);

        $this->actingAs($this->staf);
        foreach (['home', 'soal.index', 'soal.create', 'materi.index', 'materi.create', 'konten.index', 'hasil.index'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->get(route('hasil.show', $ujian))->assertOk()->assertSee($peserta->name);
        $this->get(route('konten.edit', $content))->assertOk();
        $this->get(route('user.index'))->assertForbidden();

        $this->actingAs($this->admin)->get(route('user.index'))->assertOk();
    }

    public function test_soal_crud_and_used_soal_is_protected(): void
    {
        $this->actingAs($this->staf);

        $data = [
            'kategori' => 'Logika', 'pertanyaan' => 'Semua A adalah B. Kesimpulannya?',
            'jawaban_a' => 'A', 'jawaban_b' => 'B', 'jawaban_c' => 'C', 'jawaban_d' => 'D', 'kunci' => 'c',
        ];
        $this->post(route('soal.store'), $data)->assertRedirect(route('soal.index'));
        $soal = Soal::firstWhere('pertanyaan', $data['pertanyaan']);

        $this->put(route('soal.update', $soal), ['kunci' => 'd'] + $data)->assertRedirect(route('soal.index'));
        $this->assertSame('d', $soal->refresh()->kunci);

        $this->post(route('soal.store'), ['kunci' => 'e'] + $data)->assertSessionHasErrors('kunci');

        $peserta = User::factory()->create(['roles' => User::PESERTA]);
        app(UjianService::class)->buatAtauLanjutkan($peserta);
        $this->delete(route('soal.destroy', $soal))->assertSessionHas('error');
        $this->assertModelExists($soal);

        $baru = Soal::factory()->create(['kategori' => 'Verbal', 'kunci' => 'a']);
        $this->delete(route('soal.destroy', $baru))->assertSessionHas('success');
        $this->assertModelMissing($baru);
    }

    public function test_materi_upload_and_api_returns_absolute_image_url(): void
    {
        Storage::fake('public');
        $this->actingAs($this->staf);

        $this->post(route('materi.store'), [
            'title' => 'Deret Angka',
            'content' => 'Cari selisih antarsuku.',
            'image' => UploadedFile::fake()->image('deret.jpg', 400, 300),
        ])->assertRedirect(route('materi.index'));

        $materi = Materi::first();
        Storage::disk('public')->assertExists($materi->image);

        $peserta = User::factory()->create(['roles' => User::PESERTA]);
        $this->actingAs($peserta, 'sanctum')
            ->getJson('/api/materis')
            ->assertJsonPath('data.0.image', Storage::disk('public')->url($materi->image));
    }

    public function test_admin_updates_user_without_changing_password_and_cannot_delete_self(): void
    {
        $this->actingAs($this->admin);
        $peserta = User::factory()->create(['roles' => User::PESERTA, 'email' => 'lama@contoh.test']);
        $hash = $peserta->password;

        $this->put(route('user.update', $peserta), [
            'name' => 'Nama Baru', 'email' => 'lama@contoh.test', 'roles' => User::PESERTA, 'password' => '',
        ])->assertRedirect(route('user.index'));
        $this->assertSame($hash, $peserta->refresh()->password);
        $this->assertSame('Nama Baru', $peserta->name);

        $this->delete(route('user.destroy', $this->admin))->assertSessionHas('error');
        $this->assertModelExists($this->admin);
    }
}
