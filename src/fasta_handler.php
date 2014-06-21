<?php
/**
 * FASTA Handler Class
 *
 * Class for handling/processing files in FASTA format. Caller should 
 * open/close file pointers as necessary.
 *
 * PHP version 5
 *
 * @author Jake Crawford <jjc2718@gmail.com>
 */

class FASTA_Handler {

    public $output;
    public $labels = array();
    public $b_store_labels = false;
    public $b_split_string = false;
    protected $fp;
    protected $separator;


    /**
     * Initialize class with a file pointer
     *
     * @param resource $fp        A file pointer to a file in FASTA format
     * @param string   $separator A character separating labels and sequences
     *
     * @return void
     */
    public function __construct($fp, $separator = '>') {
        $this->fp = $fp;
        $this->separator = $separator;
        $this->output = array();
    }


    /**
     * Function to set format variables and generate output by calling read()
     *
     * @param bool $b_split_string Returns a string if false and an array if true
     * @param bool $b_store_labels Store sequence labels if true
     *
     * @return array|string output parsed by read()
     */
    public function get_output($b_split_string = false, $b_store_labels = false) {
        $this->b_split_string = $b_split_string;
        $this->b_store_labels = $b_store_labels;
        $this->read();
        return $this->output;
    }


    /**
     * Function to parse text file into either strings or character arrays,
     * depending on the value of b_split_string. Called from get_output.
     *
     * @return void
     */
    private function read() {
        // Get rid of the leading > for the first label
        assert(fgetc($this->fp) === $this->separator);
        // Get (the rest of) the line as a label
        while (($line = fgets($this->fp)) !== false) {
            if ($this->b_store_labels) {
                $this->labels[] = trim($line);
            }
            $c = '';
            $current_string = '';
            // Get the sequence character by character, discarding whitespace
            while ($this->separator !== ($c = fgetc($this->fp)) && !feof($this->fp)) {
                if (!ctype_space($c)) {
                    $current_string .= $c;
                }
            }
            // Split string if the member variable is true, if not return it
            $this->output[] = ($this->b_split_string) ? str_split($current_string) : $current_string;
        }
    }


    /**
     * Formats and dumps the current output for debugging
     *
     * @return void
     */
    public function dump_output() {
        if (empty($this->output) || !is_array($this->output)) {
            echo 'No output' . "\n";
        } else {
            echo "\n";
            echo "--------------\n";
            echo "| Labels:    | \n";
            echo "--------------\n";
            var_dump($this->labels);
            echo "--------------\n";
            echo "| Sequences: | \n";
            echo "--------------\n";
            var_dump($this->output);
            echo "\n";
        }
    }

}
