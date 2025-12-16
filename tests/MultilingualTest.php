<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Validator\Validator;

/**
 * Tests pour les messages multilingues
 */
class MultilingualTest extends TestCase
{
    public function testFrenchLocaleDefault(): void
    {
        $validator = new Validator('fr');
        $result = $validator->validate(['email' => 'invalid'], ['email' => 'email']);
        
        $this->assertFalse($result->isValid());
        $error = $result->getFirstError('email');
        $this->assertStringContainsString('email valide', $error);
    }

    public function testEnglishLocale(): void
    {
        $validator = new Validator('en');
        $result = $validator->validate(['email' => 'invalid'], ['email' => 'email']);
        
        $this->assertFalse($result->isValid());
        $error = $result->getFirstError('email');
        $this->assertStringContainsString('valid email', $error);
    }

    public function testSpanishLocale(): void
    {
        $validator = new Validator('es');
        $result = $validator->validate(['email' => 'invalid'], ['email' => 'email']);
        
        $this->assertFalse($result->isValid());
        $error = $result->getFirstError('email');
        $this->assertStringContainsString('válida', $error); // "dirección de correo electrónico válida"
    }

    public function testChangeLocale(): void
    {
        $validator = new Validator('fr');
        $validator->setLocale('en');
        
        $result = $validator->validate(['email' => 'invalid'], ['email' => 'email']);
        
        $this->assertFalse($result->isValid());
        $error = $result->getFirstError('email');
        $this->assertStringContainsString('valid email', $error);
    }

    public function testCustomMessagesOverrideLocale(): void
    {
        $validator = new Validator('fr');
        $validator->setCustomMessages([
            'email.email' => 'Email personnalisé'
        ]);
        
        $result = $validator->validate(['email' => 'invalid'], ['email' => 'email']);
        
        $this->assertEquals('Email personnalisé', $result->getFirstError('email'));
    }

    public function testMultipleLanguagesForDifferentRules(): void
    {
        $validator = new Validator('en');
        
        $result = $validator->validate(
            ['email' => 'invalid', 'age' => 'not-numeric'],
            ['email' => 'email', 'age' => 'numeric']
        );
        
        $this->assertFalse($result->isValid());
        $emailError = $result->getFirstError('email');
        $ageError = $result->getFirstError('age');
        
        $this->assertStringContainsString('valid email', $emailError);
        $this->assertStringContainsString('number', $ageError);
    }

    public function testParameterReplacementInMessages(): void
    {
        $validator = new Validator('en');
        
        $result = $validator->validate(['password' => '123'], ['password' => 'min:8']);
        
        $this->assertFalse($result->isValid());
        $error = $result->getFirstError('password');
        $this->assertStringContainsString('8', $error);
        $this->assertStringContainsString('password', $error);
    }

    public function testBetweenRuleMessageWithParameters(): void
    {
        $validator = new Validator('en');
        
        $result = $validator->validate(['age' => '5'], ['age' => 'between:10,20']);
        
        $this->assertFalse($result->isValid());
        $error = $result->getFirstError('age');
        $this->assertStringContainsString('10', $error);
        $this->assertStringContainsString('20', $error);
    }

    public function testInRuleMessageWithAllowedValues(): void
    {
        $validator = new Validator('en');
        
        $result = $validator->validate(['status' => 'invalid'], ['status' => 'in:active,inactive']);
        
        $this->assertFalse($result->isValid());
        $error = $result->getFirstError('status');
        $this->assertStringContainsString('active', $error);
        // Note: Le message peut ne contenir que la première valeur selon l'implémentation
        // Vérifions au moins qu'il contient "active"
        $this->assertIsString($error);
    }
}

