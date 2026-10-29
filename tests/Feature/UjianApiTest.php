<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UjianApiTest extends TestCase
{
    use RefreshDatabase;

    private User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cbt.jumlah_soal' => 3, 'cbt.durasi_menit' => 30]);

        foreach (['Numeric', 'Verbal', 'Logika'] as $kategori) {
            foreach (['a', 'b', 'c', 'd'] as $kunci) {
                Soal::factory()->create(['kategori' => $kategori, 'kunci' => $kunci]);
            }
        }

        $this->peserta = User::factory()->create(['roles' => User::PESERTA]);
    }

    public function test_api_without_token_returns_json_401(): void
    {
        $this->get('/api/get-soal-ujian?kategori=Numeric')
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_login_rejects_wrong_password_with_generic_message_and_admin_accounts(): void
    {
        $this->postJson('/api/login', ['email' => $this->peserta->email, 'password' => 'salah'])
            ->assertStatus(401)->assertJson(['message' => 'Email atau kata sandi salah.']);
        $this->postJson('/api/login', ['email' => 'tidakada@contoh.test', 'password' => 'salah'])
            ->assertStatus(401)->assertJson(['message' => 'Email atau kata sandi salah.']);

        $admin = User::factory()->create(['roles' => User::ADMIN]);
        $this->postJson('/api/login', ['email' => $admin->email, 'password' => 'password'])->assertStatus(403);

        $this->postJson('/api/login', ['email' => $this->peserta->email, 'password' => 'password'])
            ->assertOk()->assertJsonStructure(['access_token', 'user' => ['id', 'name', 'email', 'roles']]);
    }

    public function test_register_creates_participant_role(): void
    {
        $this->postJson('/api/register', ['name' => 'Peserta Baru', 'email' => 'baru@contoh.test', 'password' => 'rahasia123'])
            ->assertOk()->assertJsonPath('user.roles', User::PESERTA);

        $this->assertSame(User::PESERTA, User::where('email', 'baru@contoh.test')->value('roles'));
    }

    public function test_create_ujian_is_idempotent_while_active(): void
    {
        Sanctum::actingAs($this->peserta);

        $first = $this->postJson('/api/create-ujian')->assertCreated()->json('data');
        $this->assertCount(3, $first['kategori']);
        $this->assertSame(3, $first['kategori'][0]['jumlah_soal']);
        $this->assertSame('belum', $first['kategori'][0]['status']);

        $second = $this->postJson('/api/create-ujian')->assertOk()->json('data');
        $this->assertSame($first['id'], $second['id']);
        $this->assertSame(1, Ujian::count());
    }

    public function test_soal_hides_key_starts_server_timer_and_resumes_answers(): void
    {
        Sanctum::actingAs($this->peserta);
        $this->postJson('/api/create-ujian');

        $response = $this->getJson('/api/get-soal-ujian?kategori=Numeric')->assertOk();
        $this->assertCount(3, $response->json('data'));
        $this->assertArrayNotHasKey('kunci', $response->json('data.0'));
        $this->assertSame(1800, $response->json('sisa_detik'));
        $this->assertSame(30, $response->json('timer'));

        $soalId = $response->json('data.0.id');
        $this->postJson('/api/answers', ['soal_id' => $soalId, 'jawaban' => 'b'])
            ->assertOk()
            ->assertJsonMissingPath('jawaban')
            ->assertJsonPath('data.jawaban', 'b');

        $this->travel(5)->minutes();
        $again = $this->getJson('/api/get-soal-ujian?kategori=Numeric')->assertOk();
        $this->assertSame('b', $again->json('data.0.jawaban'));
        $this->assertSame(1500, $again->json('sisa_detik'));
    }

    public function test_running_category_does_not_leak_score_or_correct_count(): void
    {
        Sanctum::actingAs($this->peserta);
        $this->postJson('/api/create-ujian');
        $soal = $this->getJson('/api/get-soal-ujian?kategori=Numeric')->json('data.0');
        $kunci = Soal::find($soal['id'])->kunci;
        $this->postJson('/api/answers', ['soal_id' => $soal['id'], 'jawaban' => $kunci])->assertOk();

        $numeric = collect($this->getJson('/api/ujian')->json('data.kategori'))->firstWhere('kategori', 'Numeric');
        $this->assertSame('berjalan', $numeric['status']);
        $this->assertSame(1, $numeric['terjawab']);
        $this->assertNull($numeric['nilai']);
        $this->assertNull($numeric['benar']);
        $this->assertNull($numeric['salah']);

        $hasil = collect($this->getJson('/api/hasil-ujian')->json('data.terakhir.kategori'))->firstWhere('kategori', 'Numeric');
        $this->assertNull($hasil['benar']);
    }

    public function test_answer_validation_and_foreign_soal_rejected(): void
    {
        Sanctum::actingAs($this->peserta);
        $this->postJson('/api/create-ujian');
        $soalId = $this->getJson('/api/get-soal-ujian?kategori=Verbal')->json('data.0.id');

        $this->postJson('/api/answers', ['soal_id' => $soalId, 'jawaban' => 'e'])->assertStatus(422);

        $bukanMilik = Soal::whereNotIn('id', Ujian::first()->soalList()->pluck('soal_id'))->value('id');
        $this->postJson('/api/answers', ['soal_id' => $bukanMilik, 'jawaban' => 'a'])->assertStatus(422);

        $this->getJson('/api/get-soal-ujian?kategori=Sejarah')->assertStatus(422);
    }

    public function test_answer_after_time_is_up_is_rejected_and_category_closed(): void
    {
        Sanctum::actingAs($this->peserta);
        $this->postJson('/api/create-ujian');
        $soalId = $this->getJson('/api/get-soal-ujian?kategori=Logika')->json('data.0.id');

        $this->travel(31)->minutes();

        $this->postJson('/api/answers', ['soal_id' => $soalId, 'jawaban' => 'a'])->assertStatus(422);
        $this->assertSame(Ujian::DONE, Ujian::first()->status_logika);
        $this->assertSame(0, (int) Ujian::first()->nilai_logika);
    }

    public function test_nilai_is_computed_from_saved_answers_and_finishing_all_closes_exam(): void
    {
        Sanctum::actingAs($this->peserta);
        $this->postJson('/api/create-ujian');

        foreach (['Numeric', 'Verbal', 'Logika'] as $kategori) {
            $soal = $this->getJson('/api/get-soal-ujian?kategori='.$kategori)->json('data');
            // Dua benar, satu salah → 67.
            foreach ($soal as $i => $item) {
                $kunci = Soal::find($item['id'])->kunci;
                $jawaban = $i < 2 ? $kunci : ($kunci === 'a' ? 'b' : 'a');
                $this->postJson('/api/answers', ['soal_id' => $item['id'], 'jawaban' => $jawaban])->assertOk();
            }

            $this->getJson('/api/get-nilai?kategori='.$kategori)
                ->assertOk()
                ->assertJsonPath('nilai', 67)
                ->assertJsonPath('data.benar', 2)
                ->assertJsonPath('data.salah', 1)
                ->assertJsonPath('data.kosong', 0);
        }

        $ujian = Ujian::first();
        $this->assertNotNull($ujian->selesai_at);
        $this->assertSame('Lulus', $ujian->hasil);

        // Memanggil ulang nilai tetap aman (idempoten) dan ujian aktif kosong.
        $this->getJson('/api/get-nilai?kategori=Logika')->assertOk()->assertJsonPath('nilai', 67);
        $this->getJson('/api/ujian')->assertOk()->assertJsonPath('data', null);

        $hasil = $this->getJson('/api/hasil-ujian')->assertOk();
        $this->assertSame(67, $hasil->json('data.terakhir.rata_rata'));
        $this->assertCount(1, $hasil->json('data.riwayat'));

        // Ujian berikutnya = ujian baru.
        $this->postJson('/api/create-ujian')->assertCreated();
        $this->assertSame(2, Ujian::count());
    }

    public function test_unanswered_questions_count_as_empty_and_answers_rejected_after_finish(): void
    {
        Sanctum::actingAs($this->peserta);
        $this->postJson('/api/create-ujian');
        $soalId = $this->getJson('/api/get-soal-ujian?kategori=Numeric')->json('data.0.id');

        $this->getJson('/api/get-nilai?kategori=Numeric')
            ->assertJsonPath('nilai', 0)
            ->assertJsonPath('data.kosong', 3);

        $this->postJson('/api/answers', ['soal_id' => $soalId, 'jawaban' => 'a'])->assertStatus(422);
    }

    public function test_contents_and_materis_are_read_only(): void
    {
        Sanctum::actingAs($this->peserta);
        $content = Content::create(['title' => 'Tentang', 'content' => 'Isi']);

        $this->getJson('/api/contents?id='.$content->id)->assertOk()->assertJsonPath('data.0.title', 'Tentang')->assertJsonPath('data.0.image', null);
        $this->getJson('/api/materis')->assertOk()->assertJsonPath('status', 'success');
        $this->postJson('/api/contents', ['title' => 'x'])->assertStatus(405);
        $this->deleteJson('/api/materis/1')->assertStatus(404);
    }
}
