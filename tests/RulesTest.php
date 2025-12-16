<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Validator\Validator;

/**
 * Tests complets pour toutes les règles de validation
 */
class RulesTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    // ========== REQUIRED ==========
    
    public function testRequiredRuleFailsWhenEmpty(): void
    {
        $result = $this->validator->validate(['name' => ''], ['name' => 'required']);
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('name'));
    }

    public function testRequiredRuleFailsWhenNull(): void
    {
        $result = $this->validator->validate(['name' => null], ['name' => 'required']);
        $this->assertFalse($result->isValid());
    }

    public function testRequiredRuleFailsWhenMissing(): void
    {
        $result = $this->validator->validate([], ['name' => 'required']);
        $this->assertFalse($result->isValid());
    }

    public function testRequiredRulePassesWhenPresent(): void
    {
        $result = $this->validator->validate(['name' => 'John'], ['name' => 'required']);
        $this->assertTrue($result->isValid());
    }

    // ========== EMAIL ==========
    
    public function testEmailRuleFailsWithInvalidEmail(): void
    {
        $result = $this->validator->validate(['email' => 'invalid'], ['email' => 'email']);
        $this->assertFalse($result->isValid());
    }

    public function testEmailRulePassesWithValidEmail(): void
    {
        $result = $this->validator->validate(['email' => 'test@example.com'], ['email' => 'email']);
        $this->assertTrue($result->isValid());
    }

    public function testEmailRulePassesWhenEmpty(): void
    {
        $result = $this->validator->validate(['email' => ''], ['email' => 'email']);
        $this->assertTrue($result->isValid()); // Email n'est pas required par défaut
    }

    // ========== MIN ==========
    
    public function testMinRuleFailsWhenTooShort(): void
    {
        $result = $this->validator->validate(['password' => '123'], ['password' => 'min:5']);
        $this->assertFalse($result->isValid());
    }

    public function testMinRulePassesWhenLongEnough(): void
    {
        $result = $this->validator->validate(['password' => '12345'], ['password' => 'min:5']);
        $this->assertTrue($result->isValid());
    }

    public function testMinRulePassesWhenEmpty(): void
    {
        $result = $this->validator->validate(['password' => ''], ['password' => 'min:5']);
        $this->assertTrue($result->isValid()); // Min n'est pas required par défaut
    }

    // ========== MAX ==========
    
    public function testMaxRuleFailsWhenTooLong(): void
    {
        $result = $this->validator->validate(['title' => str_repeat('a', 101)], ['title' => 'max:100']);
        $this->assertFalse($result->isValid());
    }

    public function testMaxRulePassesWhenShortEnough(): void
    {
        $result = $this->validator->validate(['title' => str_repeat('a', 100)], ['title' => 'max:100']);
        $this->assertTrue($result->isValid());
    }

    // ========== NUMERIC ==========
    
    public function testNumericRuleFailsWithNonNumeric(): void
    {
        $result = $this->validator->validate(['age' => 'not-a-number'], ['age' => 'numeric']);
        $this->assertFalse($result->isValid());
    }

    public function testNumericRulePassesWithNumeric(): void
    {
        $result = $this->validator->validate(['age' => '123'], ['age' => 'numeric']);
        $this->assertTrue($result->isValid());
    }

    public function testNumericRulePassesWithInteger(): void
    {
        $result = $this->validator->validate(['age' => 123], ['age' => 'numeric']);
        $this->assertTrue($result->isValid());
    }

    public function testNumericRulePassesWithFloat(): void
    {
        $result = $this->validator->validate(['price' => 12.5], ['price' => 'numeric']);
        $this->assertTrue($result->isValid());
    }

    // ========== URL ==========
    
    public function testUrlRuleFailsWithInvalidUrl(): void
    {
        $result = $this->validator->validate(['website' => 'not-a-url'], ['website' => 'url']);
        $this->assertFalse($result->isValid());
    }

    public function testUrlRulePassesWithValidUrl(): void
    {
        $result = $this->validator->validate(['website' => 'https://example.com'], ['website' => 'url']);
        $this->assertTrue($result->isValid());
    }

    public function testUrlRulePassesWithHttpUrl(): void
    {
        $result = $this->validator->validate(['website' => 'http://example.com'], ['website' => 'url']);
        $this->assertTrue($result->isValid());
    }

    // ========== IN ==========
    
    public function testInRuleFailsWithInvalidValue(): void
    {
        $result = $this->validator->validate(['status' => 'invalid'], ['status' => 'in:active,inactive']);
        $this->assertFalse($result->isValid());
    }

    public function testInRulePassesWithValidValue(): void
    {
        $result = $this->validator->validate(['status' => 'active'], ['status' => 'in:active,inactive']);
        $this->assertTrue($result->isValid());
    }

    public function testInRuleWithMultipleValues(): void
    {
        $result = $this->validator->validate(['color' => 'red'], ['color' => 'in:red,green,blue']);
        $this->assertTrue($result->isValid());
    }

    // ========== PATTERN ==========
    
    public function testPatternRuleFailsWhenNotMatching(): void
    {
        $result = $this->validator->validate(['code' => 'ABC'], ['code' => 'pattern:/^\d+$/']);
        $this->assertFalse($result->isValid());
    }

    public function testPatternRulePassesWhenMatching(): void
    {
        $result = $this->validator->validate(['code' => '123'], ['code' => 'pattern:/^\d+$/']);
        $this->assertTrue($result->isValid());
    }

    // ========== DATE ==========
    
    public function testDateRuleFailsWithInvalidDate(): void
    {
        $result = $this->validator->validate(['birthday' => 'invalid-date'], ['birthday' => 'date']);
        $this->assertFalse($result->isValid());
    }

    public function testDateRulePassesWithValidDate(): void
    {
        $result = $this->validator->validate(['birthday' => '2023-01-15'], ['birthday' => 'date']);
        $this->assertTrue($result->isValid());
    }

    public function testDateRuleWithCustomFormat(): void
    {
        $result = $this->validator->validate(['date' => '15/01/2023'], ['date' => 'date:d/m/Y']);
        $this->assertTrue($result->isValid());
    }

    public function testDateRuleFailsWithWrongFormat(): void
    {
        $result = $this->validator->validate(['date' => '2023-01-15'], ['date' => 'date:d/m/Y']);
        $this->assertFalse($result->isValid());
    }

    // ========== BOOLEAN ==========
    
    public function testBooleanRuleFailsWithNonBoolean(): void
    {
        $result = $this->validator->validate(['active' => 'maybe'], ['active' => 'boolean']);
        $this->assertFalse($result->isValid());
    }

    public function testBooleanRulePassesWithTrue(): void
    {
        $result = $this->validator->validate(['active' => true], ['active' => 'boolean']);
        $this->assertTrue($result->isValid());
    }

    public function testBooleanRulePassesWithFalse(): void
    {
        $result = $this->validator->validate(['active' => false], ['active' => 'boolean']);
        $this->assertTrue($result->isValid());
    }

    public function testBooleanRulePassesWithStringTrue(): void
    {
        $result = $this->validator->validate(['active' => '1'], ['active' => 'boolean']);
        $this->assertTrue($result->isValid());
    }

    public function testBooleanRulePassesWithStringFalse(): void
    {
        $result = $this->validator->validate(['active' => '0'], ['active' => 'boolean']);
        $this->assertTrue($result->isValid());
    }

    // ========== BETWEEN ==========
    
    public function testBetweenRuleFailsWhenTooSmall(): void
    {
        $result = $this->validator->validate(['age' => '5'], ['age' => 'between:10,20']);
        $this->assertFalse($result->isValid());
    }

    public function testBetweenRuleFailsWhenTooLarge(): void
    {
        $result = $this->validator->validate(['age' => '25'], ['age' => 'between:10,20']);
        $this->assertFalse($result->isValid());
    }

    public function testBetweenRulePassesWhenInRange(): void
    {
        $result = $this->validator->validate(['age' => '15'], ['age' => 'between:10,20']);
        $this->assertTrue($result->isValid());
    }

    public function testBetweenRulePassesAtMinBoundary(): void
    {
        $result = $this->validator->validate(['age' => '10'], ['age' => 'between:10,20']);
        $this->assertTrue($result->isValid());
    }

    public function testBetweenRulePassesAtMaxBoundary(): void
    {
        $result = $this->validator->validate(['age' => '20'], ['age' => 'between:10,20']);
        $this->assertTrue($result->isValid());
    }

    // ========== ALPHA ==========
    
    public function testAlphaRuleFailsWithNumbers(): void
    {
        $result = $this->validator->validate(['name' => 'John123'], ['name' => 'alpha']);
        $this->assertFalse($result->isValid());
    }

    public function testAlphaRulePassesWithLetters(): void
    {
        $result = $this->validator->validate(['name' => 'John'], ['name' => 'alpha']);
        $this->assertTrue($result->isValid());
    }

    public function testAlphaRulePassesWithAccentedLetters(): void
    {
        $result = $this->validator->validate(['name' => 'José'], ['name' => 'alpha']);
        $this->assertTrue($result->isValid());
    }

    public function testAlphaRulePassesWithSpaces(): void
    {
        $result = $this->validator->validate(['name' => 'John Doe'], ['name' => 'alpha']);
        $this->assertTrue($result->isValid());
    }

    // ========== ALPHA_NUM ==========
    
    public function testAlphaNumRuleFailsWithSpecialChars(): void
    {
        $result = $this->validator->validate(['username' => 'user-name'], ['username' => 'alpha_num']);
        $this->assertFalse($result->isValid());
    }

    public function testAlphaNumRulePassesWithLettersAndNumbers(): void
    {
        $result = $this->validator->validate(['username' => 'user123'], ['username' => 'alpha_num']);
        $this->assertTrue($result->isValid());
    }

    // ========== ALPHA_DASH ==========
    
    public function testAlphaDashRuleFailsWithSpecialChars(): void
    {
        $result = $this->validator->validate(['slug' => 'my slug'], ['slug' => 'alpha_dash']);
        $this->assertFalse($result->isValid());
    }

    public function testAlphaDashRulePassesWithAllowedChars(): void
    {
        $result = $this->validator->validate(['slug' => 'my-slug_123'], ['slug' => 'alpha_dash']);
        $this->assertTrue($result->isValid());
    }

    // ========== CONFIRMED ==========
    
    public function testConfirmedRuleFailsWhenNotConfirmed(): void
    {
        $result = $this->validator->validate(
            ['password' => 'secret', 'password_confirmation' => 'different'],
            ['password' => 'confirmed']
        );
        $this->assertFalse($result->isValid());
    }

    public function testConfirmedRulePassesWhenConfirmed(): void
    {
        $result = $this->validator->validate(
            ['password' => 'secret', 'password_confirmation' => 'secret'],
            ['password' => 'confirmed']
        );
        $this->assertTrue($result->isValid());
    }

    // ========== IP ==========
    
    public function testIpRuleFailsWithInvalidIp(): void
    {
        $result = $this->validator->validate(['ip' => '999.999.999.999'], ['ip' => 'ip']);
        $this->assertFalse($result->isValid());
    }

    public function testIpRulePassesWithValidIpv4(): void
    {
        $result = $this->validator->validate(['ip' => '192.168.1.1'], ['ip' => 'ip']);
        $this->assertTrue($result->isValid());
    }

    public function testIpRulePassesWithValidIpv6(): void
    {
        $result = $this->validator->validate(['ip' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'], ['ip' => 'ip']);
        $this->assertTrue($result->isValid());
    }

    // ========== IPV4 ==========
    
    public function testIpv4RuleFailsWithIpv6(): void
    {
        $result = $this->validator->validate(['ip' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'], ['ip' => 'ipv4']);
        $this->assertFalse($result->isValid());
    }

    public function testIpv4RulePassesWithValidIpv4(): void
    {
        $result = $this->validator->validate(['ip' => '192.168.1.1'], ['ip' => 'ipv4']);
        $this->assertTrue($result->isValid());
    }

    // ========== IPV6 ==========
    
    public function testIpv6RuleFailsWithIpv4(): void
    {
        $result = $this->validator->validate(['ip' => '192.168.1.1'], ['ip' => 'ipv6']);
        $this->assertFalse($result->isValid());
    }

    public function testIpv6RulePassesWithValidIpv6(): void
    {
        $result = $this->validator->validate(['ip' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'], ['ip' => 'ipv6']);
        $this->assertTrue($result->isValid());
    }

    // ========== JSON ==========
    
    public function testJsonRuleFailsWithInvalidJson(): void
    {
        $result = $this->validator->validate(['data' => 'not json'], ['data' => 'json']);
        $this->assertFalse($result->isValid());
    }

    public function testJsonRulePassesWithValidJson(): void
    {
        // Désactiver la sanitization pour éviter l'échappement des guillemets
        $this->validator->setSanitize(false);
        $result = $this->validator->validate(['data' => '{"key":"value"}'], ['data' => 'json']);
        $this->assertTrue($result->isValid());
    }

    public function testJsonRulePassesWithValidJsonArray(): void
    {
        $this->validator->setSanitize(false);
        $result = $this->validator->validate(['data' => '[1,2,3]'], ['data' => 'json']);
        $this->assertTrue($result->isValid());
    }

    public function testJsonRulePassesWithValidJsonNumber(): void
    {
        $this->validator->setSanitize(false);
        $result = $this->validator->validate(['data' => '123'], ['data' => 'json']);
        $this->assertTrue($result->isValid());
    }

    // ========== UUID ==========
    
    public function testUuidRuleFailsWithInvalidUuid(): void
    {
        $result = $this->validator->validate(['id' => 'not-a-uuid'], ['id' => 'uuid']);
        $this->assertFalse($result->isValid());
    }

    public function testUuidRulePassesWithValidUuid(): void
    {
        $result = $this->validator->validate(['id' => '550e8400-e29b-41d4-a716-446655440000'], ['id' => 'uuid']);
        $this->assertTrue($result->isValid());
    }

    // ========== ACCEPTED ==========
    
    public function testAcceptedRuleFailsWithInvalidValue(): void
    {
        $result = $this->validator->validate(['terms' => 'no'], ['terms' => 'accepted']);
        $this->assertFalse($result->isValid());
    }

    public function testAcceptedRulePassesWithYes(): void
    {
        $result = $this->validator->validate(['terms' => 'yes'], ['terms' => 'accepted']);
        $this->assertTrue($result->isValid());
    }

    public function testAcceptedRulePassesWithOn(): void
    {
        $result = $this->validator->validate(['terms' => 'on'], ['terms' => 'accepted']);
        $this->assertTrue($result->isValid());
    }

    public function testAcceptedRulePassesWithTrue(): void
    {
        $result = $this->validator->validate(['terms' => true], ['terms' => 'accepted']);
        $this->assertTrue($result->isValid());
    }

    public function testAcceptedRulePassesWithOne(): void
    {
        $result = $this->validator->validate(['terms' => '1'], ['terms' => 'accepted']);
        $this->assertTrue($result->isValid());
    }

    // ========== FILLED ==========
    
    public function testFilledRulePassesWhenMissing(): void
    {
        $result = $this->validator->validate([], ['name' => 'filled']);
        $this->assertTrue($result->isValid()); // filled n'est pas required
    }

    public function testFilledRuleFailsWhenEmpty(): void
    {
        $result = $this->validator->validate(['name' => ''], ['name' => 'filled']);
        $this->assertFalse($result->isValid());
    }

    public function testFilledRulePassesWhenFilled(): void
    {
        $result = $this->validator->validate(['name' => 'John'], ['name' => 'filled']);
        $this->assertTrue($result->isValid());
    }

    // ========== BEFORE ==========
    
    public function testBeforeRuleFailsWhenAfter(): void
    {
        $result = $this->validator->validate(['date' => '2024-01-15'], ['date' => 'before:2023-01-01']);
        $this->assertFalse($result->isValid());
    }

    public function testBeforeRulePassesWhenBefore(): void
    {
        $result = $this->validator->validate(['date' => '2022-01-15'], ['date' => 'before:2023-01-01']);
        $this->assertTrue($result->isValid());
    }

    // ========== AFTER ==========
    
    public function testAfterRuleFailsWhenBefore(): void
    {
        $result = $this->validator->validate(['date' => '2022-01-15'], ['date' => 'after:2023-01-01']);
        $this->assertFalse($result->isValid());
    }

    public function testAfterRulePassesWhenAfter(): void
    {
        $result = $this->validator->validate(['date' => '2024-01-15'], ['date' => 'after:2023-01-01']);
        $this->assertTrue($result->isValid());
    }

    // ========== DIFFERENT ==========
    
    public function testDifferentRuleFailsWhenSame(): void
    {
        $result = $this->validator->validate(
            ['password' => 'secret', 'old_password' => 'secret'],
            ['password' => 'different:old_password']
        );
        $this->assertFalse($result->isValid());
    }

    public function testDifferentRulePassesWhenDifferent(): void
    {
        $result = $this->validator->validate(
            ['password' => 'secret', 'old_password' => 'old'],
            ['password' => 'different:old_password']
        );
        $this->assertTrue($result->isValid());
    }

    // ========== SAME ==========
    
    public function testSameRuleFailsWhenDifferent(): void
    {
        $result = $this->validator->validate(
            ['password' => 'secret', 'password_confirm' => 'different'],
            ['password' => 'same:password_confirm']
        );
        $this->assertFalse($result->isValid());
    }

    public function testSameRulePassesWhenSame(): void
    {
        $result = $this->validator->validate(
            ['password' => 'secret', 'password_confirm' => 'secret'],
            ['password' => 'same:password_confirm']
        );
        $this->assertTrue($result->isValid());
    }

    // ========== SIZE ==========
    
    public function testSizeRuleFailsWhenWrongSize(): void
    {
        $result = $this->validator->validate(['code' => '123'], ['code' => 'size:5']);
        $this->assertFalse($result->isValid());
    }

    public function testSizeRulePassesWhenCorrectSize(): void
    {
        $result = $this->validator->validate(['code' => '12345'], ['code' => 'size:5']);
        $this->assertTrue($result->isValid());
    }
}

