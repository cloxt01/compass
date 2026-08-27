<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Services\AI\ProfileBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PackageSeeder::class);
    }

    public function test_ai_profile_tab_renders_for_authenticated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings', ['tab' => 'ai-profile']));

        $response->assertOk();
        $response->assertSee('Profil Kandidat untuk AI', false);
        $response->assertSee('data-testid="ai-profile-form"', false);
        $response->assertSee('data-testid="ai-profile-nama"', false);
        $response->assertSee('data-testid="ai-profile-ekspektasi-gaji"', false);
        $response->assertSee('data-testid="ai-profile-notice-period"', false);
        $response->assertSee('data-testid="ai-profile-save-button"', false);
    }

    public function test_saves_full_profile_to_apply_configuration()
    {
        $user = User::factory()->create();

        $payload = [
            'nama' => 'Muhammad Ferdiansyah',
            'phone' => '+6281234567890',
            'lokasi' => 'Lebak, Banten',
            'kewarganegaraan' => 'Indonesia',
            'gender' => 'Laki-laki',
            'tanggal_lahir' => '2000-05-10',

            'pendidikan' => 'S1',
            'jurusan' => 'Teknik Informatika',
            'institusi' => 'Universitas ABC',
            'tahun_lulus' => 2024,
            'ipk' => 3.45,

            'pengalaman_level' => 'FRESH_GRADUATE',
            'pengalaman_tahun' => 0,
            'posisi_terakhir' => 'Network Engineer Intern',
            'perusahaan_terakhir' => 'PT ANS Radius',

            'skills' => [
                ['name' => 'TCP/IP', 'level' => 'INTERMEDIATE'],
                ['name' => 'VLAN', 'level' => 'BASIC'],
                ['name' => '', 'level' => 'BASIC'], // should be filtered out
            ],
            'sertifikasi' => [
                ['nama' => 'HCIA', 'issuer' => 'Huawei', 'tahun' => 2024],
            ],
            'bahasa' => [
                ['nama' => 'English', 'level' => 'INTERMEDIATE'],
                ['nama' => 'Indonesia', 'level' => 'NATIVE'],
            ],

            'gaji_terakhir' => 3000000,
            'ekspektasi_gaji' => 5000000,
            'notice_period' => 'IMMEDIATELY',

            'bersedia_wfo' => 1,
            'bersedia_wfh' => 1,
            'bersedia_luar_kota' => 1,
            'bersedia_industri_banking' => 1,

            'catatan' => 'Punya SIM A dan C aktif',
        ];

        $response = $this->actingAs($user)->post(route('settings.ai-profile.save'), $payload);

        $response->assertRedirect(route('settings', ['tab' => 'ai-profile']));
        $response->assertSessionHas('success');

        $user->refresh();
        $profile = data_get($user->apply_configuration, 'auto_answer.profile');
        $this->assertIsArray($profile);
        $this->assertSame('Muhammad Ferdiansyah', $profile['nama']);
        $this->assertSame(5000000, $profile['ekspektasi_gaji']);
        $this->assertSame('IMMEDIATELY', $profile['notice_period']);
        $this->assertTrue($profile['bersedia_industri_banking']);

        // Skill kosong harus terfilter
        $this->assertCount(2, $profile['skills']);
        $this->assertSame('TCP/IP', $profile['skills'][0]['name']);

        // Sertifikasi & bahasa tersimpan
        $this->assertCount(1, $profile['sertifikasi']);
        $this->assertCount(2, $profile['bahasa']);
    }

    public function test_rejects_invalid_notice_period()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'notice_period' => 'RANDOM_INVALID_VALUE',
        ]);

        $response->assertSessionHasErrors('notice_period');
    }

    public function test_profile_builder_prefers_manual_over_platform()
    {
        $user = User::factory()->create([
            'apply_configuration' => [
                'auto_answer' => [
                    'profile' => [
                        'nama' => 'Manual Name',
                        'ekspektasi_gaji' => 8000000,
                        'notice_period' => 'ONE_MONTH',
                        'skills' => [['name' => 'Docker', 'level' => 'ADVANCED']],
                    ],
                ],
            ],
        ]);

        $platformProfile = [
            'first_name' => 'Platform',
            'last_name' => 'Name',
            'email' => 'from-platform@x.com',
        ];

        $profile = ProfileBuilder::build('glints', $platformProfile, $user);

        // Manual wins for name
        $this->assertSame('Manual Name', $profile['nama']);
        // Platform provides email since manual doesn't have one
        $this->assertSame('from-platform@x.com', $profile['email']);
        $this->assertSame(8000000, $profile['ekspektasi_gaji']);
        $this->assertSame('ONE_MONTH', $profile['notice_period']);
        $this->assertCount(1, $profile['skills']);
        $this->assertSame('Docker', $profile['skills'][0]['name']);
    }

    public function test_profile_completeness_returns_score()
    {
        // Empty profile → 0
        $this->assertSame(0, ProfileBuilder::completeness([]));

        // Full profile → reasonably high
        $full = [
            'nama' => 'X', 'phone' => 'Y', 'lokasi' => 'Z',
            'pendidikan' => 'S1', 'jurusan' => 'IT',
            'pengalaman_level' => 'FRESH_GRADUATE',
            'posisi_terakhir' => 'X', 'perusahaan_terakhir' => 'Y',
            'gaji_terakhir' => 3000000,
            'ekspektasi_gaji' => 5000000,
            'notice_period' => 'IMMEDIATELY',
            'kewarganegaraan' => 'Indonesia',
            'catatan' => 'note',
            'skills' => [['name' => 'A', 'level' => 'BASIC']],
            'sertifikasi' => [['nama' => 'X', 'issuer' => 'Y', 'tahun' => 2024]],
            'bahasa' => [['nama' => 'EN', 'level' => 'FLUENT']],
            'preferensi_lokasi_kerja' => ['WFO'],
        ];
        $score = ProfileBuilder::completeness($full);
        $this->assertGreaterThanOrEqual(90, $score);
    }

    public function test_apply_configuration_settings_tab_shows_link_to_ai_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings', ['tab' => 'ai-profile']));
        $response->assertOk();
        $response->assertSee('data-testid="settings-tab-ai-profile"', false);
        $response->assertSee('AI Profile', false);
    }
}
