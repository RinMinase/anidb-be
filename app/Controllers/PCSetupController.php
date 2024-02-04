<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCSetupRepository;

use App\Requests\ImportRequest;
use App\Requests\PCSetup\AddEditRequest;

use App\Resources\DefaultResponse;

class PCSetupController extends Controller {

  private PCSetupRepository $pcSetupRepository;

  public function __construct(PCSetupRepository $pcSetupRepository) {
    $this->pcSetupRepository = $pcSetupRepository;
  }

  /**
   * @OA\Get(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups",
   *   summary="Get All PC Setups",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/PCSetup"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    $setups = $this->pcSetupRepository->getAll();

    return DefaultResponse::success(null, [
      'data' => $setups,
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups/{pc_setup_id}",
   *   summary="Get a PC Setup",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="pc_setup_id",
   *     description="PC Setup ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/PCSetup"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($id): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->pcSetupRepository->get($id),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups",
   *   summary="Add a PC Setup",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_label"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_is_current"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_is_future"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_is_server"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu_sub2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ram"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ram_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ram_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_gpu"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_gpu_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_gpu_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_motherboard"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_motherboard_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_psu"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_psu_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler_acc"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler_acc_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_3"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_3_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_4"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_4_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_3"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_3_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_4"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_4_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_3"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_3_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_4"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_4_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_sub2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mouse"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mouse_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_speakers"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_speakers_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_wifi"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_wifi_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic_acc"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic_acc_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_audio_interface"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_audio_interface_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_equalizer"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_equalizer_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_amplifier"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_amplifier_price"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->pcSetupRepository->add($request->only(
      'label',
      'is_current',
      'is_future',
      'is_server',
      'cpu',
      'cpu_price',
      'cpu_sub',
      'cpu_sub2',
      'ram',
      'ram_price',
      'ram_sub',
      'gpu',
      'gpu_price',
      'gpu_sub',
      'motherboard',
      'motherboard_price',
      'psu',
      'psu_price',
      'cooler',
      'cooler_price',
      'cooler_acc',
      'cooler_acc_price',
      'ssd_1',
      'ssd_1_price',
      'ssd_2',
      'ssd_2_price',
      'ssd_3',
      'ssd_3_price',
      'ssd_4',
      'ssd_4_price',
      'hdd_1',
      'hdd_1_price',
      'hdd_2',
      'hdd_2_price',
      'hdd_3',
      'hdd_3_price',
      'hdd_4',
      'hdd_4_price',
      'case',
      'case_price',
      'case_fans_1',
      'case_fans_1_price',
      'case_fans_2',
      'case_fans_2_price',
      'case_fans_3',
      'case_fans_3_price',
      'case_fans_4',
      'case_fans_4_price',
      'monitor',
      'monitor_price',
      'monitor_sub',
      'monitor_acc_1',
      'monitor_acc_1_price',
      'monitor_acc_2',
      'monitor_acc_2_price',
      'keyboard',
      'keyboard_price',
      'keyboard_sub',
      'keyboard_sub2',
      'keyboard_acc_1',
      'keyboard_acc_1_price',
      'keyboard_acc_2',
      'keyboard_acc_2_price',
      'mouse',
      'mouse_price',
      'speakers',
      'speakers_price',
      'wifi',
      'wifi_price',
      'headset_1',
      'headset_1_price',
      'headset_2',
      'headset_2_price',
      'mic',
      'mic_price',
      'mic_acc',
      'mic_acc_price',
      'audio_interface',
      'audio_interface_price',
      'equalizer',
      'equalizer_price',
      'amplifier',
      'amplifier_price',
    ));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups/{pc_setup_id}",
   *   summary="Edit a PC Setup",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="pc_setup_id",
   *     description="PC Setup ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_label"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_is_current"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_is_future"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_is_server"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cpu_sub2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ram"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ram_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ram_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_gpu"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_gpu_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_gpu_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_motherboard"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_motherboard_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_psu"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_psu_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler_acc"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_cooler_acc_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_3"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_3_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_4"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_ssd_4_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_3"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_3_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_4"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_hdd_4_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_3"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_3_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_4"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_case_fans_4_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_monitor_acc_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_sub"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_sub2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_keyboard_acc_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mouse"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mouse_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_speakers"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_speakers_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_wifi"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_wifi_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_1"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_1_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_2"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_headset_2_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic_acc"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_mic_acc_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_audio_interface"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_audio_interface_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_equalizer"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_equalizer_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_amplifier"),
   *   @OA\Parameter(ref="#/components/parameters/pc_setup_add_edit_amplifier_price"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->pcSetupRepository->edit(
      $request->only(
        'label',
        'is_current',
        'is_future',
        'is_server',
        'cpu',
        'cpu_price',
        'cpu_sub',
        'cpu_sub2',
        'ram',
        'ram_price',
        'ram_sub',
        'gpu',
        'gpu_price',
        'gpu_sub',
        'motherboard',
        'motherboard_price',
        'psu',
        'psu_price',
        'cooler',
        'cooler_price',
        'cooler_acc',
        'cooler_acc_price',
        'ssd_1',
        'ssd_1_price',
        'ssd_2',
        'ssd_2_price',
        'ssd_3',
        'ssd_3_price',
        'ssd_4',
        'ssd_4_price',
        'hdd_1',
        'hdd_1_price',
        'hdd_2',
        'hdd_2_price',
        'hdd_3',
        'hdd_3_price',
        'hdd_4',
        'hdd_4_price',
        'case',
        'case_price',
        'case_fans_1',
        'case_fans_1_price',
        'case_fans_2',
        'case_fans_2_price',
        'case_fans_3',
        'case_fans_3_price',
        'case_fans_4',
        'case_fans_4_price',
        'monitor',
        'monitor_price',
        'monitor_sub',
        'monitor_acc_1',
        'monitor_acc_1_price',
        'monitor_acc_2',
        'monitor_acc_2_price',
        'keyboard',
        'keyboard_price',
        'keyboard_sub',
        'keyboard_sub2',
        'keyboard_acc_1',
        'keyboard_acc_1_price',
        'keyboard_acc_2',
        'keyboard_acc_2_price',
        'mouse',
        'mouse_price',
        'speakers',
        'speakers_price',
        'wifi',
        'wifi_price',
        'headset_1',
        'headset_1_price',
        'headset_2',
        'headset_2_price',
        'mic',
        'mic_price',
        'mic_acc',
        'mic_acc_price',
        'audio_interface',
        'audio_interface_price',
        'equalizer',
        'equalizer_price',
        'amplifier',
        'amplifier_price',
      ),
      $id,
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/pc-setups/import",
   *   summary="Import a JSON file to seed data for PC Setups table",
   *   security={{"token":{}}},
   *
   *   @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="multipart/form-data",
   *       @OA\Schema(
   *         type="object",
   *         @OA\Property(property="file", type="string", format="binary"),
   *       ),
   *     ),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/DefaultImportSchema"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function import(ImportRequest $request) {
    $file = json_decode($request->file('file')->get());
    $count = $this->pcSetupRepository->import($file);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($file),
      ],
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups/duplicate/{pc_setup_id}",
   *   summary="Duplicate a PC Setup and return ID of duplicate",
   *   security={{"token":{}}},
   *
   *
   *   @OA\Parameter(
   *     name="pc_setup_id",
   *     description="PC Setup ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             @OA\Property(property="newID", type="integer", format="int32", example=1),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function duplicate($id): JsonResponse {
    $new_pc_setup_id = $this->pcSetupRepository->duplicate($id);

    return DefaultResponse::success(null, [
      'data' => [
        'newID' => $new_pc_setup_id,
      ],
    ]);
  }

  /**
   * @OA\Put(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups/current/{pc_setup_id}",
   *   summary="Toggle a PC Setup as current",
   *   security={{"token":{}}},
   *
   *
   *   @OA\Parameter(
   *     name="pc_setup_id",
   *     description="PC Setup ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function toggleCurrent($id): JsonResponse {
    $this->pcSetupRepository->toggleCurrent($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups/future/{pc_setup_id}",
   *   summary="Toggle a PC Setup as future",
   *   security={{"token":{}}},
   *
   *
   *   @OA\Parameter(
   *     name="pc_setup_id",
   *     description="PC Setup ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function toggleFuture($id): JsonResponse {
    $this->pcSetupRepository->toggleFuture($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups/server/{pc_setup_id}",
   *   summary="Toggle a PC Setup as a server setup",
   *   security={{"token":{}}},
   *
   *
   *   @OA\Parameter(
   *     name="pc_setup_id",
   *     description="PC Setup ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function toggleServer($id): JsonResponse {
    $this->pcSetupRepository->toggleServer($id);

    return DefaultResponse::success();
  }
}
