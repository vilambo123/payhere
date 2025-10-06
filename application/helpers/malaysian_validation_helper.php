<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Malaysian Validation Helper
 * 
 * Helper functions for validating Malaysian-specific formats
 */

if (!function_exists('validate_malaysian_phone')) {
    /**
     * Validate Malaysian phone number
     * 
     * @param string $phone Phone number to validate
     * @return bool True if valid
     */
    function validate_malaysian_phone($phone) {
        // Remove spaces, dashes, and plus signs
        $cleanPhone = preg_replace('/[\s\-\+]/', '', $phone);
        
        // Malaysian phone patterns
        $patterns = [
            '/^01[0-9]{8,9}$/',         // Mobile: 01xxxxxxxx or 01xxxxxxxxx
            '/^0[2-9][0-9]{7,8}$/',     // Landline: 0xxxxxxxx or 0xxxxxxxxx
            '/^601[0-9]{8,9}$/',        // Mobile with country code (no +)
            '/^60[2-9][0-9]{7,8}$/'     // Landline with country code
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanPhone)) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('validate_malaysian_ic')) {
    /**
     * Validate Malaysian IC (MyKad) number
     * 
     * @param string $ic IC number to validate
     * @return bool True if valid
     */
    function validate_malaysian_ic($ic) {
        // Remove dashes and spaces
        $cleanIC = preg_replace('/[\s\-]/', '', $ic);
        
        // Must be exactly 12 digits
        if (!preg_match('/^[0-9]{12}$/', $cleanIC)) {
            return false;
        }
        
        // Validate date part (YYMMDD)
        $year = (int)substr($cleanIC, 0, 2);
        $month = (int)substr($cleanIC, 2, 2);
        $day = (int)substr($cleanIC, 4, 2);
        
        if ($month < 1 || $month > 12) return false;
        if ($day < 1 || $day > 31) return false;
        
        // Validate place of birth code
        $birthPlace = (int)substr($cleanIC, 6, 2);
        
        // Valid Malaysian state codes
        $validCodes = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,           // Johor, Kedah, Kelantan, Melaka, N.Sembilan, Pahang, Penang, Perak, Perlis, Selangor
            11, 12, 13, 14, 15, 16,                  // Terengganu, Sabah, Sarawak, WP KL, WP Labuan, WP Putrajaya
            21, 22, 23, 24,                          // Born outside Malaysia
            59, 60,                                   // Unknown/Special cases
            71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82  // Other categories
        ];
        
        return in_array($birthPlace, $validCodes) || ($birthPlace >= 1 && $birthPlace <= 82);
    }
}

if (!function_exists('validate_malaysian_postcode')) {
    /**
     * Validate Malaysian postcode
     * 
     * @param string $postcode Postcode to validate
     * @return bool True if valid
     */
    function validate_malaysian_postcode($postcode) {
        // Remove spaces
        $cleanPostcode = preg_replace('/\s/', '', $postcode);
        
        // Must be exactly 5 digits
        return preg_match('/^[0-9]{5}$/', $cleanPostcode) === 1;
    }
}

if (!function_exists('format_malaysian_phone')) {
    /**
     * Format Malaysian phone number for display
     * 
     * @param string $phone Phone number to format
     * @return string Formatted phone number
     */
    function format_malaysian_phone($phone) {
        $cleanPhone = preg_replace('/[\s\-\+]/', '', $phone);
        
        if (substr($cleanPhone, 0, 2) === '60') {
            // With country code
            $len = strlen($cleanPhone);
            if ($len === 11 || $len === 12) {
                return '+' . substr($cleanPhone, 0, 2) . ' ' . 
                       substr($cleanPhone, 2, 2) . '-' . 
                       substr($cleanPhone, 4, 4) . '-' . 
                       substr($cleanPhone, 8);
            }
        } elseif (substr($cleanPhone, 0, 1) === '0') {
            // Without country code
            $len = strlen($cleanPhone);
            if ($len === 10 || $len === 11) {
                return substr($cleanPhone, 0, 3) . '-' . 
                       substr($cleanPhone, 3, 4) . '-' . 
                       substr($cleanPhone, 7);
            } elseif ($len === 9) {
                return substr($cleanPhone, 0, 2) . '-' . 
                       substr($cleanPhone, 2, 4) . '-' . 
                       substr($cleanPhone, 6);
            }
        }
        
        return $phone;
    }
}

if (!function_exists('format_malaysian_ic')) {
    /**
     * Format Malaysian IC number for display
     * 
     * @param string $ic IC number to format
     * @return string Formatted IC (YYMMDD-PB-###G)
     */
    function format_malaysian_ic($ic) {
        $cleanIC = preg_replace('/[\s\-]/', '', $ic);
        
        if (strlen($cleanIC) === 12) {
            return substr($cleanIC, 0, 6) . '-' . 
                   substr($cleanIC, 6, 2) . '-' . 
                   substr($cleanIC, 8, 4);
        }
        
        return $ic;
    }
}

if (!function_exists('get_malaysian_states')) {
    /**
     * Get list of Malaysian states
     * 
     * @return array List of states
     */
    function get_malaysian_states() {
        return [
            'Johor',
            'Kedah',
            'Kelantan',
            'Melaka',
            'Negeri Sembilan',
            'Pahang',
            'Penang',
            'Perak',
            'Perlis',
            'Sabah',
            'Sarawak',
            'Selangor',
            'Terengganu',
            'WP Kuala Lumpur',
            'WP Labuan',
            'WP Putrajaya'
        ];
    }
}

if (!function_exists('validate_malaysian_name')) {
    /**
     * Validate Malaysian name format
     * 
     * @param string $name Name to validate
     * @return bool True if valid
     */
    function validate_malaysian_name($name) {
        // Allow letters, spaces, and common Malaysian name characters
        // Also allow bin, binti, a/l, a/p
        $pattern = '/^[a-zA-Z\s\.\/@-]+$/';
        
        if (!preg_match($pattern, $name)) {
            return false;
        }
        
        // Must be at least 3 characters
        if (strlen(trim($name)) < 3) {
            return false;
        }
        
        return true;
    }
}

if (!function_exists('validate_loan_amount_range')) {
    /**
     * Validate loan amount is within acceptable Malaysian range
     * 
     * @param float $amount Loan amount
     * @param string $loan_type Type of loan
     * @return array ['valid' => bool, 'message' => string]
     */
    function validate_loan_amount_range($amount, $loan_type) {
        $ranges = [
            'personal' => ['min' => 1000, 'max' => 200000],
            'business' => ['min' => 10000, 'max' => 1000000],
            'home' => ['min' => 50000, 'max' => 2000000],
            'car' => ['min' => 20000, 'max' => 500000]
        ];
        
        if (!isset($ranges[$loan_type])) {
            return ['valid' => true, 'message' => ''];
        }
        
        $range = $ranges[$loan_type];
        
        if ($amount < $range['min']) {
            return [
                'valid' => false,
                'message' => "Minimum loan amount for this type is RM " . number_format($range['min'], 0)
            ];
        }
        
        if ($amount > $range['max']) {
            return [
                'valid' => false,
                'message' => "Maximum loan amount for this type is RM " . number_format($range['max'], 0)
            ];
        }
        
        return ['valid' => true, 'message' => ''];
    }
}
