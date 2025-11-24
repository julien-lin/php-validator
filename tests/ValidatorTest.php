<?php

declare(strict_types=1);

namespace JulienLinard\Validator\Tests;

use PHPUnit\Framework\TestCase;
use JulienLinard\Validator\Validator;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testRequiredRule(): void
    {
        $data = ['name' => ''];
        $rules = ['name' => 'required'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('name'));
    }

    public function testEmailRule(): void
    {
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'email'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('email'));
    }

    public function testValidEmail(): void
    {
        $data = ['email' => 'test@example.com'];
        $rules = ['email' => 'email'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertTrue($result->isValid());
    }

    public function testMinRule(): void
    {
        $data = ['password' => '123'];
        $rules = ['password' => 'min:5'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertFalse($result->isValid());
        $this->assertTrue($result->hasFieldErrors('password'));
    }

    public function testMaxRule(): void
    {
        $data = ['title' => str_repeat('a', 101)];
        $rules = ['title' => 'max:100'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertFalse($result->isValid());
    }

    public function testMultipleRules(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertTrue($result->isValid());
        $this->assertEquals('test@example.com', $result->getValidatedValue('email'));
    }

    public function testCustomMessages(): void
    {
        $data = ['email' => 'invalid'];
        $rules = ['email' => 'email'];
        
        $this->validator->setCustomMessages([
            'email.email' => 'Email invalide personnalisé'
        ]);
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertEquals('Email invalide personnalisé', $result->getFirstError('email'));
    }

    public function testNumericRule(): void
    {
        $data = ['age' => 'not-a-number'];
        $rules = ['age' => 'numeric'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertFalse($result->isValid());
    }

    public function testUrlRule(): void
    {
        $data = ['website' => 'not-a-url'];
        $rules = ['website' => 'url'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertFalse($result->isValid());
    }

    public function testInRule(): void
    {
        $data = ['status' => 'invalid'];
        $rules = ['status' => 'in:active,inactive'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertFalse($result->isValid());
    }

    public function testValidInRule(): void
    {
        $data = ['status' => 'active'];
        $rules = ['status' => 'in:active,inactive'];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertTrue($result->isValid());
    }

    public function testSanitization(): void
    {
        $data = ['name' => '  <script>alert("xss")</script>  '];
        $rules = ['name' => 'required'];
        
        $result = $this->validator->validate($data, $rules);
        
        $validated = $result->getValidatedValue('name');
        $this->assertStringNotContainsString('<script>', $validated);
        $this->assertStringNotContainsString('  ', $validated);
    }

    public function testGetValidatedData(): void
    {
        $data = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ];
        $rules = [
            'email' => 'required|email',
            'name' => 'required',
        ];
        
        $result = $this->validator->validate($data, $rules);
        
        $this->assertTrue($result->isValid());
        $validated = $result->getValidated();
        $this->assertArrayHasKey('email', $validated);
        $this->assertArrayHasKey('name', $validated);
    }
}

