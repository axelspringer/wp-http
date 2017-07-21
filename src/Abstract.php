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

abstract class Header {
  const ContentEncoding   = 'Content-Encoding';
  const Location          = 'Location';
  const CacheControl      = 'Cache-Control';
  const ETag              = 'ETag';
  const Expires           = 'Expires';
  const Pragma            = 'Pragma';
  const LastModified      = 'Last-Modified';
}

abstract class Code {
  const HTTP200       = 200;
  const HTTP403       = 403;
  const HTTP404       = 404;
  const HTTP503       = 503;
}

abstract class Legacy {
  const HTTP304       = 'HTTP/1.1 304 Not Modified';
  const HTTP503       = 'HTTP/1.1 503 Service Unavailable';
}
