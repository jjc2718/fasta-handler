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


    /**
     * Initialize class with a file pointer
     *
     * @param resource $fp A file pointer to a file in FASTA format
     *
     * @return void
     */
    public function __construct($fp) {
        $this->fp = $fp;
        $this->output = array();
    }


    /**
     * Function to set b_split_string and generate output by calling read()
     *
     * @return array|string output parsed by read()
     */
    public function get_output($b_split_string = false) {
        $this->b_split_string = $b_split_string;
        $this->read();
        return $this->output();
    }


    /**
     * Function to parse text file into either strings or character arrays,
     * depending on the value of b_split_string. Called from get_output.
     *
     * @return void
     */
    private function read() {
        // Get rid of the leading > for the first label
        assert(fgetc($this->fp) === '>');
        // Get (the rest of) the line as a label
        while (($line = fgets($this->fp)) !== false) {
            if ($this->b_store_labels) {
                $this->labels[] = trim($line);
            }
            $c = '';
            $current_string = '';
            // Get the sequence character by character, discarding whitespace
            while ('>' !== ($c = fgetc($this->fp)) && !feof($this->fp)) {
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
