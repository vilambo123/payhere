<?php
/**
 * Language Helper
 * Handles multi-language support for the application
 */

/**
 * Get current language
 * 
 * @return string Current language code (en, my, zh)
 */
function get_current_language() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check session first
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }
    
    // Check cookie
    if (isset($_COOKIE['language'])) {
        $_SESSION['language'] = $_COOKIE['language'];
        return $_COOKIE['language'];
    }
    
    // Default to English
    return 'en';
}

/**
 * Set current language
 * 
 * @param string $lang Language code
 * @return bool Success status
 */
function set_language($lang) {
    $allowed_languages = ['en', 'my', 'zh'];
    
    if (!in_array($lang, $allowed_languages)) {
        return false;
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['language'] = $lang;
    
    // Set cookie for 30 days
    setcookie('language', $lang, time() + (30 * 24 * 60 * 60), '/');
    
    return true;
}

/**
 * Load language file
 * 
 * @param string $lang Language code
 * @return array Language translations
 */
function load_language($lang = null) {
    if ($lang === null) {
        $lang = get_current_language();
    }
    
    $language_file = APPPATH . "language/{$lang}.php";
    
    if (!file_exists($language_file)) {
        $language_file = APPPATH . "language/en.php";
    }
    
    return require $language_file;
}

/**
 * Get translation
 * 
 * @param string $key Translation key
 * @param array $params Parameters for replacement
 * @return string Translated text
 */
function lang($key, $params = []) {
    static $translations = null;
    
    if ($translations === null) {
        $translations = load_language();
    }
    
    $text = isset($translations[$key]) ? $translations[$key] : $key;
    
    // Replace parameters
    if (!empty($params)) {
        foreach ($params as $param_key => $param_value) {
            $text = str_replace('{' . $param_key . '}', $param_value, $text);
        }
    }
    
    return $text;
}

/**
 * Get all available languages
 * 
 * @return array Language list
 */
function get_available_languages() {
    return [
        'en' => [
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'flag' => '🇺🇸'
        ],
        'my' => [
            'code' => 'my',
            'name' => 'Malay',
            'native_name' => 'Bahasa Melayu',
            'flag' => '🇲🇾'
        ],
        'zh' => [
            'code' => 'zh',
            'name' => 'Chinese',
            'native_name' => '简体中文',
            'flag' => '🇨🇳'
        ]
    ];
}

/**
 * Get language name
 * 
 * @param string $code Language code
 * @param bool $native Return native name
 * @return string Language name
 */
function get_language_name($code, $native = false) {
    $languages = get_available_languages();
    
    if (isset($languages[$code])) {
        return $native ? $languages[$code]['native_name'] : $languages[$code]['name'];
    }
    
    return $code;
}

/**
 * Translate and echo
 * 
 * @param string $key Translation key
 * @param array $params Parameters for replacement
 */
function _e($key, $params = []) {
    echo lang($key, $params);
}

/**
 * Get language JSON for JavaScript
 * 
 * @param array $keys Keys to include (null for all)
 * @return string JSON encoded translations
 */
function get_language_json($keys = null) {
    $translations = load_language();
    
    if ($keys !== null) {
        $filtered = [];
        foreach ($keys as $key) {
            if (isset($translations[$key])) {
                $filtered[$key] = $translations[$key];
            }
        }
        $translations = $filtered;
    }
    
    return json_encode($translations);
}
