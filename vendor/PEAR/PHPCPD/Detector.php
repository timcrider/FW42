<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phpcpd
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

/**
 * PHPCPD code analyser.
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: 1.3.5
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class PHPCPD_Detector
{
    /**
     * @var PHPCPD_Detector_Strategy
     */
    protected $strategy;

    /**
     * @var ezcConsoleOutput
     */
    protected $output;

    /**
     * Constructor.
     *
     * @param PHPCPD_Detector_Strategy $strategy
     * @param ezcConsoleOutput         $output
     * @since Method available since Release 1.3.0
     */
    public function __construct(PHPCPD_Detector_Strategy $strategy, ezcConsoleOutput $output = NULL)
    {
        $this->strategy = $strategy;
        $this->output   = $output;
    }

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param  Iterator|array   $files     List of files to process
     * @param  integer          $minLines  Minimum number of identical lines
     * @param  integer          $minTokens Minimum number of identical tokens
     * @return PHPCPD_CloneMap  Map of exact clones found in the list of files
     */
    public function copyPasteDetection($files, $minLines = 5, $minTokens = 70)
    {
        $result = new PHPCPD_CloneMap;

        if ($this->output !== NULL) {
            $bar = new ezcConsoleProgressbar($this->output, count($files));
            print "Processing files\n";
        }

        foreach ($files as $file) {
            $this->strategy->processFile($file, $minLines, $minTokens, $result);

            if ($this->output !== NULL) {
                $bar->advance();
            }
        }

        if ($this->output !== NULL) {
            print "\n\n";
        }

        return $result;
    }
}
