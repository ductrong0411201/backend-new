<?php

namespace App\Http\Controllers\Api;

/**
 * Class XMLHelper.
 */
class XMLHelper {

  /**
   * parse xml string to object/array
   *
   * @param [string] $xml
   * @param [boolean] $object true to convert to object, false to convert to array
   * @return mixed
   */
  public static function parse($xml, $object = true) {
    return json_decode(json_encode((array) simplexml_load_string($xml)), $object);
  }
}
