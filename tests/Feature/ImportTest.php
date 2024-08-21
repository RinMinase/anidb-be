<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\BaseTestCase;

use App\Models\Bucket;
use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Group;
use App\Models\PCSetup;
use App\Models\Sequence;

class ImportTest extends BaseTestCase {

  // Backup related variables
  private $bucket_backup = null;
  private $pc_setup_backup = null;
  private $group_backup = null;
  private $sequence_backup = null;

  private $entry_backup = null;
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;

  // Class variables

  // Backup related tables
  private function setup_backup() {
    $this->bucket_backup = Bucket::all()->toArray();
    $this->pc_setup_backup = PCSetup::all()->toArray();

    $hidden_columns = ['id', 'created_at', 'updated_at'];
    $this->group_backup = Group::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['created_at', 'updated_at'];
    $this->sequence_backup = Sequence::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_entries'];
    $this->entry_rewatch_backup = EntryRewatch::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_entries', 'created_at', 'updated_at', 'deleted_at'];
    $this->entry_rating_backup = EntryRating::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id_entries', 'created_at', 'updated_at', 'deleted_at'];
    $this->entry_offquel_backup = EntryOffquel::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_quality', 'updated_at', 'deleted_at'];
    $this->entry_backup = Entry::all()->makeVisible($hidden_columns)->toArray();
  }

  // Restore related tables
  private function setup_restore() {
    Bucket::truncate();
    Bucket::insert($this->bucket_backup);
    Bucket::refreshAutoIncrements();

    PCSetup::truncate();
    PCSetup::insert($this->pc_setup_backup);
    PCSetup::refreshAutoIncrements();

    Group::truncate();
    Group::insert($this->group_backup);
    Group::refreshAutoIncrements();

    Sequence::truncate();
    Sequence::insert($this->sequence_backup);
    Sequence::refreshAutoIncrements();

    Entry::truncate(); // cascade deletes

    Entry::insert($this->entry_backup);
    EntryOffquel::insert($this->entry_offquel_backup);
    EntryRating::insert($this->entry_rating_backup);
    EntryRewatch::insert($this->entry_rewatch_backup);

    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();
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

  // Buckets
  public function test_should_import_buckets() {
    Bucket::truncate();
    Bucket::refreshAutoIncrements();

    $content = [
      [
        'from' => 'a',
        'size' => 2000339066880,
        'to' => 'm',
      ],
      [
        'from' => 'n',
        'size' => 2000339066880,
        'to' => 'z',
      ],
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/buckets/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => count($content),
          'totalJsonEntries' => count($content),
        ]
      ]);

    $actual = Bucket::select('from', 'size', 'to')->get()->toArray();

    $this->assertEquals($content, $actual);
  }

  public function test_should_not_import_buckets_when_no_file_is_attached() {
    $response = $this->withoutMiddleware()->post('/api/buckets/import/');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);

    $response = $this->withoutMiddleware()->post('/api/buckets/import/', ['file' => null]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_buckets_when_file_type_is_invalid() {
    $file = UploadedFile::fake()->create('test_file.txt');

    $response = $this->withoutMiddleware()->post('/api/buckets/import/', ['file' => $file]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_buckets_when_json_content_is_invalid() {
    $file_1 = UploadedFile::fake()->createWithContent('test_file.json', '{malformedjson}');

    $response = $this->withoutMiddleware()->post('/api/buckets/import/', ['file' => $file_1]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);

    $file_2 = UploadedFile::fake()->createWithContent('test_file.json', 'invalid json');

    $response = $this->withoutMiddleware()->post('/api/buckets/import/', ['file' => $file_2]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);
  }

  // Groups
  public function test_should_import_groups() {
    Group::truncate();
    Group::refreshAutoIncrements();

    $content = [
      'Test Group 1',
      'Test Group 2',
      'Test Group 3',
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/groups/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => count($content),
          'totalJsonEntries' => count($content),
        ]
      ]);

    $actual = Group::orderBy('name')->pluck('name')->toArray();

    $this->assertEqualsCanonicalizing($content, $actual);
  }

  public function test_should_not_import_groups_when_no_file_is_attached() {
    $response = $this->withoutMiddleware()->post('/api/groups/import/');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);

    $response = $this->withoutMiddleware()->post('/api/groups/import/', ['file' => null]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_groups_when_file_type_is_invalid() {
    $file = UploadedFile::fake()->create('test_file.txt');

    $response = $this->withoutMiddleware()->post('/api/groups/import/', ['file' => $file]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_groups_when_json_content_is_invalid() {
    $file_1 = UploadedFile::fake()->createWithContent('test_file.json', '{malformedjson}');

    $response = $this->withoutMiddleware()->post('/api/groups/import/', ['file' => $file_1]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);

    $file_2 = UploadedFile::fake()->createWithContent('test_file.json', 'invalid json');

    $response = $this->withoutMiddleware()->post('/api/groups/import/', ['file' => $file_2]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);
  }

  // PC Setups
  public function test_should_import_pc_setups() {
    PCSetup::truncate();
    PCSetup::refreshAutoIncrements();

    $content = [
      [
        'label' => 'test label',

        'is_current' => true,
        'is_future' => false,
        'is_server' => false,

        'cpu' => 'test cpu',
        'cpu_price' => 100,
        'cpu_sub' => 'cpu subtext',
        'cpu_sub2' => 'cpu subtext 2',

        'ram' => 'test ram',
        'ram_price' => 200,
        'ram_sub' => 'ram subtext',

        'gpu' => 'test gpu',
        'gpu_price' => 300,
        'gpu_sub' => 'gpu subtext',

        'motherboard' => 'test motherboard',
        'motherboard_price' => 400,

        'psu' => 'test psu',
        'psu_price' => 500,

        'cooler' => 'test cooler',
        'cooler_price' => 600,
        'cooler_acc' => 'cooler accessory',
        'cooler_acc_price' => 700,

        'ssd_1' => 'ssd 1',
        'ssd_1_price' => 100,
        'ssd_2' => 'ssd 2',
        'ssd_2_price' => 200,
        'ssd_3' => 'ssd 3',
        'ssd_3_price' => 300,
        'ssd_4' => 'ssd 4',
        'ssd_4_price' => 400,

        'hdd_1' => 'hdd 1',
        'hdd_1_price' => 500,
        'hdd_2' => 'hdd 2',
        'hdd_2_price' => 600,
        'hdd_3' => 'hdd 3',
        'hdd_3_price' => 700,
        'hdd_4' => 'hdd 4',
        'hdd_4_price' => 800,

        'case' => 'test case',
        'case_price' => 100,
        'case_fans_1' => 'case fans 1',
        'case_fans_1_price' => 200,
        'case_fans_2' => 'case fans 2',
        'case_fans_2_price' => 300,
        'case_fans_3' => 'case fans 3',
        'case_fans_3_price' => 400,
        'case_fans_4' => 'case fans 4',
        'case_fans_4_price' => 500,

        'monitor' => 'test monitor',
        'monitor_price' => 100,
        'monitor_sub' => 'monitor subtext',
        'monitor_acc_1' => 'monitor accessory 1',
        'monitor_acc_1_price' => 200,
        'monitor_acc_2' => 'monitor accessory 2',
        'monitor_acc_2_price' => 300,

        'keyboard' => 'test keyboard',
        'keyboard_price' => 100,
        'keyboard_sub' => 'keyboard subtext 1',
        'keyboard_sub2' => 'keyboard subtext 2',
        'keyboard_acc_1' => 'keyboard accessory 1',
        'keyboard_acc_1_price' => 200,
        'keyboard_acc_2' => 'keyboard accessory 2',
        'keyboard_acc_2_price' => 300,

        'mouse' => 'test mouse',
        'mouse_price' => 100,

        'speakers' => 'test speakers',
        'speakers_price' => 200,

        'wifi' => 'test wifi',
        'wifi_price' => 300,

        'headset_1' => 'test headset 1',
        'headset_1_price' => 400,
        'headset_2' => 'test headset 2',
        'headset_2_price' => 500,

        'mic' => 'test mic',
        'mic_price' => 600,
        'mic_acc' => 'mic accessory',
        'mic_acc_price' => 700,

        'audio_interface' => 'test interface',
        'audio_interface_price' => 100,
        'equalizer' => 'test eq',
        'equalizer_price' => 200,
        'amplifier' => 'test amp',
        'amplifier_price' => 300,
      ],
      [
        'label' => 'test label 2',

        'is_current' => false,
        'is_future' => true,
        'is_server' => false,

        'cpu' => 'test cpu 2',
      ],
      [
        'label' => 'test server 1',

        'is_current' => false,
        'is_future' => true,
        'is_server' => true,

        'cpu' => 'test server cpu',
      ],
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/pc-setups/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => count($content),
          'totalJsonEntries' => count($content),
        ]
      ]);

    $actual = PCSetup::all()->toArray();

    foreach ($content as $index => $value) {
      $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
        $value,
        $actual[$index],
        array_keys($value),
      );
    }
  }

  public function test_should_not_import_pc_setups_when_no_file_is_attached() {
    $response = $this->withoutMiddleware()->post('/api/pc-setups/import/');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);

    $response = $this->withoutMiddleware()->post('/api/pc-setups/import/', ['file' => null]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_pc_setups_when_file_type_is_invalid() {
    $file = UploadedFile::fake()->create('test_file.txt');

    $response = $this->withoutMiddleware()->post('/api/pc-setups/import/', ['file' => $file]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_pc_setups_when_json_content_is_invalid() {
    $file_1 = UploadedFile::fake()->createWithContent('test_file.json', '{malformedjson}');

    $response = $this->withoutMiddleware()->post('/api/pc-setups/import/', ['file' => $file_1]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);

    $file_2 = UploadedFile::fake()->createWithContent('test_file.json', 'invalid json');

    $response = $this->withoutMiddleware()->post('/api/pc-setups/import/', ['file' => $file_2]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);
  }

  // Sequences
  public function test_should_import_sequences() {
    Sequence::truncate();
    Sequence::refreshAutoIncrements();

    $content = [
      [
        'date_from' => 1370736000,
        'date_to' => 1364083200,
        'title' => 'Summer 2013',
      ],
      [
        'date_from' => 1402790400,
        'date_to' => 1396310400,
        'title' => 'Summer 2014',
      ],
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/sequences/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => count($content),
          'totalJsonEntries' => count($content),
        ]
      ]);

    $actual = Sequence::select('date_from', 'date_to', 'title')->get()->toArray();

    $expected = [
      [
        'date_from' => '2013-06-09',
        'date_to' => '2013-03-24',
        'title' => 'Summer 2013',
      ],
      [
        'date_from' => '2014-06-15',
        'date_to' => '2014-04-01',
        'title' => 'Summer 2014',
      ]
    ];

    $this->assertEquals($expected, $actual);
  }

  public function test_should_not_import_sequences_when_no_file_is_attached() {
    $response = $this->withoutMiddleware()->post('/api/sequences/import/');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);

    $response = $this->withoutMiddleware()->post('/api/sequences/import/', ['file' => null]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_sequences_when_file_type_is_invalid() {
    $file = UploadedFile::fake()->create('test_file.txt');

    $response = $this->withoutMiddleware()->post('/api/sequences/import/', ['file' => $file]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_sequences_when_json_content_is_invalid() {
    $file_1 = UploadedFile::fake()->createWithContent('test_file.json', '{malformedjson}');

    $response = $this->withoutMiddleware()->post('/api/sequences/import/', ['file' => $file_1]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);

    $file_2 = UploadedFile::fake()->createWithContent('test_file.json', 'invalid json');

    $response = $this->withoutMiddleware()->post('/api/sequences/import/', ['file' => $file_2]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);
  }

  // Entries
  public function test_should_import_entries() {
    Entry::truncate();
    Entry::refreshAutoIncrements();

    $content = [
      [
        'dateFinished' => 1396454400,
        'downloadPriority' => -1,
        'duration' => 17096,
        'encoder' => 'sample encoder',
        'episodes' => 12,
        'filesize' => 12780605656,
        'firstSeasonTitle' => 'sample title',
        'inhdd' => 1,
        'offquel' => '',
        'ovas' => 123,
        'prequel' => '',
        'quality' => 'FHD 1080p',
        'releaseSeason' => 'Fall',
        'releaseYear' => '2020',
        'remarks' => 'remarks',
        'seasonNumber' => 1,
        'sequel' => 'sample sequel',
        'specials' => 234,
        'title' => 'sample title',
        'variants' => 'variants',
        'watchStatus' => 0
      ],
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => count($content),
          'totalJsonEntries' => count($content),
        ]
      ]);

    $actual = Entry::all()->toArray();

    $this->assertCount(1, $actual);

    $expected = [
      'title' => $content[0]['title'],
      'duration' => $content[0]['duration'],
      'episodes' => $content[0]['episodes'],
      'ovas' => $content[0]['ovas'],
      'specials' => $content[0]['specials'],
      'variants' => $content[0]['variants'],
      'remarks' => $content[0]['remarks'],
      'release_year' => $content[0]['releaseYear'],
      'release_season' => $content[0]['releaseSeason'],

      'season_number' => $content[0]['seasonNumber'],
      'encoder_video' => $content[0]['encoder'],

      'date_finished' => '2014-04-03'
    ];

    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
      $expected,
      $actual[0],
      array_keys($expected),
    );
  }

  public function test_should_import_entries_with_correct_priorities() {
    Entry::truncate();
    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();

    $content = [
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title',
      ],
      [
        'downloadPriority' => 0,
        'title' => 'should not be included'
      ],
      [
        'downloadPriority' => 1,
        'title' => 'should not be included 2'
      ],
      [
        'downloadPriority' => 1,
        'title' => 'should not be included 3'
      ],
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => 1,
          'totalJsonEntries' => count($content),
        ]
      ]);

    $actual = Entry::select('title')->pluck('title')->toArray();

    $this->assertCount(1, $actual);
    $this->assertEquals($content[0]['title'], $actual[0]);
  }

  public function test_should_import_entries_with_correct_connections() {
    Entry::truncate();
    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();

    $content = [
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title',
        'seasonNumber' => 1,
        'firstSeasonTitle' => 'sample title',
        'prequel' => null,
        'sequel' => 'sample title 2',
        'offquel' => 'sample title offquel 1, sample title offquel 2'
      ],
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title 2',
        'seasonNumber' => 2,
        'firstSeasonTitle' => 'sample title',
        'prequel' => 'sample title',
        'sequel' => 'sample title 3',
      ],
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title 3',
        'seasonNumber' => 3,
        'firstSeasonTitle' => 'sample title',
        'prequel' => 'sample title 2',
        'sequel' => null,
      ],
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title offquel 1',
        'seasonNumber' => 0,
        'firstSeasonTitle' => 'sample title',
        'prequel' => 'sample title 1',
        'sequel' => null,
      ],
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title offquel 2',
        'seasonNumber' => 0,
        'firstSeasonTitle' => 'sample title',
        'prequel' => 'sample title 1',
        'sequel' => null,
      ],
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => count($content),
          'totalJsonEntries' => count($content),
        ]
      ]);

    // returns by order :: 1 -> 2 -> 3 -> O1 -> O2
    $actual = Entry::select()
      ->orderBy('title')
      ->get()
      ->makeVisible('id')
      ->toArray();

    $actual_titles = collect($actual)->pluck('title');
    $expected = collect($content)->pluck('title');

    $this->assertEquals($expected, $actual_titles);

    $expected_first_season_id = Entry::where('title', $content[0]['title'])->first()->id;

    $this->assertCount(count($content), $actual);

    // First Entry
    $this->assertEquals($expected_first_season_id, $actual[0]['season_first_title_id']);
    $this->assertNull($actual[0]['prequel_id']);
    $this->assertEquals($actual[1]['id'], $actual[0]['sequel_id']);

    // Second Entry
    $this->assertEquals($expected_first_season_id, $actual[1]['season_first_title_id']);
    $this->assertEquals($actual[0]['id'], $actual[1]['prequel_id']);
    $this->assertEquals($actual[2]['id'], $actual[1]['sequel_id']);

    // Third Entry
    $this->assertEquals($expected_first_season_id, $actual[2]['season_first_title_id']);
    $this->assertEquals($actual[1]['id'], $actual[2]['prequel_id']);
    $this->assertNull($actual[2]['sequel_id']);

    // Offquel 1
    $this->assertEquals($expected_first_season_id, $actual[3]['season_first_title_id']);
    $this->assertNull($actual[3]['prequel_id']);
    $this->assertNull($actual[3]['sequel_id']);

    $actual_offquel_1 = EntryOffquel::where('id_entries', $expected_first_season_id)
      ->where('id_entries_offquel', $actual[3]['id'])
      ->first();

    $this->assertNotNull($actual_offquel_1);
    $this->assertModelExists($actual_offquel_1);

    // Offquel 2
    $this->assertEquals($expected_first_season_id, $actual[4]['season_first_title_id']);
    $this->assertNull($actual[4]['prequel_id']);
    $this->assertNull($actual[4]['sequel_id']);

    $actual_offquel_2 = EntryOffquel::where('id_entries', $expected_first_season_id)
      ->where('id_entries_offquel', $actual[4]['id'])
      ->first();

    $this->assertNotNull($actual_offquel_2);
    $this->assertModelExists($actual_offquel_2);
  }

  public function test_should_import_entries_with_ratings() {
    Entry::truncate();
    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();

    $content = [
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title',
        'rating' => [
          'audio' => 2,
          'enjoyment' => 3,
          'graphics' => 5,
          'plot' => 7,
        ]
      ],
      [
        'downloadPriority' => -1,
        'quality' => 'FHD 1080p',
        'title' => 'sample title 2',
        'rating' => [
          'audio' => 1,
          'enjoyment' => 2,
          'graphics' => 6,
          'plot' => 5,
        ]
      ],
    ];

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'acceptedImports' => count($content),
          'totalJsonEntries' => count($content),
        ]
      ]);

    $entry_id_1 = Entry::where('title', $content[0]['title'])->first()->id;
    $actual_1 = EntryRating::select('audio', 'enjoyment', 'graphics', 'plot')
      ->where('id_entries', $entry_id_1)
      ->first()
      ->toArray();

    $this->assertNotNull($actual_1);
    $this->assertEqualsCanonicalizing($content[0]['rating'], $actual_1);

    $entry_id_2 = Entry::where('title', $content[1]['title'])->first()->id;
    $actual_2 = EntryRating::select('audio', 'enjoyment', 'graphics', 'plot')
      ->where('id_entries', $entry_id_2)
      ->first()
      ->toArray();

    $this->assertNotNull($actual_2);
    $this->assertEqualsCanonicalizing($content[1]['rating'], $actual_2);
  }

  public function test_should_not_import_entries_when_no_file_is_attached() {
    $response = $this->withoutMiddleware()->post('/api/entries/import/');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => null]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_entries_when_file_type_is_invalid() {
    $file = UploadedFile::fake()->create('test_file.txt');

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => $file]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_entries_when_json_content_is_invalid() {
    $file_1 = UploadedFile::fake()->createWithContent('test_file.json', '{malformedjson}');

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => $file_1]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);

    $file_2 = UploadedFile::fake()->createWithContent('test_file.json', 'invalid json');

    $response = $this->withoutMiddleware()->post('/api/entries/import/', ['file' => $file_2]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);
  }

  // Combined / General Import
  public function test_should_import_data() {
    Bucket::truncate();
    Bucket::refreshAutoIncrements();

    Group::truncate();
    Group::refreshAutoIncrements();

    Sequence::truncate();
    Sequence::refreshAutoIncrements();

    Entry::truncate(); // cascade deletes

    Entry::refreshAutoIncrements();
    EntryOffquel::refreshAutoIncrements();
    EntryRating::refreshAutoIncrements();
    EntryRewatch::refreshAutoIncrements();

    $content = [
      'entry' => [
        [
          'downloadPriority' => -1,
          'quality' => 'FHD 1080p',
          'title' => 'sample title',
        ],
      ],
      'bucket' => [
        [
          'from' => 'a',
          'size' => 2000339066880,
          'to' => 'm',
        ],
        [
          'from' => 'n',
          'size' => 2000339066880,
          'to' => 'z',
        ],
      ],
      'sequence' => [
        [
          'date_from' => 1370736000,
          'date_to' => 1364083200,
          'title' => 'Summer 2013',
        ],
      ],
      'group' => [
        'Test Group 1',
        'Test Group 2',
        'Test Group 3',
      ],
      'not_included' => [
        'stuff' => 123
      ],

    ];

    $expected_count_entry = count($content['entry']);
    $expected_count_bucket = count($content['bucket']);
    $expected_count_sequence = count($content['sequence']);
    $expected_count_group = count($content['group']);

    $file = UploadedFile::fake()->createWithContent('test_file.json', json_encode($content));

    $response = $this->withoutMiddleware()->post('/api/import/', ['file' => $file]);

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          'entries' => [
            'accepted' => $expected_count_entry,
            'total' => $expected_count_entry,
          ],
          'buckets' => [
            'accepted' => $expected_count_bucket,
            'total' => $expected_count_bucket,
          ],
          'sequences' => [
            'accepted' => $expected_count_sequence,
            'total' => $expected_count_sequence,
          ],
          'groups' => [
            'accepted' => $expected_count_group,
            'total' => $expected_count_group,
          ],
        ]
      ]);

    $actual_entries = Entry::count();
    $this->assertEquals($expected_count_entry, $actual_entries);

    $actual_buckets = Bucket::count();
    $this->assertEquals($expected_count_bucket, $actual_buckets);

    $actual_sequences = Sequence::count();
    $this->assertEquals($expected_count_sequence, $actual_sequences);

    $actual_groups = Group::count();
    $this->assertEquals($expected_count_group, $actual_groups);
  }

  public function test_should_not_import_data_when_no_file_is_attached() {
    $response = $this->withoutMiddleware()->post('/api/import/');

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);

    $response = $this->withoutMiddleware()->post('/api/import/', ['file' => null]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_data_when_file_type_is_invalid() {
    $file = UploadedFile::fake()->create('test_file.txt');

    $response = $this->withoutMiddleware()->post('/api/import/', ['file' => $file]);

    $response->assertStatus(401)
      ->assertJsonStructure(['data' => ['file']]);
  }

  public function test_should_not_import_data_when_json_content_is_invalid() {
    $file_1 = UploadedFile::fake()->createWithContent('test_file.json', '{malformedjson}');

    $response = $this->withoutMiddleware()->post('/api/import/', ['file' => $file_1]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);

    $file_2 = UploadedFile::fake()->createWithContent('test_file.json', 'invalid json');

    $response = $this->withoutMiddleware()->post('/api/import/', ['file' => $file_2]);

    $response->assertStatus(400)
      ->assertJson(['message' => 'The file is an invalid JSON']);
  }
}
