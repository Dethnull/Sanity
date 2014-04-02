<?php

    include_once "./portable-utf8.php"; // See file for licensing & author information
    include_once "./PasswordHash.php"; // see file for licensing & author information

    /**
     * Class Sanity
     *
     * The MIT License (MIT)
     *
     * Copyright (c) 2014 Dethnull
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in all
     * copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
     * SOFTWARE.
     */
    class Sanity {
        // Constants to match the named Key => value pairs for Sanity
        const MIN_LENGTH          = "MIN_LENGTH";
        const MAX_LENGTH          = "MAX_LENGTH";
        const COMPLEXITY_REQUIRED = "COMPLEXITY_REQUIRED";
        const ALLOW_WHITESPACES   = "ALLOW_WHITESPACES";
        const ALLOW_SYMBOLS       = "ALLOW_SYMBOLS";
        const ALLOW_NUMBERS       = "ALLOW_NUMBERS";
        const IGNORE_CASE         = "IGNORE_CASE";
        const UPPER_COUNT         = "UPPER";
        const LOWER_COUNT         = "LOWER";
        const NUMBER_COUNT        = "NUMBER";
        const SYMBOL_COUNT        = "SYMBOL";
        const WHITESPACES_COUNT   = "WHITESPACES";
        const REQUIRED_GROUPS     = "REQUIRED_GROUPS";
        const DISALLOWED_LIST     = "DISALLOWED";
        const DEBUG               = "DEBUG";

        // Default rules used if any rules are omitted during configuration
        private static $DEFAULT_RULES = array(
            self::MIN_LENGTH          => 8, // Ignored if < 1
            self::MAX_LENGTH          => -1, // Ignored if < 1 or < MIN_LENGTH
            self::ALLOW_WHITESPACES   => 1,
            self::ALLOW_SYMBOLS       => 1,
            self::ALLOW_NUMBERS       => 1,
            self::IGNORE_CASE         => 0, // If true forces input to all lowercase for checking
            // COMPLEXITY_REQUIRED is the amount of fields in REQUIRED_GROUPS that must be met
            // for the sanity check to be valid. Error will be thrown if this number is higher
            // than the active values in REQUIRED_GROUPS
            self::COMPLEXITY_REQUIRED => 3,
            self::REQUIRED_GROUPS     => array(
                self::UPPER_COUNT       => 2, // If any of these values are < 0, they will be ignored
                self::LOWER_COUNT       => 2, //
                self::NUMBER_COUNT      => 2,
                self::SYMBOL_COUNT      => 2,
                self::WHITESPACES_COUNT => 2,
            ),
            // This list holds an array of strings that will be checked for specific sequences
            // e.g.  "123", "abc", it will check if 123 is found sequentially
            self::DISALLOWED_LIST     => array(),
            self::DEBUG               => false,
        );
        // Rule being used when calling the Check function.
        private static $RULE_IN_USE = array();
        // Array of preconfigured rules
        private static $SAVED_RULES = array();
        // Basic array containing debug information
        private static $debug_info = array();

        // constructor is set to private to prevent instantiation from occurring. All functions are static
        private function __construct() { }


        /**
         *
         * This function configures Sanity to the rules passed. If any const values are omitted it uses the default
         *
         * Constants:
         *      Sanity::MIN_LENGTH Minimum length allowed for input. If <= 0 ignored
         *      Sanity::MAX_LENGTH Maximum length allowed for input. If <= 0 or < MIN_LENGTH ignored
         *      Sanity::COMPLEXITY_REQUIRED Required # of matches to pass check.
         *                                  Checks against REQUIRED_GROUPS. if < 0 or greater than 5 ignored
         *      Sanity::ALLOW_WHITESPACES Boolean value to reject whitespaces or not
         *      Sanity::ALLOW_SYMBOLS Boolean value to reject symbols or not
         *      Sanity::ALLOW_NUMBERS Boolean value to reject numbers or not
         *      Sanity::IGNORE_CASE Boolean value to ignore the case of the input or not. If true then forces input to
         *                          lowercase for checking
         *      Sanity::UPPER_COUNT Required # of uppercase letters to pass check. Ignored if <= 0
         *      Sanity::LOWER_COUNT Required # of lowercase letters to pass check. Ignored if <= 0
         *      Sanity::NUMBER_COUNT Required amount of #'s to pass check. Ignored if <= 0
         *      Sanity::SYMBOL_COUNT Required amount of symbols to pass check. Ignored if <= 0
         *      Sanity::WHITESPACES_COUNT Required amount of whitespaces to pass check. Ignored if <= 0
         *      Sanity::REQUIRED_GROUPS array of the groups required to pass check.
         *                              COMPLEXITY_REQUIRED checks against the size of this array.
         *                              Currently these groups are: UPPER_COUNT, LOWER_COUNT, NUMBER_COUNT, SYMBOL_COUNT, WHITESPACES_COUNT
         *      Sanity::DISALLOWED_LIST array of case-insensitive consecutive values that are not allowed in input.
         *      Sanity::DEBUG boolean if true allows use of print_debug_info()
         *
         *
         * @since 1.0
         *
         * @param array         $conf [CONST_NAME] = value
         * @param null | string $ruleName Name of the rule you are defining
         */
        public static function configure($conf = array(), $ruleName = null) {
            $backup_rules = null;
            if (isset($ruleName)) {
                $backup_rules = self::$DEFAULT_RULES; // Name is set, backup the default values

                // Name exists, so this is an update
                // Save the named configuration to the current RULES to be manipulated
                if (array_key_exists($ruleName, self::$SAVED_RULES)) {
                    self::$DEFAULT_RULES = self::$SAVED_RULES[$ruleName];
                }
            }

            if (isset($conf[self::MIN_LENGTH])) {
                self::$DEFAULT_RULES[self::MIN_LENGTH] = $conf[self::MIN_LENGTH];
            }
            if (isset($conf[self::MAX_LENGTH])) {
                self::$DEFAULT_RULES[self::MAX_LENGTH] = $conf[self::MAX_LENGTH];
            }
            if (isset($conf[self::COMPLEXITY_REQUIRED])) {
                self::$DEFAULT_RULES[self::COMPLEXITY_REQUIRED] = $conf[self::COMPLEXITY_REQUIRED];
            }
            if (isset($conf[self::ALLOW_WHITESPACES])) {
                self::$DEFAULT_RULES[self::ALLOW_WHITESPACES] = $conf[self::ALLOW_WHITESPACES];
            }
            if (isset($conf[self::ALLOW_NUMBERS])) {
                self::$DEFAULT_RULES[self::ALLOW_NUMBERS] = $conf[self::ALLOW_NUMBERS];
            }
            if (isset($conf[self::ALLOW_SYMBOLS])) {
                self::$DEFAULT_RULES[self::ALLOW_SYMBOLS] = $conf[self::ALLOW_SYMBOLS];
            }
            if (isset($conf[self::IGNORE_CASE])) {
                self::$DEFAULT_RULES[self::IGNORE_CASE] = $conf[self::IGNORE_CASE];
            }
            if (isset($conf[self::UPPER_COUNT])) {
                self::$DEFAULT_RULES[self::REQUIRED_GROUPS][self::UPPER_COUNT] = $conf[self::UPPER_COUNT];
            }
            if (isset($conf[self::LOWER_COUNT])) {
                self::$DEFAULT_RULES[self::REQUIRED_GROUPS][self::LOWER_COUNT] = $conf[self::LOWER_COUNT];
            }
            if (isset($conf[self::NUMBER_COUNT])) {
                self::$DEFAULT_RULES[self::REQUIRED_GROUPS][self::NUMBER_COUNT] = $conf[self::NUMBER_COUNT];
            }
            if (isset($conf[self::SYMBOL_COUNT])) {
                self::$DEFAULT_RULES[self::REQUIRED_GROUPS][self::SYMBOL_COUNT] = $conf[self::SYMBOL_COUNT];
            }
            if (isset($conf[self::WHITESPACES_COUNT])) {
                self::$DEFAULT_RULES[self::REQUIRED_GROUPS][self::WHITESPACES_COUNT] = $conf[self::WHITESPACES_COUNT];
            }
            if (isset($conf[self::DISALLOWED_LIST])) {
                self::$DEFAULT_RULES[self::DISALLOWED_LIST] = array_merge(self::$DEFAULT_RULES[self::DISALLOWED_LIST], $conf[self::DISALLOWED_LIST]);
            }
            if (isset($conf[self::DEBUG])) {
                self::$DEFAULT_RULES[self::DEBUG] = $conf[self::DEBUG];
            }

            if (isset($conf)) {
                if (isset($ruleName)) {
                    // Reassign the named rule to the updated values
                    if (array_key_exists($ruleName, self::$SAVED_RULES)) {
                        self::$SAVED_RULES[$ruleName] = self::$DEFAULT_RULES;
                    } else {
                        // Create a new entry in our SAVED_RULES list
                        self::$SAVED_RULES = array_merge(self::$SAVED_RULES, array($ruleName => self::$DEFAULT_RULES));
                    }
                    self::$DEFAULT_RULES = $backup_rules;
                }
            }
        }

        /**
         * This is the primary function of Sanity. It checks your input against the given ruleName, or if one is not
         * provided it uses whatever is in the DEFAULT_RULES array.
         *
         * @since 1.0
         *
         * @param string $input input string.
         * @param null   $ruleName name of the saved rule to use
         *
         * @return bool true if input passes rules, false otherwise
         */
        public static function check($input, $ruleName = null) {
            // Set the current RULE_IN_USE
            $input = trim($input);
            if (isset($ruleName)) {
                if (array_key_exists($ruleName, self::$SAVED_RULES)) {
                    self::$RULE_IN_USE = self::$SAVED_RULES[$ruleName];
                } else {
                    self::d("Rule {$ruleName} doesn't exist. Falling back to default. Check code...");
                    self::$RULE_IN_USE = self::$DEFAULT_RULES;
                }
            } else {
                self::$RULE_IN_USE = self::$DEFAULT_RULES;
            }

            $minLength       = self::$RULE_IN_USE[self::MIN_LENGTH];
            $maxLength       = self::$RULE_IN_USE[self::MAX_LENGTH];
            $complexRequired = self::$RULE_IN_USE[self::COMPLEXITY_REQUIRED];
            $allowWhiteSpace = self::$RULE_IN_USE[self::ALLOW_WHITESPACES];
            $allowSymbol     = self::$RULE_IN_USE[self::ALLOW_SYMBOLS];
            $allowNumbers    = self::$RULE_IN_USE[self::ALLOW_NUMBERS];
            $ignoreCase      = self::$RULE_IN_USE[self::IGNORE_CASE];
            $upperReq        = self::$RULE_IN_USE[self::REQUIRED_GROUPS][self::UPPER_COUNT];
            $lowerReq        = self::$RULE_IN_USE[self::REQUIRED_GROUPS][self::LOWER_COUNT];
            $numberReq       = self::$RULE_IN_USE[self::REQUIRED_GROUPS][self::NUMBER_COUNT];
            $symbolReq       = self::$RULE_IN_USE[self::REQUIRED_GROUPS][self::SYMBOL_COUNT];
            $whiteSpacesReq  = self::$RULE_IN_USE[self::REQUIRED_GROUPS][self::WHITESPACES_COUNT];
            $disallowedList  = array_values(self::$RULE_IN_USE[self::DISALLOWED_LIST]);

            // If we ignore the case then we'll set our string to all lowercase to compare
            if ($ignoreCase) {
                $inputChars = utf8_split(utf8_strtolower($input));
            } else {
                $inputChars = utf8_split($input);
            }
            $inputLen = count($inputChars);

            // == Check to make sure that minLength is set and see if $len is less than that
            if (($minLength >= 1) && ($inputLen < $minLength)) {
                self::d("Input length {$inputLen} is less than min Length {$minLength}");

                return false;
            }
            // == Its ok for maxLength to be the same as minLength, as long as it's not less
            if (($maxLength >= 1) && ($maxLength >= $minLength) && ($inputLen >= $maxLength)) {
                self::d("Input length is {$inputLen}, which greater than maxlength {$maxLength}");

                return false;
            }

            $upperCount      = 0;
            $lowerCount      = 0;
            $numberCount     = 0;
            $symbolCount     = 0;
            $whiteSpaceCount = 0;

            $caseTable = utf8_case_table();
            // We're only after the values, not what kind of whitespace it is
            $whiteSpaceTable = array_values(utf8_whitespace_table());
            $lowerCharList   = array_keys($caseTable);
            $upperCharList   = array_values($caseTable);

            for ($i = 0; $i < $inputLen; $i++) {
                // Since we don't have a list of acceptable symbols, we assume that if our other checks
                // don't find anything, its a symbol.
                $isSymbol = true;

                // ============================================
                // ============ Lowercase check ===============
                // --------------------------------------------
                if (in_array($inputChars[$i], $lowerCharList, true)) {
                    $lowerCount++;
                    $isSymbol = false;
                }
                // ============================================
                // ============ Uppercase check ===============
                // == Note: checked if ignoreCase is false ====
                // --------------------------------------------
                if (!$ignoreCase) {
                    if (in_array($inputChars[$i], $upperCharList, true)) {
                        $upperCount++;
                        $isSymbol = false;
                    }
                }
                // ============================================
                // ============ Number check ==================
                // --------------------------------------------
                if ($inputChars[$i] >= '0' && $inputChars[$i] <= '9') {
                    if ($allowNumbers) {
                        $numberCount++;
                        $isSymbol = false;
                    } else {
                        self::d("Number {$inputChars[$i]} found, but numbers aren't allowed");

                        return false;
                    }
                }
                // ============================================
                // =========== Whitespace check ===============
                // --------------------------------------------
                if (in_array($inputChars[$i], $whiteSpaceTable)) {
                    if ($allowWhiteSpace) {
                        $whiteSpaceCount++;
                        $isSymbol = false;
                    } else {
                        // Display error that states what kind of whitespace was found
                        self::d("Found whitespace " . array_search($inputChars[$i], utf8_whitespace_table()) .
                            ", but whitespaces aren't allowed");

                        return false;
                    }
                }

                // ============================================
                // ============ Symbol check ==================
                // --------------------------------------------
                if ($allowSymbol && $isSymbol) {
                    $symbolCount++;
                } elseif (!$allowSymbol && $isSymbol) {
                    self::d("Found symbol {$inputChars[$i]}, but symbols aren't allowed");

                    return false;
                }
            }

            // ============================================
            // ============ Disallowed check ==============
            // = Values are trimmed of leading/trailing ===
            // = whitespaces and forced to all lowercase ==
            // --------------------------------------------
            foreach ($disallowedList as $set) {

                $set      = utf8_strtolower(trim($set));
                $setChars = utf8_split($set);

                $setLen   = count($setChars);
                $setIndex = 0; // Start at the beginning

                $charFound        = false;
                $consecutiveFound = 0;

                for ($i = 0; $i < $inputLen; $i++) {
                    if ($setIndex < $setLen) {
                        // set the input character to lower case since this check is case insensitive
                        if (utf8_strtolower($inputChars[$i]) === $setChars[$setIndex]) {
                            if ($charFound) {
                                $consecutiveFound++;
                                if (($setIndex + 1) < $setLen) {
                                    $setIndex++;
                                } else {
                                    // we're at the end, exit loop
                                    break;
                                }
                            } else {
                                $charFound        = true;
                                $consecutiveFound = 1; // First occurrence
                                if (($setIndex + 1) < $setLen) {
                                    $setIndex++;
                                } else {
                                    // We're at the end, exit loop
                                    break;
                                }
                            }
                        } else {
                            // This iteration we didn't find a match
                            // If we found one previously, we must reset the set index
                            $charFound        = false;
                            $consecutiveFound = 0;
                            $setIndex         = 0;
                        }
                    }
                }

                if ($consecutiveFound == $setLen) {
                    // since the found characters matches the length of the set, we know this set exists in the input
                    self::d("Input contained disallowed sequence {$set}\n\r<br/>");

                    return false;
                }
            }

            // Time to determine the complexity requirements
            $complexCount = 0;

            if ($lowerReq >= 1 && $lowerCount >= $lowerReq) {
                self::d("Lowercase requirement met");
                $complexCount++;
            } else {
                self::d("Required lowercase count {$lowerReq} : received {$lowerCount}");
            }

            if ($upperReq >= 1 && $upperCount >= $upperReq) {
                self::d("Uppercase requirement met");
                $complexCount++;
            } else {
                self::d("Required uppercase count {$upperReq} : received {$upperCount}");
            }

            if ($numberReq >= 1 && $numberCount >= $numberReq) {
                self::d("Number requirement met");
                $complexCount++;
            } else {
                self::d("Required numbers {$numberReq} : received {$numberCount}");
            }

            if ($symbolReq >= 1 && $symbolCount >= $symbolReq) {
                self::d("Symbol requirement met");
                $complexCount++;
            } else {
                self::d("Required symbols {$symbolReq} : received {$symbolCount}");
            }

            if ($whiteSpacesReq >= 1 && $whiteSpaceCount >= $whiteSpacesReq) {
                self::d("Whitespace requirement met");
                $complexCount++;
            } else {
                self::d("Required whitespaces {$whiteSpacesReq} : received {$whiteSpaceCount}");
            }

            if ($complexCount >= $complexRequired) {
                // We can have more than required, but not less
                return true;
            } else {
                self::d("Complexity requirements weren't met:\n\r\t" .
                    "You needed {$complexRequired} groups, but you had {$complexCount}");

                return false;
            }
        }

        /**
         *
         * This function performs a Sanity check on the $input string and upon it passing it returns a hashed value.
         * This is more of a helper function when dealing primarily with passwords, but can be used for any value
         * needing hashing.
         *
         * @since 1.0
         *
         * @param string $input String of input to be Sanity checked and hashed
         * @param null   $ruleName Name of the rule to use for the Sanity check
         *
         * @return string hashed value of input if it passes the Sanity check,
         *         boolean false if value doesn't pass Sanity check
         */
        public static function check_and_hash($input, $ruleName = null) {
            if (self::check($input, $ruleName)) {
                return create_hash($input);
            } else {
                return false;
            }
        }

        /**
         * This function prints a formatted list of the default rules. The default rules may be changed if configure
         * is set without passing a ruleName to it.
         *
         * @since 1.0
         */
        public static function print_default_rules() {
            foreach (self::$DEFAULT_RULES as $rule => $value) {
                if (is_array($value)) {
                    echo "{\t}Rule: {$rule}\n\r<br/>";
                    foreach ($value as $key => $val) {
                        echo "&nbsp;&nbsp;&nbsp;{$key} &nbsp;&nbsp;=> {$val}\n\r<br/>";
                    }
                } else {
                    echo "{\t}Rule: {$rule}\t {$value}<br/>";
                }
            }
        }

        /**
         * Similar to print_default_rules, this function returns a formatted list from the SAVED_RULES array
         *
         * @since 1.0
         */
        public static function print_saved_rules() {
            if (count(self::$SAVED_RULES) > 0) {
                foreach (self::$SAVED_RULES as $name => $rules) {
                    echo "Rule Name: {$name}<br/>";
                    foreach ($rules as $rule => $value) {
                        if (is_array($value)) {
                            echo "{\t}Rule: {$rule}\n\r<br/>";
                            foreach ($value as $key => $val) {
                                echo "&nbsp;&nbsp;&nbsp;{$key} &nbsp;&nbsp;=> {$val}\n\r<br/>";
                            }
                        } else {
                            echo "{\t}Rule: {$rule}\t {$value}<br/>";
                        }
                    }
                    echo "<br/>";
                }
            } else {
                echo "<br/>No saved ruleset<br/>";
            }
        }

        /**
         * Iterates through the debug_info array and prints it values.
         *
         * @since 1.0
         */
        public static function print_debug_info() {
            if (self::$RULE_IN_USE[self::DEBUG]) {
                foreach (self::$debug_info as $msg) {
                    echo "{$msg}\n\r<br/>";
                }
            }
        }

        /**
         * Returns the debug array so the programmer can deal with it.
         *
         * @since 1.0
         *
         * @return array debug information
         */
        public static function get_debug_info() {
            return self::$debug_info;
        }

        /**
         * Adds another value to the debug_info array.
         *
         * @since 1.0
         *
         * @param string $msg
         */
        private static function d($msg) {
            self::$debug_info = array_merge(self::$debug_info, array($msg));
        }

    }

