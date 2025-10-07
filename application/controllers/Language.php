<?php
/**
 * Language Controller
 * Handles language switching
 */

class Language extends CI_Controller {
    
    /**
     * Switch language
     */
    public function switch_language() {
        header('Content-Type: application/json');
        
        // Get language from POST or GET
        $lang = isset($_POST['lang']) ? $_POST['lang'] : (isset($_GET['lang']) ? $_GET['lang'] : null);
        
        if (!$lang) {
            echo json_encode([
                'success' => false,
                'message' => 'Language code is required'
            ]);
            return;
        }
        
        // Load language helper
        require_once APPPATH . 'helpers/language_helper.php';
        
        // Set language
        if (set_language($lang)) {
            echo json_encode([
                'success' => true,
                'message' => 'Language changed successfully',
                'language' => $lang
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid language code'
            ]);
        }
    }
}
