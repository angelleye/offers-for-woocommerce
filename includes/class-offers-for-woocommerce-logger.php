<?php

/**
 * Allows log files to be written to for debugging purposes.
 *
 * @class       Angelleye_Offers_For_Woocommerce_Logger
 * @version	1.0.0
 * @package	offers-for-woocommerce/includes
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class Angelleye_Offers_For_Woocommerce_Logger {

    /**
     * @var array Stores open file _handles.
     * @access private
     */
    private $_handles;

    /**
     * Constructor for the logger.
     *
     * @access public
     *
     * @since 2.3.22
     *
     * @return void
     */
    public function __construct() {
        $this->_handles = array();
    }

    /**
     * Destructor.
     *
     * @access public
     *
     * @since 2.3.22
     *
     * @return void
     */
    public function __destruct() {
        foreach ($this->_handles as $handle) {
            @fclose(escapeshellarg($handle));
        }
    }

    /**
     * Open log file for writing.
     *
     * @access private
     * @param mixed $handle
     *
     * @since 2.3.22
     *
     * @return bool success
     */
    private function open($handle) {
        if (isset($this->_handles[$handle])) {
            return true;
        }

        if ($this->_handles[$handle] = @fopen($this->ofw_get_log_file_path($handle), 'a')) {
            return true;
        }

        return false;
    }

    /**
     * Add a log entry to chosen file.
     *
     * @access public
     * @param mixed $handle Get the handle key.
     * @param mixed $message Get the message.
     *
     * @since 2.3.22
     *
     * @return void
     */
    public function add($handle, $message) {
        if ($this->open($handle) && is_resource($this->_handles[$handle])) {
            $time = date_i18n('m-d-Y @ H:i:s -'); // Grab Time
            @fwrite($this->_handles[$handle], $time . " " . $message . "\n");
        }
    }

    /**
     * Clear entries from chosen file.
     *
     * @access public
     * @param mixed $handle get the file.
     *
     * @since 2.3.22
     *
     * @return void
     */
    public function clear($handle) {
        if ($this->open($handle) && is_resource($this->_handles[$handle])) {
            @ftruncate($this->_handles[$handle], 0);
        }
    }

    /**
     * Get file path
     *
     * @access public
     * @param mixed $handle Get the file handle key.
     *
     * @since 2.3.22
     *
     * @return void
     */
    public function ofw_get_log_file_path($handle) {
        return trailingslashit(OFFERS_FOR_WOOCOMMERCE_LOG_DIR) . $handle . '-' . sanitize_file_name(wp_hash($handle)) . '.log';
    }

}
