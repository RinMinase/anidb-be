<?php

namespace Tests\Feature;

use Tests\BaseTestCase;

use Carbon\Carbon;

use App\Models\AppSetting;

class AppSettingTest extends BaseTestCase {

  // Backup related variables
  private $setting_backup = null;

  // Class variables
  private $setting_id_1 = 99999;
  private $setting_id_2 = 99998;
  private $setting_id_3 = 99997;

  private $setting_key_1 = 'first_key';
  private $setting_key_2 = 'second_key';
  private $setting_key_3 = 'third_key';

  private $setting_value_1 = 'first_value';
  private $setting_value_2 = 'second_value';
  private $setting_value_3 = 'third_value';

  // Backup related tables
  private function setup_backup() {
    $this->setting_backup = AppSetting::all()->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    AppSetting::truncate();
    AppSetting::insert($this->setting_backup);
    AppSetting::refreshAutoIncrements();
  }

  // Setup data for testing
  private function setup_config() {
    AppSetting::truncate();

    $timestamp = Carbon::now();

    $test_settings = [
      [
        'id' => $this->setting_id_1,
        'key' => $this->setting_key_1,
        'value' => $this->setting_value_1,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ],
      [
        'id' => $this->setting_id_2,
        'key' => $this->setting_key_2,
        'value' => $this->setting_value_2,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ],
      [
        'id' => $this->setting_id_3,
        'key' => $this->setting_key_3,
        'value' => $this->setting_value_3,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
      ],
    ];

    AppSetting::insert($test_settings);
  }

  // Fixtures
  public function setUp(): void {
    parent::setUp();
    $this->setup_backup();
  }

  public function tearDown(): void {
    $this->setup_restore();
    parent::tearDown();
  }

  // Test cases
  public function test_should_get_settings_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/app-settings');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'key',
          'value',
          'createdAt',
          'updatedAt',
        ]],
      ]);

    $expected = [
      [
        'id' => $this->setting_id_1,
        'key' => $this->setting_key_1,
        'value' => $this->setting_value_1,
      ],
      [
        'id' => $this->setting_id_2,
        'key' => $this->setting_key_2,
        'value' => $this->setting_value_2,
      ],
      [
        'id' => $this->setting_id_3,
        'key' => $this->setting_key_3,
        'value' => $this->setting_value_3,
      ],
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'],
      ['id', 'key', 'value']
    );
  }

  public function test_should_get_single_setting_successfully() {
    $this->setup_config();

    $response = $this->withoutMiddleware()->get('/api/app-settings/' . $this->setting_id_1);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'key',
          'value',
          'createdAt',
          'updatedAt',
        ],
      ]);

    $expected = [
      'id' => $this->setting_id_1,
      'key' => $this->setting_key_1,
      'value' => $this->setting_value_1,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'],
      ['id', 'key', 'value']
    );

    $response = $this->withoutMiddleware()->get('/api/app-settings/' . $this->setting_id_2);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'key',
          'value',
          'createdAt',
          'updatedAt',
        ],
      ]);

    $expected = [
      'id' => $this->setting_id_2,
      'key' => $this->setting_key_2,
      'value' => $this->setting_value_2,
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $response['data'],
      ['id', 'key', 'value']
    );
  }

  public function test_should_add_setting_successfully() {
  }

  public function test_should_not_add_setting_on_form_errors() {
  }

  public function test_should_edit_setting_successfully() {
  }

  public function test_should_not_edit_setting_on_form_errors() {
  }

  public function test_should_not_edit_non_existent_setting() {
  }

  public function test_should_delete_setting_successfully() {
  }

  public function test_should_not_delete_non_existent_setting() {
  }
}
