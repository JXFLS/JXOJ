<?php
return array (
  'profile' => 
  array (
    'oj-name' => 'JXFLS Online Judge',
    'oj-name-short' => 'JXOJ',
    'administrator' => 'admin',
    'admin-email' => 'admin@uoj',
    'qq-group' => '',
    'ICP-license' => '',
  ),
  'database' => 
  array (
    'database' => 'app_uoj233',
    'username' => 'root',
    'password' => 'root',
    'host' => '127.0.0.1',
  ),
  'web' => 
  array (
    'domain' => NULL,
    'main' => 
    array (
      'protocol' => 'http',
      'host' => isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''),
      'port' => 80,
    ),
    'blog' => 
    array (
      'protocol' => 'http',
      'host' => isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''),
      'port' => 80,
    ),
  ),
  'security' => 
  array (
    'user' => 
    array (
      'client_salt' => 'a8Tk5f6iDq5HDpVK6monDICNUCpXOEvn',
    ),
    'cookie' => 
    array (
      'checksum_salt' => 
      array (
        0 => '9jy8fjZKOma7HRvk',
        1 => 'IQSUAIex6q9jZqUR',
        2 => 'p2GCrLvFQjMHqW6r',
      ),
    ),
  ),
  'mail' => 
  array (
    'noreply' => 
    array (
      'username' => 'noreply@none',
      'password' => 'noreply',
    ),
  ),
  'judger' => 
  array (
    'socket' => 
    array (
      'port' => 2333,
      'password' => 'EiNUA0tQIyUy6hwyTYToFJ3urP6DKt0I',
    ),
  ),
  'svn' => 
  array (
    'our-root' => 
    array (
      'username' => 'our-root',
      'password' => 'kR2wDTl3i0FH64BTmIi8ZtfLVjMlQxp5',
    ),
  ),
  'switch' => 
  array (
    'ICP-license' => false,
    'web-analytics' => false,
    'blog-use-subdomain' => false,
  ),
);
