<?php

class AsseMobileDetect {

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

class AsseMobileDetectCloudfront extends AsseMobileDetect {

  /**
   * Undocumented function
   */
  public function __construct() {
    $this->device = isset( $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] )
      && $_SERVER['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] == 'true' ? AsseHttpDevice::Mobile : AsseHttpDevice::Desktop;
  }
}

class AsseMobileDetectUA extends AsseMobileDetect {

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
    $this->device = $this->mobile_detect->isMobile() ? AsseHttpDevice::Mobile : AsseHttpDevice::Desktop;
  }
}

class AsseMobileDetectAkamai extends AsseMobileDetect {
}
