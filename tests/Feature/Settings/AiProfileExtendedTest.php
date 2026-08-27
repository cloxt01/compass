<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use App\Services\AI\ProfileBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Extended coverage for Settings -> AI Profile (validation, filtering, persistence,
 * prefill, auth guard) and ProfileBuilder manual/platform precedence + completeness.
 */
class AiProfileExtendedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PackageSeeder::class);
    }

    protected function makeUser(array $attrs = []): User
    {
        return User::factory()->create($attrs);
    }

    // ---------------------------------------------------------------- VALIDATION

    public function test_rejects_invalid_pendidikan()
    {
        $response = $this->actingAs($this->makeUser())
            ->post(route('settings.ai-profile.save'), ['pendidikan' => 'S9']);

        $response->assertSessionHasErrors('pendidikan');
    }

    public function test_rejects_invalid_skill_level()
    {
        $response = $this->actingAs($this->makeUser())
            ->post(route('settings.ai-profile.save'), [
                'skills' => [['name' => 'Docker', 'level' => 'GURU']],
            ]);

        $response->assertSessionHasErrors('skills.0.level');
    }

    public function test_rejects_invalid_bahasa_level_and_out_of_range_numbers()
    {
        $response = $this->actingAs($this->makeUser())
            ->post(route('settings.ai-profile.save'), [
                'bahasa' => [['nama' => 'English', 'level' => 'PERFECT']],
                'ipk' => 5.5,
                'tahun_lulus' => 1900,
                'tanggal_lahir' => now()->addYear()->toDateString(),
                'pengalaman_level' => 'SUPER_SENIOR',
            ]);

        $response->assertSessionHasErrors([
            'bahasa.0.level', 'ipk', 'tahun_lulus', 'tanggal_lahir', 'pengalaman_level',
        ]);
    }

    public function test_invalid_payload_does_not_persist_anything()
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'nama' => 'TEST_Should Not Save',
            'notice_period' => 'NEVER',
        ]);

        $user->refresh();
        $this->assertNull(data_get($user->apply_configuration, 'auto_answer.profile'));
    }

    // ---------------------------------------------------------------- AUTH GUARD

    public function test_guest_cannot_save_ai_profile()
    {
        $response = $this->post(route('settings.ai-profile.save'), ['nama' => 'TEST_Guest']);
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_view_ai_profile_tab()
    {
        $this->get(route('settings', ['tab' => 'ai-profile']))->assertRedirect(route('login'));
    }

    public function test_unknown_tab_returns_404()
    {
        $this->actingAs($this->makeUser())
            ->get(route('settings', ['tab' => 'not-a-tab']))
            ->assertNotFound();
    }

    // ---------------------------------------------------------------- PERSISTENCE

    public function test_empty_sertifikasi_and_bahasa_rows_are_filtered()
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'nama' => 'TEST_Filter',
            'skills' => [['name' => '', 'level' => 'BASIC'], ['name' => '  ', 'level' => '']],
            'sertifikasi' => [
                ['nama' => '', 'issuer' => '', 'tahun' => null],
                ['nama' => 'CCNA', 'issuer' => 'Cisco', 'tahun' => 2023],
            ],
            'bahasa' => [['nama' => '', 'level' => 'BASIC']],
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $profile = data_get($user->apply_configuration, 'auto_answer.profile');

        $this->assertSame([], $profile['skills']);
        $this->assertCount(1, $profile['sertifikasi']);
        $this->assertSame('CCNA', $profile['sertifikasi'][0]['nama']);
        $this->assertSame([], $profile['bahasa']);
    }

    public function test_unchecked_booleans_are_saved_as_false()
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'nama' => 'TEST_Bool',
            'bersedia_wfh' => 1,
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $profile = data_get($user->apply_configuration, 'auto_answer.profile');

        $this->assertTrue($profile['bersedia_wfh']);
        foreach (['bersedia_wfo', 'bersedia_hybrid', 'bersedia_luar_kota', 'bersedia_industri_banking'] as $k) {
            $this->assertIsBool($profile[$k], "$k must be bool");
            $this->assertFalse($profile[$k], "$k must default to false");
        }
    }

    public function test_numeric_fields_are_cast_to_int_and_float()
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'gaji_terakhir' => '3000000',
            'ekspektasi_gaji' => '5500000',
            'tahun_lulus' => '2024',
            'ipk' => '3.5',
            'pengalaman_tahun' => '2.5',
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $profile = data_get($user->apply_configuration, 'auto_answer.profile');

        $this->assertSame(3000000, $profile['gaji_terakhir']);
        $this->assertSame(5500000, $profile['ekspektasi_gaji']);
        $this->assertSame(2024, $profile['tahun_lulus']);
        $this->assertSame(3.5, $profile['ipk']);
        $this->assertSame(2.5, $profile['pengalaman_tahun']);
    }

    public function test_saving_ai_profile_preserves_other_apply_configuration_keys()
    {
        $user = $this->makeUser([
            'apply_configuration' => [
                'career_ai' => ['api_key' => 'sk-or-keep-me', 'model' => 'x/y'],
                'auto_answer' => ['enabled' => true],
                'glints' => ['keyword' => 'network'],
            ],
        ]);

        $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'nama' => 'TEST_Preserve',
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $cfg = $user->apply_configuration;

        $this->assertSame('sk-or-keep-me', data_get($cfg, 'career_ai.api_key'));
        $this->assertTrue(data_get($cfg, 'auto_answer.enabled'));
        $this->assertSame('network', data_get($cfg, 'glints.keyword'));
        $this->assertSame('TEST_Preserve', data_get($cfg, 'auto_answer.profile.nama'));
    }

    public function test_saved_profile_is_prefilled_on_the_form()
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'nama' => 'TEST_Prefill Candidate',
            'ekspektasi_gaji' => 7250000,
            'notice_period' => 'TWO_WEEKS',
            'skills' => [['name' => 'Mikrotik', 'level' => 'ADVANCED']],
        ])->assertSessionHasNoErrors();

        $response = $this->actingAs($user->fresh())->get(route('settings', ['tab' => 'ai-profile']));
        $response->assertOk();
        $response->assertSee('TEST_Prefill Candidate', false);
        $response->assertSee('7250000', false);
        $response->assertSee('Mikrotik', false);
    }

    public function test_second_save_overwrites_previous_profile()
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('settings.ai-profile.save'), ['nama' => 'TEST_First']);
        $this->actingAs($user->fresh())->post(route('settings.ai-profile.save'), ['nama' => 'TEST_Second']);

        $user->refresh();
        $this->assertSame('TEST_Second', data_get($user->apply_configuration, 'auto_answer.profile.nama'));
    }

    // ---------------------------------------------------------------- PROFILE BUILDER

    public function test_profile_builder_falls_back_to_platform_when_manual_field_empty()
    {
        $user = $this->makeUser([
            'apply_configuration' => [
                'auto_answer' => ['profile' => ['nama' => '', 'lokasi' => null, 'phone' => '']],
            ],
        ]);

        $profile = ProfileBuilder::build('glints', [
            'first_name' => 'Platform',
            'last_name' => 'Candidate',
            'phone' => '0812999',
            'highestEducation' => 'BACHELOR_DEGREE',
            'preferredLocations' => [['formattedName' => 'Jakarta, Indonesia']],
        ], $user);

        $this->assertSame('Platform Candidate', $profile['nama']);
        $this->assertSame('0812999', $profile['phone']);
        $this->assertSame('Jakarta, Indonesia', $profile['lokasi']);
        $this->assertSame('S1', $profile['pendidikan']);
    }

    public function test_profile_builder_maps_work_preferences_into_readable_array()
    {
        $user = $this->makeUser([
            'apply_configuration' => [
                'auto_answer' => ['profile' => [
                    'bersedia_wfo' => true,
                    'bersedia_hybrid' => true,
                    'bersedia_wfh' => false,
                    'bersedia_luar_kota' => true,
                ]],
            ],
        ]);

        $profile = ProfileBuilder::build('glints', [], $user);

        $this->assertSame(['WFO', 'Hybrid'], $profile['preferensi_lokasi_kerja']);
        $this->assertTrue($profile['bersedia_luar_kota']);
        $this->assertFalse($profile['bersedia_industri_banking']);
    }

    public function test_profile_builder_drops_blank_dynamic_rows()
    {
        $user = $this->makeUser([
            'apply_configuration' => [
                'auto_answer' => ['profile' => [
                    'skills' => [['name' => 'Linux', 'level' => 'ADVANCED'], ['name' => '', 'level' => '']],
                    'bahasa' => [['nama' => '', 'level' => '']],
                    'sertifikasi' => 'not-an-array',
                ]],
            ],
        ]);

        $profile = ProfileBuilder::build('glints', [], $user);

        $this->assertCount(1, $profile['skills']);
        $this->assertSame([], $profile['bahasa']);
        $this->assertSame([], $profile['sertifikasi']);
    }

    public function test_profile_builder_handles_null_user_and_jobstreet_provider()
    {
        $profile = ProfileBuilder::build('jobstreet', [
            'latest_roles' => [
                'title' => ['text' => 'Network Engineer'],
                'company' => ['text' => 'PT Contoh'],
            ],
        ], null);

        $this->assertSame('Network Engineer', $profile['posisi_terakhir']);
        $this->assertSame('PT Contoh', $profile['perusahaan_terakhir']);
        $this->assertNull($profile['nama']);
    }

    public function test_profile_builder_tolerates_non_array_manual_profile()
    {
        $user = $this->makeUser([
            'apply_configuration' => ['auto_answer' => ['profile' => 'corrupted']],
        ]);

        $profile = ProfileBuilder::build('glints', [], $user);

        $this->assertSame($user->name, $profile['nama']);
        $this->assertSame([], $profile['skills']);
    }

    public function test_completeness_is_monotonic_and_bounded()
    {
        $partial = ['nama' => 'A', 'phone' => 'B'];
        $more = $partial + ['pendidikan' => 'S1', 'ekspektasi_gaji' => 5000000, 'notice_period' => 'IMMEDIATELY'];

        $low = ProfileBuilder::completeness($partial);
        $high = ProfileBuilder::completeness($more);

        $this->assertGreaterThan($low, $high);
        $this->assertGreaterThanOrEqual(0, $low);
        $this->assertLessThanOrEqual(100, ProfileBuilder::completeness($more));
    }

    public function test_completeness_link_reflects_saved_profile_on_panel_configuration()
    {
        $user = $this->makeUser();

        $component = \Livewire\Livewire::actingAs($user)
            ->test('panel-configuration')
            ->call('init');

        $component->assertSee('data-testid="auto-answer-profile-link"', false);
        $component->assertSee('Profil kandidat: 0% lengkap', false);

        $this->actingAs($user)->post(route('settings.ai-profile.save'), [
            'nama' => 'TEST_Complete',
            'phone' => '0812',
            'lokasi' => 'Jakarta',
            'pendidikan' => 'S1',
            'jurusan' => 'IT',
            'pengalaman_level' => 'FRESH_GRADUATE',
            'posisi_terakhir' => 'Intern',
            'perusahaan_terakhir' => 'PT X',
            'gaji_terakhir' => 1000000,
            'ekspektasi_gaji' => 5000000,
            'notice_period' => 'IMMEDIATELY',
            'kewarganegaraan' => 'Indonesia',
            'catatan' => 'note',
            'skills' => [['name' => 'A', 'level' => 'BASIC']],
            'sertifikasi' => [['nama' => 'B', 'issuer' => 'C', 'tahun' => 2024]],
            'bahasa' => [['nama' => 'EN', 'level' => 'FLUENT']],
            'bersedia_wfo' => 1,
        ])->assertSessionHasNoErrors();

        $html = \Livewire\Livewire::actingAs($user->fresh())
            ->test('panel-configuration')
            ->call('init')
            ->html();

        preg_match('/Profil kandidat: (\\d+)% lengkap/', $html, $m);
        $this->assertNotEmpty($m, 'Completeness badge should be rendered');
        $this->assertGreaterThanOrEqual(90, (int) $m[1]);
    }
}
