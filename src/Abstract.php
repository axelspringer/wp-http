<?php

namespace Asse\Plugin\Http;

abstract class CDN {
  const None        = 'none'; // hook
  const Akamai      = 'akamai';
  const Cloudfront  = 'cloudfront';
}

abstract class Device {
  const Mobile        = 'mobile';
  const Desktop       = 'desktop';
  const Tablet        = 'tablet';
  const SmartTv       = 'tv';
}

abstract class Encoding {
  const Brotli        = 'br';
  const GZip          = 'gzip';
  const Deflate       = 'deflate';
}
