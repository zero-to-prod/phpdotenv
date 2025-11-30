<?php

namespace Tests\Unit;

use Tests\TestCase;
use Zerotoprod\Phpdotenv\Phpdotenv;

class ParseFromStringTest extends TestCase
{
    /** @test */
    public function it_parses_simple_key_value_pairs_from_string(): void
    {
        $subject = 'TEST_KEY=test_value';

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['TEST_KEY' => 'test_value'], $result);
    }

    /** @test */
    public function it_parses_multiple_lines_with_unix_newlines(): void
    {
        $subject = "APP_NAME=MyApp\nAPP_ENV=production\nAPP_DEBUG=false";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'APP_NAME' => 'MyApp',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false'
        ], $result);
    }

    /** @test */
    public function it_parses_multiple_lines_with_windows_newlines(): void
    {
        $subject = "APP_NAME=MyApp\r\nAPP_ENV=production\r\nAPP_DEBUG=false";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'APP_NAME' => 'MyApp',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false'
        ], $result);
    }

    /** @test */
    public function it_parses_multiple_lines_with_mac_newlines(): void
    {
        $subject = "APP_NAME=MyApp\rAPP_ENV=production\rAPP_DEBUG=false";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'APP_NAME' => 'MyApp',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false'
        ], $result);
    }

    /** @test */
    public function it_parses_multiple_lines_with_mixed_newlines(): void
    {
        $subject = "APP_NAME=MyApp\r\nAPP_ENV=production\nAPP_DEBUG=false\rDATABASE_URL=mysql://localhost";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'APP_NAME' => 'MyApp',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'DATABASE_URL' => 'mysql://localhost'
        ], $result);
    }

    /** @test */
    public function it_skips_comment_lines_in_string(): void
    {
        $subject = "# This is a comment\nTEST_KEY=test_value\n# Another comment";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['TEST_KEY' => 'test_value'], $result);
    }

    /** @test */
    public function it_strips_double_quotes_from_values_in_string(): void
    {
        $subject = 'QUOTED_DOUBLE="quoted value"';

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['QUOTED_DOUBLE' => 'quoted value'], $result);
    }

    /** @test */
    public function it_strips_single_quotes_from_values_in_string(): void
    {
        $subject = "QUOTED_SINGLE='single quoted'";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['QUOTED_SINGLE' => 'single quoted'], $result);
    }

    /** @test */
    public function it_trims_whitespace_from_keys_and_values_in_string(): void
    {
        $subject = "  WITH_SPACES  =  value with trim  ";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['WITH_SPACES' => 'value with trim'], $result);
    }

    /** @test */
    public function it_handles_unquoted_values_in_string(): void
    {
        $subject = 'UNQUOTED=plain_value';

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['UNQUOTED' => 'plain_value'], $result);
    }

    /** @test */
    public function it_filters_out_empty_lines_in_string(): void
    {
        $subject = "TEST_KEY=value1\n\n\nTEST_VALUE=value2";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'TEST_KEY' => 'value1',
            'TEST_VALUE' => 'value2'
        ], $result);
    }

    /** @test */
    public function it_handles_lines_without_equals_sign_in_string(): void
    {
        $subject = "VALID_KEY=value\nINVALID_LINE_NO_EQUALS\nANOTHER_KEY=another_value";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'VALID_KEY' => 'value',
            'ANOTHER_KEY' => 'another_value'
        ], $result);
    }

    /** @test */
    public function it_handles_values_with_equals_signs_in_string(): void
    {
        $subject = 'MULTILINE_KEY=value=with=equals';

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['MULTILINE_KEY' => 'value=with=equals'], $result);
    }

    /** @test */
    public function it_handles_empty_values_in_string(): void
    {
        $subject = 'TEST_KEY=';

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['TEST_KEY' => ''], $result);
    }

    /** @test */
    public function it_handles_mixed_quote_styles_in_value_in_string(): void
    {
        $subject = "TEST_KEY=\"value with 'single' quotes inside\"";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['TEST_KEY' => "value with 'single' quotes inside"], $result);
    }

    /** @test */
    public function it_returns_empty_array_for_empty_string(): void
    {
        $result = Phpdotenv::parseFromString('');

        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_overwrites_duplicate_keys_with_last_value_in_string(): void
    {
        $subject = "DUPLICATE_KEY=first_value\nDUPLICATE_KEY=second_value";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['DUPLICATE_KEY' => 'second_value'], $result);
    }

    /** @test */
    public function it_parses_realistic_env_file_format(): void
    {
        $subject = <<<ENV
# Application Settings
APP_NAME=MyApplication
APP_ENV=production
APP_DEBUG=false

# Database Configuration
DATABASE_URL="mysql://user:pass@localhost:3306/mydb"
DATABASE_POOL_SIZE=10

# API Keys
API_KEY='secret-key-123'
API_SECRET="another-secret"
ENV;

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'APP_NAME' => 'MyApplication',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'DATABASE_URL' => 'mysql://user:pass@localhost:3306/mydb',
            'DATABASE_POOL_SIZE' => '10',
            'API_KEY' => 'secret-key-123',
            'API_SECRET' => 'another-secret'
        ], $result);
    }

    /** @test */
    public function it_handles_string_with_only_comments(): void
    {
        $subject = "# Comment 1\n# Comment 2\n# Comment 3";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_string_with_only_empty_lines(): void
    {
        $subject = "\n\n\n";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_handles_trailing_and_leading_empty_lines(): void
    {
        $subject = "\n\nTEST_KEY=value\n\n";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals(['TEST_KEY' => 'value'], $result);
    }

    /** @test */
    public function it_handles_whitespace_only_lines(): void
    {
        $subject = "TEST_KEY=value1\n   \nTEST_VALUE=value2";

        $result = Phpdotenv::parseFromString($subject);

        $this->assertEquals([
            'TEST_KEY' => 'value1',
            'TEST_VALUE' => 'value2'
        ], $result);
    }
}