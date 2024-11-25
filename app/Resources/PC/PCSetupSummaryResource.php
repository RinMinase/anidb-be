<?php

namespace App\Resources\PC;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="rawType", type="string", example="ssd"),
 *   @OA\Property(property="type", type="string", example="SSD"),
 *   @OA\Property(property="name", type="string", example="Component Name"),
 *   @OA\Property(property="description", type="string", example="Component Description"),
 *   @OA\Property(property="count", type="integer", example=2),
 *   @OA\Property(property="isHidden", type="boolean", example=false),
 * )
 */
class PCSetupSummaryResource extends JsonResource {

  public function toArray($request) {
    return [
      'id' => $this->id,
      'rawType' => $this->component->type->type ?? '',
      'type' => $this->calculate_type($this->component->type->type),
      'name' => $this->component->name ?? '',
      'description' => $this->component->description ?? '',
      'count' => $this->count,
      'isHidden' => $this->is_hidden,
    ];
  }

  private function calculate_type(?string $type): string {
    if (!isset($type)) {
      return '';
    }

    if ($type === 'pcie_card') return 'PCIe Card';
    if ($type === 'keyboard_accessory') return 'Keyboard Accessory';
    if ($type === 'audio_related') return 'Audio-related';

    $types = ['cpu', 'gpu', 'ram', 'psu', 'ssd', 'hdd'];
    if (in_array($type, $types)) {
      return strtoupper($type);
    }

    if (!empty($type)) {
      return ucfirst($type);
    }

    return '';
  }
}
