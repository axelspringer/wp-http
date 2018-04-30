<?php

namespace AxelSpringer\WP\HTTP;

abstract class Defaults {
  const CacheControl = array(
    'front_page'  => [
      'max-age'                 => 300,           //                5 min
      's-maxage'                => 150,            //                2 min 30 sec
      'public'                  => true,
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'single'      => [
      'max-age'                 => 600,           //               10 min
      's-maxage'                => 60,            //                1 min
      'mmulti'                  => 1,              // enabled,
      'public'                  => true,
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'page'        => [
      'max-age'                 => 1200,          //               20 min
      's-maxage'                => 300,            //                5 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'home'         => [
      'max-age'                 => 180,           //                3 min
      's-maxage'                => 45,            //                      45 sec
      'paged'                   => 5,              //                       5 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'category'   => [
      'max-age'                 => 900,           //               15 min
      's-maxage'                => 300,           //                5 min
      'paged'                   => 8,              //                       8 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'tag'         => [
      'max-age'                 => 900,           //               15 min
      's-maxage'                => 300,           //                5 min            //                       8 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'author'      => [
      'max-age'                 => 1800,          //               30 min
      's-maxage'                => 600,           //               10 min
      'paged'                   => 10,             //                      10 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'date'        =>  [
      'max-age'                 => 10800,         //      3 hours
      's-maxage'                => 2700,          //               45 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'feed'        => [
      'max-age'                 => 5400,          //       1 hours 30 min
      's-maxage'                => 600,            //               10 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'attachment'   => [
      'max-age'                 => 10800,         //       3 hours
      's-maxage'                => 2700,          //               45 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'search'       => [
      'max-age'                 => 1800,          //               30 min
      's-maxage'                => 600,            //               10 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    '404'     => [
      'max-age'                 => 900,           //               15 min
      's-maxage'                => 300,            //                5 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ]
  );

  const BrotliCompressionLevel  = 4;
  const ZLibCompressionLevel    = 6;
  const GZipCompressionLevel    = 6;
  const AllowedCacheControllHeaders = array(
    'max-age',
    's-maxage',
    'min-fresh',
    'must-revalidate',
    'no-cache',
    'no-store',
    'no-transform',
    'public',
    'private',
    'proxy-revalidate',
    'stale-while-revalidate',
    'stale-if-error'
  );
  const AcceptedEncoding = array(
    Encoding::Brotli,
    Encoding::GZip,
    Encoding::Deflate
  );
}
