<?php

namespace Tests\Unit;

use Tests\TestCase;
use Zerotoprod\Phpdotenv\Phpdotenv;

class ParseTest extends TestCase
{
    /** @test */
    public function it_parses_simple_key_value_pairs(): void
    {
        $lines = ['TEST_KEY=test_value'];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['TEST_KEY' => 'test_value'], $result);
    }

    /** @test */
    public function it_skips_comment_lines(): void
    {
        $lines = [
            '# This is a comment',
            'TEST_KEY=test_value',
            '# Another comment'
        ];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['TEST_KEY' => 'test_value'], $result);
    }

    /** @test */
    public function it_strips_double_quotes_from_values(): void
    {
        $lines = ['QUOTED_DOUBLE="quoted value"'];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['QUOTED_DOUBLE' => 'quoted value'], $result);
    }

    /** @test */
    public function it_strips_single_quotes_from_values(): void
    {
        $lines = ["QUOTED_SINGLE='single quoted'"];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['QUOTED_SINGLE' => 'single quoted'], $result);
    }

    /** @test */
    public function it_trims_whitespace_from_keys_and_values(): void
    {
        $lines = ['  WITH_SPACES  =  value with trim  '];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['WITH_SPACES' => 'value with trim'], $result);
    }

    /** @test */
    public function it_handles_unquoted_values(): void
    {
        $lines = ['UNQUOTED=plain_value'];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['UNQUOTED' => 'plain_value'], $result);
    }

    /** @test */
    public function it_handles_empty_lines(): void
    {
        $lines = [
            'TEST_KEY=value1',
            '',
            '',
            'TEST_VALUE=value2'
        ];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals([
            'TEST_KEY' => 'value1',
            'TEST_VALUE' => 'value2'
        ], $result);
    }

    /** @test */
    public function it_handles_lines_without_equals_sign(): void
    {
        $lines = [
            'VALID_KEY=value',
            'INVALID_LINE_NO_EQUALS',
            'ANOTHER_KEY=another_value'
        ];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals([
            'VALID_KEY' => 'value',
            'ANOTHER_KEY' => 'another_value'
        ], $result);
    }

    /** @test */
    public function it_handles_values_with_equals_signs(): void
    {
        $lines = ['MULTILINE_KEY=value=with=equals'];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['MULTILINE_KEY' => 'value=with=equals'], $result);
    }

    /** @test */
    public function it_handles_empty_values(): void
    {
        $lines = ['TEST_KEY='];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['TEST_KEY' => ''], $result);
    }

    /** @test */
    public function it_handles_mixed_quote_styles_in_value(): void
    {
        $lines = ["TEST_KEY=\"value with 'single' quotes inside\""];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['TEST_KEY' => "value with 'single' quotes inside"], $result);
    }

    /** @test */
    public function it_parses_multiple_variables(): void
    {
        $lines = [
            'APP_NAME=MyApp',
            'APP_ENV=production',
            'APP_DEBUG=false',
            'DATABASE_URL=mysql://localhost/db'
        ];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals([
            'APP_NAME' => 'MyApp',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'DATABASE_URL' => 'mysql://localhost/db'
        ], $result);
    }

    /** @test */
    public function it_returns_empty_array_for_empty_input(): void
    {
        $result = Phpdotenv::parse([]);

        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_overwrites_duplicate_keys_with_last_value(): void
    {
        $lines = [
            'DUPLICATE_KEY=first_value',
            'DUPLICATE_KEY=second_value'
        ];

        $result = Phpdotenv::parse($lines);

        $this->assertEquals(['DUPLICATE_KEY' => 'second_value'], $result);
    }
}