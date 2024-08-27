<?php

namespace App\Exceptions\Entry;

use App\Exceptions\CustomException;

class SearchFilterParsingException extends CustomException {

  protected $field = '';
  protected $error_message = '';

  public function __construct($field, $error_message) {
    $this->field = $field;
    $this->error_message = $error_message;
  }

  public function render() {
    return response()->json([
      'status' => 401,
      'data' => [$this->field => [$this->error_message]],
    ], 401);
  }
}
