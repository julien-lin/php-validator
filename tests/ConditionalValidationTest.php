<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Validator\Validator;

/**
 * Tests pour la validation conditionnelle
 */
class ConditionalValidationTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testRequiredOnlyWhenOtherFieldPresent(): void
    {
        // Si 'type' est 'premium', alors 'license_key' est requis
        $data = ['type' => 'premium', 'license_key' => ''];
        $rules = [
            'type' => 'required|in:free,premium',
            'license_key' => 'required' // Devrait être requis si type = premium
        ];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('license_key'));
    }

    public function testRequiredOnlyWhenOtherFieldHasValue(): void
    {
        $data = ['has_address' => 'yes', 'address' => ''];
        $rules = [
            'has_address' => 'required|in:yes,no',
            'address' => 'required' // Devrait être requis si has_address = yes
        ];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
    }

    public function testConditionalValidationWithFilled(): void
    {
        // Si le champ est présent, il doit être rempli
        $data = ['optional_field' => ''];
        $rules = ['optional_field' => 'filled'];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
    }

    public function testConditionalValidationWithFilledWhenMissing(): void
    {
        // Si le champ n'est pas présent, filled ne doit pas échouer
        $data = [];
        $rules = ['optional_field' => 'filled'];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result->isValid());
    }

    public function testMultipleRulesWithConditionalLogic(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ];
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result->isValid());
    }

    public function testConditionalValidationWithDifferent(): void
    {
        // Le nouveau mot de passe doit être différent de l'ancien
        $data = [
            'old_password' => 'oldsecret',
            'new_password' => 'oldsecret' // Même que l'ancien
        ];
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|different:old_password'
        ];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('new_password'));
    }

    public function testConditionalValidationWithSame(): void
    {
        // Le mot de passe doit être le même que la confirmation
        $data = [
            'password' => 'secret',
            'password_confirm' => 'different'
        ];
        $rules = [
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('password_confirm'));
    }

    public function testConditionalValidationWithBefore(): void
    {
        // La date de fin doit être avant une date limite
        $data = ['end_date' => '2024-12-31'];
        $rules = ['end_date' => 'required|date|before:2023-12-31'];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
    }

    public function testConditionalValidationWithAfter(): void
    {
        // La date de début doit être après une date de référence
        $data = ['start_date' => '2022-01-01'];
        $rules = ['start_date' => 'required|date|after:2023-01-01'];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
    }

    public function testOptionalFieldWithMultipleRules(): void
    {
        // Un champ optionnel mais s'il est présent, il doit respecter les règles
        $data = ['phone' => '123']; // Trop court
        $rules = ['phone' => 'min:10']; // Optionnel mais si présent, min 10
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
    }

    public function testOptionalFieldCanBeEmpty(): void
    {
        // Un champ optionnel peut être vide
        $data = ['phone' => ''];
        $rules = ['phone' => 'min:10']; // Optionnel
        
        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result->isValid()); // Vide = OK car pas required
    }

    public function testComplexConditionalValidation(): void
    {
        // Scénario complexe : formulaire d'inscription
        $data = [
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'age' => '25',
            'terms' => 'yes'
        ];
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'age' => 'required|numeric|between:18,100',
            'terms' => 'required|accepted'
        ];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result->isValid());
    }

    public function testComplexConditionalValidationWithErrors(): void
    {
        // Même scénario mais avec des erreurs
        $data = [
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'age' => '15',
            'terms' => 'no'
        ];
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'age' => 'required|numeric|between:18,100',
            'terms' => 'required|accepted'
        ];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('email'));
        $this->assertTrue($result->hasFieldErrors('password'));
        $this->assertTrue($result->hasFieldErrors('age'));
        $this->assertTrue($result->hasFieldErrors('terms'));
    }
}

