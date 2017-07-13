<?php

namespace Asse\Plugin\Http;

abstract class CDN {
  const None        = 0; // hook
  const Akamai      = 1;
  const Cloudfront  = 2;
}

abstract class Device {
  const Mobile        = 'mobile';
  const Desktop       = 'desktop';
  const Tablet        = 'tablet';
  const SmartTv       = 'tv';
}
