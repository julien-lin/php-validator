<?php

/**
 * English translations for validation error messages
 */
return [
    // Basic rules
    'required' => 'The :field field is required.',
    'email' => 'The :field field must be a valid email address.',
    'min' => 'The :field field must be at least :min characters.',
    'max' => 'The :field field may not be greater than :max characters.',
    'numeric' => 'The :field field must be a number.',
    'url' => 'The :field field must be a valid URL.',
    'in' => 'The :field field must be one of the following: :allowed.',
    'pattern' => 'The :field field does not match the required format.',
    
    // New rules
    'date' => 'The :field field must be a valid date.',
    'boolean' => 'The :field field must be a boolean (true/false, 1/0, yes/no).',
    'between' => 'The :field field must be between :min and :max.',
    'file' => 'The :field field must be a valid file.',
    'image' => 'The :field field must be a valid image.',
    'size' => 'The :field field must have a size of :size.',
    
    // Advanced rules
    'alpha' => 'The :field field may only contain letters.',
    'alpha_num' => 'The :field field may only contain letters and numbers.',
    'alpha_dash' => 'The :field field may only contain letters, numbers, dashes and underscores.',
    'confirmed' => 'The :field field confirmation does not match.',
    'ip' => 'The :field field must be a valid IP address.',
    'ipv4' => 'The :field field must be a valid IPv4 address.',
    'ipv6' => 'The :field field must be a valid IPv6 address.',
    'json' => 'The :field field must be a valid JSON string.',
    'uuid' => 'The :field field must be a valid UUID.',
    'accepted' => 'The :field field must be accepted.',
    'filled' => 'The :field field must have a value.',
    'before' => 'The :field field must be a date before :value.',
    'after' => 'The :field field must be a date after :value.',
    'different' => 'The :field field must be different from :other.',
    'same' => 'The :field field must match :other.',
];

