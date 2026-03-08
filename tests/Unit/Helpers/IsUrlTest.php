<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class IsUrlTest extends TestCase {
  public function test_should_return_true_on_valid_url_formats() {
    $test_urls = [
      'https://www.google.com',
      'http://example.com/page',
      'https://subdomain.domain.org/path/to/file.html',
      'http://domain.net?query=value',
      'https://api.example.io/v1/user/123',

      'http://example.com:8080/test',
      'https://example.com/path%20with%20spaces/',
      'http://example-website.com',
      'https://example.university/',
      'https://example.com/#anchor-point',
      'http://example.com/path-value/test.cgi?param=val#section',

      // "Valid" URLs due to how parse_url works
      'https://example.c',
    ];

    foreach ($test_urls as $key => $value) {
      $actual = is_url($value);
      $this->assertTrue($actual, 'Error in $key=' . $key);
    }
  }

  public function test_should_return_false_on_invalid_url_formats() {
    $test_urls = [
      'https://localhost',

      // IPv4 Addresses
      'https://127.0.0.1',
      'https://127.0.0.1/localhost',
      'http://256.256.256.256',
      '127.0.0.1',

      // IPv6 Addresses
      '2001:db8:85a3:0000:0000:8a2e:0370:7334',
      '[2001:db8:85a3:0000:0000:8a2e:0370:7334]',
      'http://[2001:db8:85a3:8d3:1319:8a2e:370:7348]/',
      'https://[2001:db8::1]/',
      'http://[::1]/',
      'http://[2001:db8::1]:8080/',
      'http://[fe80::1ff:fe23:4567:890a%eth0]/',
      'http://[::ffff:192.168.1.1]/',
      'http://[2001:db8::g123]/',
      'http://2001:db8::1/',
      'http://[2001:db8:85a3::8g3]',

      // Credentials
      'https://user:pass@example.com',
      'http://username@password:example.com',

      'google.com',
      'https://',
      'http://...example.com',
      'http://example .com',
      'http:/example.com',
      'https://example..com',
      'http://-example.com',
      'http://repo_name.example.com',
      'https://example.com:65536',
      'mailto:test@example.com',
      'javascript:alert(1)',
      'https://example.com/path\with\backslashes',
      'http://.com',
      'ftp://example.com/file.zip',
      'sftp://storage.server.net',
      'file:///C:/Users/Desktop/index.html',
      'mailto:support@example.com',
      'ssh://root@192.168.1.1',
      'git@github.com:user/repo.git',
      'mongodb://localhost:27017',
      'tel:+1234567890',
      'sms:+1234567890',
      'data:image/png;base64,iVBORw0KGgoAAA',
      'ws://echo.websocket.org',
      'notes://internal-server/database',
      'htttps://example.com',
      '://example.com',
      'http:example.com',
      'http://123.45.67',
      'https://',
      'http://example.com?query=val|invalid',
      'http://點看.com',
    ];

    foreach ($test_urls as $key => $value) {
      $actual = is_url($value);
      $this->assertFalse($actual, 'Error in $key=' . $key);
    }
  }
}
