<?php

namespace Tests\Feature;

use Error;
use Exception;
use Carbon\Carbon;
use Cloudinary\Api\Admin\AdminApi;
use Tests\BaseTestCase;

use App\Enums\EntrySearchHasEnum;
use App\Exceptions\Entry\SearchFilterParsingException;

use App\Models\Entry;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\Quality;

use App\Repositories\EntrySearchRepository;

class EntrySearchTest extends BaseTestCase {

  // Backup related variables
  private $entry_rewatch_backup = null;
  private $entry_rating_backup = null;
  private $entry_offquel_backup = null;
  private $entry_backup = null;

  // Class variables
  private $total_entry_count = 5;

  private $entry_id_1 = 99999;
  private $entry_id_2 = 99998;
  private $entry_id_3 = 99997;
  private $entry_id_4 = 99996;
  private $entry_id_5 = 99995;

  private $entry_uuid_1 = 'b354c456-fb16-4809-b4bb-e55f8c9ec900';
  private $entry_uuid_2 = 'a787f460-bc60-44cf-9224-3901fb5b08ca';
  private $entry_uuid_3 = '959d90bd-f1ed-4078-b374-4fd4dfedfbb6';
  private $entry_uuid_4 = '64b3e54c-8280-4275-b5c2-5361065a5bf9';
  private $entry_uuid_5 = 'ddd65078-5d05-48a3-9604-a2ed9f4a679e';

  private $entry_title_1 = 'testing series title season 1';

  private $entry_1_image = '__test_data__8fa9b149-0185-41b2-b6c2-7d2ac7512eb4';
  // cached static value throughout the whole test, make single call only to API
  private static $entry_1_image_url = null;

  private $entry_1_rating_audio = 6;
  private $entry_1_rating_enjoyment = 5;
  private $entry_1_rating_graphics = 4;
  private $entry_1_rating_plot = 3;

  private $entry_1_rewatch_id = 99999;
  private $entry_1_rewatch_uuid = 'e16593ad-ed01-4314-b4b1-0120ba734f90';

  // Backup related tables
  private function setup_backup() {
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

  // Setup data for testing
  private function setup_config() {
    Entry::truncate();

    $id_quality = Quality::where('quality', 'FHD 1080p')->first()->id;

    $date_finished_1 = Carbon::parse('2001-01-01')->format('Y-m-d');
    $date_finished_2 = Carbon::parse('2001-01-02')->format('Y-m-d');
    $date_finished_3 = Carbon::parse('2001-01-03')->format('Y-m-d');
    $date_finished_4 = Carbon::parse('2001-01-04')->format('Y-m-d');
    $date_finished_5 = Carbon::parse('2001-01-05')->format('Y-m-d');

    $date_finished_rewatch = Carbon::parse('2001-02-01')->format('Y-m-d');

    if (self::$entry_1_image_url === null) {
      echo PHP_EOL . 'INFO: API call to Cloudinary:AdminAPI:asset' . PHP_EOL;

      $image_url = (new AdminApi())->asset($this->entry_1_image)['url'];

      if (!$image_url) {
        throw new Error('Image URL was not acquired');
      }

      self::$entry_1_image_url = $image_url;
    }

    $test_entries = [
      [
        'id' => $this->entry_id_1,
        'uuid' => $this->entry_uuid_1,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_1,
        'title' => $this->entry_title_1,
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => $this->entry_id_4,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => self::$entry_1_image_url,
      ],
      [
        'id' => $this->entry_id_2,
        'uuid' => $this->entry_uuid_2,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_2,
        'title' => 'testing another solo title',
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_3,
        'uuid' => $this->entry_uuid_3,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_3,
        'title' => 'test offquel',
        'season_number' => 1,
        'prequel_id' => null,
        'sequel_id' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_4,
        'uuid' => $this->entry_uuid_4,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_4,
        'title' => 'testing series title season 2',
        'season_number' => 2,
        'prequel_id' => $this->entry_id_1,
        'sequel_id' => $this->entry_id_5,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
      [
        'id' => $this->entry_id_5,
        'uuid' => $this->entry_uuid_5,
        'id_quality' => $id_quality,
        'date_finished' => $date_finished_5,
        'title' => 'testing series title season 3',
        'season_number' => 3,
        'prequel_id' => $this->entry_id_4,
        'sequel_id' => null,
        'created_at' => '2020-01-01 13:00:00',
        'updated_at' => '2020-01-01 13:00:00',
        'image' => null,
      ],
    ];

    $test_entry_offquel = [
      'id_entries' => $this->entry_id_1,          // parent entry
      'id_entries_offquel' => $this->entry_id_3,  // offquel entry
    ];

    $test_entry_rating = [
      'id_entries' => $this->entry_id_1,
      'audio' => $this->entry_1_rating_audio,
      'enjoyment' => $this->entry_1_rating_enjoyment,
      'graphics' => $this->entry_1_rating_graphics,
      'plot' => $this->entry_1_rating_plot,
      'created_at' => '2020-01-01 13:00:00',
      'updated_at' => '2020-01-01 13:00:00',
    ];

    $test_entry_rewatch = [
      'id' => $this->entry_1_rewatch_id,
      'id_entries' => $this->entry_id_1,
      'uuid' => $this->entry_1_rewatch_uuid,
      'date_rewatched' => $date_finished_rewatch,
    ];

    Entry::insert($test_entries);
    EntryOffquel::insert($test_entry_offquel);
    EntryRating::insert($test_entry_rating);
    EntryRewatch::insert($test_entry_rewatch);
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

  // Test Cases
  public function test_should_deep_search_all_data() {
  }

  public function test_should_not_deep_search_all_data_when_any_filter_is_invalid() {
  }

  // Entry Search Functions
  public function test_should_parse_quality_value_with_multiple_values() {
    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];

    $value = '4k, 1080p, 720p, 480p, 360p';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $value = '4K, 1080P, 720P, 480P, 360P';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $value = '2160p, 1080p, 720p, 480p, 360p';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $value = '2160P, 1080P, 720P, 480P, 360P';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $value = '2160, 1080, 720, 480, 360';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $value = 'uhd, fhd, hd, hq, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $value = 'uhd,fhd,hd,hq,lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_quality_value_with_absolute_value() {
    $expected = ['4K 2160p'];
    $values = ['4K', '4k', 'UHD', 'uhd', '2160P', '2160p', '2160'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['FHD 1080p'];
    $values = ['FHD', 'fhd', '1080P', '1080p', '1080'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HD 720p'];
    $values = ['HD', 'hd', '720P', '720p', '720'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HQ 480p'];
    $values = ['HQ', 'hq', '480P', '480p', '480'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['LQ 360p'];
    $values = ['LQ', 'lq', '360P', '360p', '360'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_parse_quality_value_with_comparators() {
    $expected = ['4K 2160p'];
    $values = [
      '>= uhd',
      '>= 4k',
      'gte uhd',
      'gte 4k',
      'greater than equal uhd',
      'greater than equal 4k',
      'greater than or equal uhd',
      'greater than or equal 4k',
    ];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['<= uhd', 'lte uhd', 'less than equal uhd', 'less than or equal uhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['< uhd', 'lt uhd', 'less than uhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p'];
    $values = ['> fhd', 'gt fhd', 'greater than fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p'];
    $values = ['>= fhd', 'gte fhd', 'greater than equal fhd', 'greater than or equal fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['<= fhd', 'lte fhd', 'less than equal fhd', 'less than or equal fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['< fhd', 'lt fhd', 'less than fhd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p'];
    $values = ['> hd', 'gt hd', 'greater than hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p'];
    $values = ['>= hd', 'gte hd', 'greater than equal hd', 'greater than or equal hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['<= hd', 'lte hd', 'less than equal hd', 'less than or equal hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HQ 480p', 'LQ 360p'];
    $values = ['< hd', 'lt hd', 'less than hd'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p'];
    $values = ['> hq', 'gt hq', 'greater than hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p'];
    $values = ['>= hq', 'gte hq', 'greater than equal hq', 'greater than or equal hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['HQ 480p', 'LQ 360p'];
    $values = ['<= hq', 'lte hq', 'less than equal hq', 'less than or equal hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['LQ 360p'];
    $values = ['< hq', 'lt hq', 'less than hq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p'];
    $values = ['> lq', 'gt lq', 'greater than lq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $values = ['>= lq', 'gte lq', 'greater than equal lq', 'greater than or equal lq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = ['LQ 360p'];
    $values = ['<= lq', 'lte lq', 'less than equal lq', 'less than or equal lq'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_quality($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_valid_filters_when_parsing_partial_invalid_quality() {
    $expected = ['FHD 1080p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $value = 'invalid, fhd, hd, hq, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'HD 720p', 'HQ 480p', 'LQ 360p'];
    $value = 'uhd, invalid, hd, hq, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'FHD 1080p', 'HQ 480p', 'LQ 360p'];
    $value = 'uhd, fhd, invalid, hq, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'LQ 360p'];
    $value = 'uhd, fhd, hd, invalid, lq';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);

    $expected = ['4K 2160p', 'FHD 1080p', 'HD 720p', 'HQ 480p'];
    $value = 'uhd, fhd, hd, hq, invalid';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_null_on_parsing_empty_quality() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_quality($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_completely_invalid_quality() {
    $value = 'greater than uhd';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = 'greater than 4k';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = 'less than 360p';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = '< lq';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = 'greater than invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      Exception::class
    );

    $value = '> invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid, value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid,value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_quality($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_date_value_with_range() {
    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => '2020-11-12',
      'comparator' => null,
    ];

    $values = [
      'from 2020-10-12 to 2020-11-12',
      'from 12-10-2020 to 12-11-2020',
      'from 10/12/2020 to 11/12/2020',
      'from oct 12 2020 to nov 12 2020',
      'from Oct 12 2020 to Nov 12 2020',
      'from Oct 12, 2020 to Nov 12, 2020',
      'from october 12 2020 to november 12 2020',
      'from October 12 2020 to November 12 2020',
      'from October 12, 2020 to November 12, 2020',
      '2020-10-12 to 2020-11-12',
      '12-10-2020 to 12-11-2020',
      '10/12/2020 to 11/12/2020',
      'oct 12 2020 to nov 12 2020',
      'Oct 12 2020 to Nov 12 2020',
      'Oct 12, 2020 to Nov 12, 2020',
      'october 12 2020 to november 12 2020',
      'October 12 2020 to November 12 2020',
      'October 12, 2020 to November 12, 2020',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }

    $expected = [
      'date_from' => '2020-01-01',
      'date_to' => '2022-12-31',
      'comparator' => null,
    ];

    $value = '2020 to 2022';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 2020 to 2022';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'date_from' => '2020-03-01',
      'date_to' => '2020-06-30',
      'comparator' => null,
    ];

    $values = [
      '2020-03 to 2020-06',
      '2020-3 to 2020-6',
      'from 2020-03 to 2020-06',
      '03-2020 to 06-2020',
      '3-2020 to 6-2020',
      'from 03-2020 to 06-2020',
      '2020/03 to 2020/06',
      '2020/3 to 2020/6',
      'from 2020/03 to 2020/06',
      '03/2020 to 06/2020',
      '3/2020 to 6/2020',
      'from 03/2020 to 06/2020',
      'from 2020/03 to 2020/06',
      'Mar 2020 to Jun 2020',
      '2020 mar to 2020 jun',
      '2020 Mar to 2020 Jun',
      'from Mar 2020 to Jun 2020',
      'from 2020 Mar to 2020 Jun',
      'March 2020 to June 2020',
      'from March 2020 to June 2020',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }
  }

  public function test_should_parse_date_value_with_semirange_absolute_value() {
    $expected = [
      'date_from' => '2020-01-01',
      'date_to' => '2020-12-31',
      'comparator' => null,
    ];

    $value = '2020';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'date_from' => '2021-01-01',
      'date_to' => '2021-12-31',
      'comparator' => null,
    ];

    $value = '2021';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'date_from' => '2021-01-01',
      'date_to' => '2021-01-31',
      'comparator' => null,
    ];

    $values = [
      '2021-1',
      '2021-01',
      '1-2021',
      '01-2021',
      '2021/1',
      '2021/01',
      '1/2021',
      '01/2021',
      'jan 2021',
      'Jan 2021',
      'JAN 2021',
      'January 2021',
      '2021 jan',
      '2021 Jan',
      '2021 JAN',
      '2021 January',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }
  }

  public function test_should_parse_date_value_with_absolute_value() {
    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => null,
    ];

    $values = [
      '2020-10-12',
      '12-10-2020',
      '10/12/2020',
      'oct 12 2020',
      'Oct 12 2020',
      'Oct 12, 2020',
      'october 12 2020',
      'October 12 2020',
      'October 12, 2020',
    ];

    foreach ($values as $key => $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual, 'Error on $key = ' . $key);
    }
  }

  public function test_should_parse_date_value_with_comparators() {
    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '>',
    ];

    $values = [
      '> 2020-10-12',
      'gt 2020-10-12',
      'greater than 2020-10-12',
      '> oct 12 2020',
      'gt oct 12 2020',
      'greater than oct 12 2020',
      '> oct 12, 2020',
      'gt oct 12, 2020',
      'greater than oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '>=',
    ];

    $values = [
      '>= 2020-10-12',
      'gte 2020-10-12',
      'greater than equal 2020-10-12',
      'greater than or equal 2020-10-12',
      '>= oct 12 2020',
      'gte oct 12 2020',
      'greater than equal oct 12 2020',
      'greater than or equal oct 12 2020',
      '>= oct 12, 2020',
      'gte oct 12, 2020',
      'greater than equal oct 12, 2020',
      'greater than or equal oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '<',
    ];

    $values = [
      '< 2020-10-12',
      'lt 2020-10-12',
      'less than 2020-10-12',
      '< oct 12 2020',
      'lt oct 12 2020',
      'less than oct 12 2020',
      '< oct 12, 2020',
      'lt oct 12, 2020',
      'less than oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'date_from' => '2020-10-12',
      'date_to' => null,
      'comparator' => '<=',
    ];

    $values = [
      '<= 2020-10-12',
      'lte 2020-10-12',
      'less than equal 2020-10-12',
      'less than or equal 2020-10-12',
      '<= oct 12 2020',
      'lte oct 12 2020',
      'less than equal oct 12 2020',
      'less than or equal oct 12 2020',
      '<= oct 12, 2020',
      'lte oct 12, 2020',
      'less than equal oct 12, 2020',
      'less than or equal oct 12, 2020',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_date($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_date() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_date($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_date() {
    $value = '2020-11-20 to 2020-10-21';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );

    $value = '<> invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );

    $value = '> jan 40 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_date($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_filesize_value_with_range() {
    $expected = [
      'filesize_from' => 3_145_728,
      'filesize_to' => 3_221_225_472,
      'comparator' => null,
    ];

    $value = 'from 3145728 to 3221225472';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 3 MB to 3 GB';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 3MB to 3GB';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'filesize_from' => 3,
      'filesize_to' => 10_995_116_277_760,
      'comparator' => null,
    ];

    $value = 'from 3 to 10995116277760';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);

    $value = 'from 3 to 10TB';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_filesize_value_correctly() {
    $expected = [
      'filesize_from' => 3,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_072,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3072', '> 3KB', '> 3 KB', '> 3kb', '> 3 kb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_145_728,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3145728', '> 3MB', '> 3 MB', '> 3mb', '> 3 mb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3221225472', '> 3GB', '> 3 GB', '> 3gb', '> 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_298_534_883_328,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3298534883328', '> 3TB', '> 3 TB', '> 3tb', '> 3 tb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_parse_filesize_value_with_comparators() {
    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3 gb', 'gt 3 gb', 'greater than 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3 gb', 'gte 3 gb', 'greater than equal 3 gb', 'greater than or equal 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3 gb', 'lt 3 gb', 'less than 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'filesize_from' => 3_221_225_472,
      'filesize_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3 gb', 'lte 3 gb', 'less than equal 3 gb', 'less than or equal 3 gb'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_filesize($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_filesize() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_filesize($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_filesize() {
    $value = '6 GB to 3 GB';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_filesize($value),
      SearchFilterParsingException::class
    );

    $value = '5 EB to 6 EB';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_filesize($value),
      SearchFilterParsingException::class
    );

    $value = '>< 6 GB';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_filesize($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_count_value_with_range() {
    $expected = [
      'count_from' => 3,
      'count_to' => 6,
      'comparator' => null,
    ];

    $value = 'from 3 to 6';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertEquals($expected, $actual);

    $value = '3 to 6';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_count_value_with_absolute_value() {
    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => null,
    ];

    $value = '3';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_count_value_with_comparators() {
    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3', 'gt 3', 'greater than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3', 'gte 3', 'greater than equal 3', 'greater than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3', 'lt 3', 'less than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'count_from' => 3,
      'count_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3', 'lte 3', 'less than equal 3', 'less than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_count() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_count($value, 'test_field');
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_count_value() {
    $value = '6 to 3';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_count($value, 'test_field'),
      SearchFilterParsingException::class,
    );

    $value = 'invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_count($value, 'test_field'),
      SearchFilterParsingException::class,
    );

    $value = '>< 6';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_count($value, 'test_field'),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_rating_value_with_range() {
    $expected = [
      'rating_from' => 3,
      'rating_to' => 6,
      'comparator' => null,
    ];

    $value = 'from 3 to 6';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $value = '3 to 6';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'rating_from' => 3.3,
      'rating_to' => 6.75,
      'comparator' => null,
    ];

    $value = 'from 3.3 to 6.75';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $value = '3.3 to 6.75';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_rating_value_with_absolute_value() {
    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => null,
    ];

    $value = '3';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => null,
    ];

    $value = '3.75';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_rating_value_with_comparators() {
    $expected = [
      'rating_from' => 10,
      'rating_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 10', 'gte 10', 'greater than equal 10', 'greater than or equal 10'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3', 'gt 3', 'greater than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3', 'gte 3', 'greater than equal 3', 'greater than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3', 'lt 3', 'less than 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3,
      'rating_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3', 'lte 3', 'less than equal 3', 'less than or equal 3'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '>',
    ];

    $values = ['> 3.75', 'gt 3.75', 'greater than 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '>=',
    ];

    $values = ['>= 3.75', 'gte 3.75', 'greater than equal 3.75', 'greater than or equal 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '<',
    ];

    $values = ['< 3.75', 'lt 3.75', 'less than 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'rating_from' => 3.75,
      'rating_to' => null,
      'comparator' => '<=',
    ];

    $values = ['<= 3.75', 'lte 3.75', 'less than equal 3.75', 'less than or equal 3.75'];
    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_rating($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_return_null_on_parsing_empty_rating() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_rating($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_rating_value() {
    $value = '6 to 3';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class,
    );

    $value = 'invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class,
    );

    $value = '>< 6';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '> 10';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '10 to 10';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '5 to 5';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '11';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '10.1';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '-1';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );

    $value = '-0.1';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_rating($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_release_value_with_range() {
    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'winter',
      'release_to_year' => 2021,
      'release_to_season' => 'fall',
      'comparator' => null,
    ];

    $value = 'from 2020 to 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'from winter 2020 to fall 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 to 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'winter 2020 to fall 2021';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'spring',
      'release_to_year' => 2099,
      'release_to_season' => 'summer',
      'comparator' => null,
    ];

    $value = 'from spring 2020 to summer 2099';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_release_value_with_absolute_value() {
    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'winter 2020';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Winter 2020';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'WINTER 2020';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 Winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2020 WINTER';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 2999,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'spring 2999';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '2999 spring';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 1900,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'winter 1900';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = '1900 winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 1900,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = '1900';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => 2999,
      'release_from_season' => null,
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = '2999';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_release_value_with_comparators() {
    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '>',
    ];

    $values = [
      '> 2020',
      '> winter 2020',
      '> 2020 winter',
      'gt 2020',
      'gt winter 2020',
      'gt 2020 winter',
      'greater than 2020',
      'greater than winter 2020',
      'greater than 2020 winter',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '>=',
    ];

    $values = [
      '>= spring 2020',
      '>= 2020 spring',
      'gte spring 2020',
      'gte 2020 spring',
      'greater than equal spring 2020',
      'greater than equal 2020 spring',
      'greater than or equal spring 2020',
      'greater than or equal 2020 spring',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'summer',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '<=',
    ];

    $values = [
      '<= summer 2020',
      '<= 2020 summer',
      'lte summer 2020',
      'lte 2020 summer',
      'less than equal summer 2020',
      'less than equal 2020 summer',
      'less than or equal summer 2020',
      'less than or equal 2020 summer',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual);
    }

    $expected = [
      'release_from_year' => 2020,
      'release_from_season' => 'fall',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => '<',
    ];

    $values = [
      '< fall 2020',
      '< 2020 fall',
      'lt fall 2020',
      'lt 2020 fall',
      'less than fall 2020',
      'less than 2020 fall',
    ];

    foreach ($values as $value) {
      $actual = EntrySearchRepository::search_parse_release($value);
      $this->assertEquals($expected, $actual);
    }
  }

  public function test_should_parse_release_value_with_seasons_range() {
    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => 'fall',
      'comparator' => null,
    ];

    $value = 'from winter to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'From Winter to Fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'winter to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => 'summer',
      'comparator' => null,
    ];

    $value = 'from spring to summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'spring to summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => 'fall',
      'comparator' => null,
    ];

    $value = 'from spring to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'spring to fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_release_value_with_absolute_season() {
    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'winter',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'WINTER';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Winter';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'spring',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'spring';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'SPRING';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Spring';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'summer',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'SUMMER';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Summer';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $expected = [
      'release_from_year' => null,
      'release_from_season' => 'fall',
      'release_to_year' => null,
      'release_to_season' => null,
      'comparator' => null,
    ];

    $value = 'fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'FALL';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);

    $value = 'Fall';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_null_on_parsing_empty_release() {
    $value = '';
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertNull($actual);

    $value = null;
    $actual = EntrySearchRepository::search_parse_release($value);
    $this->assertNull($actual);
  }

  public function test_should_throw_error_on_parsing_invalid_release_value() {
    $value = 'invalid value';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      Exception::class
    );

    $value = '> 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid to invalid';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '1899';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '1899 to 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'invalid 1899 to invalid 3000';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = '2020 to 2019';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'spring 2020 to winter 2020';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'spring to winter';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'summer to spring';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'summer to winter';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'fall to winter';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );

    $value = 'fall to summer';
    $this->assertThrows(
      fn() => EntrySearchRepository::search_parse_release($value),
      SearchFilterParsingException::class
    );
  }

  public function test_should_parse_has_value_as_yes() {
    $expected = EntrySearchHasEnum::YES;

    $actual = EntrySearchRepository::search_parse_has_value('yes');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('YES');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('Yes');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('true');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('TRUE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('True');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('TruE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value(true);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_has_value_as_no() {
    $expected = EntrySearchHasEnum::NO;

    $actual = EntrySearchRepository::search_parse_has_value('no');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('NO');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('No');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('false');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('FALSE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('False');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('FalsE');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value(false);
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_has_value_as_any() {
    $expected = EntrySearchHasEnum::ANY;

    $actual = EntrySearchRepository::search_parse_has_value('any');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('null');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('default');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value('');
    $this->assertEquals($expected, $actual);

    $actual = EntrySearchRepository::search_parse_has_value(null);
    $this->assertEquals($expected, $actual);
  }
}
