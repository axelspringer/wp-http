<?php

namespace Asse\Plugin\Http;

class MobileDetect {

  /**
   * Undocumented variable
   *
   * @var [type]
   */
  protected $device;

  /**
   * Undocumented function
   *
   * @return void
   */
  public function set_header() {
    if ( empty( $this->device ) ) {
      return;
    }
    $_SERVER['HTTP_X_UA_DEVICE'] = $this->device;
  }

  /**
   * Undocumented function
   *
   * @param [type] $header
   * @return boolean
   */
  protected static function is_mobile( $header ) {
    return $header === 'mobile'
      ? \Asse\Plugin\Http\Device::Mobile
      : \Asse\Plugin\Http\Device::Desktop;
  }
}

class MobileDetectCloudfront extends MobileDetect {

  /**
   * Undocumented function
   */
  public function __construct() {
    $this->device = isset( $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] )
      && $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] == 'true' ? \Asse\Plugin\Http\Device::Mobile : \Asse\Plugin\Http\Device::Desktop;
  }
}

class MobileDetectUA extends MobileDetect {

  /**
   * Undocumented variable
   *
   * @var [type]
   */
  private $mobile_detect;

  /**
   * Undocumented function
   */
  public function __construct() {
    if ( ! class_exists( 'Mobile_Detect' ) ) {
      return;
    }

    if ( isset( $_SERVER['HTTP-X-UA-DEVICE'] ) ) {
      $this->device = MobileDetect::is_mobile( $_SERVER['HTTP-X-UA-DEVICE'] );

      return;
    }

    $this->mobile_detect = new \Mobile_Detect();
    $this->device = $this->mobile_detect->isMobile()
      ? \Asse\Plugin\Http\Device::Mobile
      : \Asse\Plugin\Http\Device::Desktop;
  }
}

class MobileDetectAkamai extends MobileDetect {
}
