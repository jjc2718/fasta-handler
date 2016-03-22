import sys
import itertools

class FASTA_Handler:
    """ A simple, lightweight FASTA file parser """

    def __init__(self, filename, separator = ">"):
        """ Initialize class with the FASTA file at filename.

        Args:
            filename (string) : The filename to read from
            separator (string): The separator to identify a new sequence
                                by. Defaults to '>' (the FASTA file format
                                occasionally allows for other separators as
                                well)

        Returns: void
        """
        try:
            self.fp = open(filename, 'r')
        except IOError:
            sys.exit("Could not open file: {}".format(filename))
        self.separator = separator
        self.output = []
        self.labels = []

    def read(self, split_string = False, store_labels = False):
        """ Set format variables and generate output.

        Args:
            split_string (bool): If true, store the sequence as individual
                                 characters in self.output; if false, store
                                 as a single string.
            store_labels (bool): If true, store the label (i.e. the text
                                 following the separator); if false, store
                                 only the sequences.

        Returns:
            self.output (list): A list containing the sequences in the file
                                specified in the constructor, in the desired
                                format.
        """
        self.split_string = split_string
        self.store_labels = store_labels
        self._read()
        return self.output

    def _read(self):
        """ Parse text file and generate output """
        for label, sequence in self._yieldlines():
            if self.store_labels:
                self.labels.append(label)
            self.output.append(list(sequence.strip()) if self.split_string
                               else sequence.strip())

    def _yieldlines(self):
        """ Generator for reading (label, sequence) pairs from file """
        while True:

            # first, get the label, if there's none left we're done
            label = self.fp.readline().strip().replace(self.separator, '')
            if label == '':
                raise StopIteration

            # then, get the associated sequence, continuing until another
            # separator is encountered
            sequence = ''
            nextline = self.fp.readline()

            # save the position at the beginning of the current line - if
            # it is another label, we break the loop and back up
            last_pos = self.fp.tell()
            while (len(nextline) > 0 and nextline[0] != self.separator):
                sequence += nextline.strip()
                last_pos = self.fp.tell()
                nextline = self.fp.readline()
            self.fp.seek(last_pos)
            yield (label, sequence)


