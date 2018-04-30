<?php

namespace AxelSpringer\WP\HTTP;

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

    $this->mobile_detect = new \Mobile_Detect();
    $this->device = $this->mobile_detect->isMobile()
      ? \Asse\Plugin\Http\Device::Mobile
      : \Asse\Plugin\Http\Device::Desktop;
  }
}

class MobileDetectAkamai extends MobileDetect {

  public function __construct() {

    if ( ! isset( $_SERVER['HTTP_X_UA_DEVICE'] ) ) {
      return;
    }

    $this->device = $_SERVER['HTTP_X_UA_DEVICE'] === 'mobile'
      ? \Asse\Plugin\Http\Device::Mobile
      : \Asse\Plugin\Http\Device::Desktop;

  }
}
