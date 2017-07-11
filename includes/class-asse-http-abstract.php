<?php

abstract class AsseHttpCDN {
    const None        = 0; // hook
    const Akamai      = 1;
    const Cloudfront  = 2;
}

abstract class AsseHttpDevice {
  const Mobile        = 'mobile';
  const Desktop       = 'desktop';
  const Tablet        = 'tablet';
  const SmartTv       = 'tv';
}
