<?php

namespace AxelSpringer\WP\HTTP;

abstract class Header {
  const ContentEncoding   = 'Content-Encoding';
  const Location          = 'Location';
  const CacheControl      = 'Cache-Control';
  const ETag              = 'ETag';
  const Expires           = 'Expires';
  const Pragma            = 'Pragma';
  const LastModified      = 'Last-Modified';
}
