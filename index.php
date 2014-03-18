<?php

    include_once 'Sanity.php';

    $input = (isset($_GET['input']) ? $_GET['input'] : null);

    $minLen = (isset($_GET['min_length']) ? $_GET['min_length'] : 8);
    $maxLen = (isset($_GET['max_length']) ? $_GET['max_length'] : -1);
    $complexReq = (isset($_GET['complex_req']) ? $_GET['complex_req'] : 3);
    if (isset($_GET['allow_whitespaces'])) {
        $allowWhiteSpace = 1;
    } else {
        $allowWhiteSpace = 0;
    }

    if (isset($_GET['allow_symbols'])) {
        $allowSymbols = 1;
    } else {
        $allowSymbols = 0;
    }

    if (isset($_GET['allow_numbers'])) {
        $allowNumbers = 1;
    } else {
        $allowNumbers = 0;
    }

    if (isset($_GET['ignore_case'])) {
        $ignoreCase = 1;
    } else {
        $ignoreCase = 0;
    }

    $upper = (isset($_GET['upper']) ? $_GET['upper'] : 1);
    $lower = (isset($_GET['lower']) ? $_GET['lower'] : 1);
    $number = (isset($_GET['number']) ? $_GET['number'] : 1);
    $symbol = (isset($_GET['symbol']) ? $_GET['symbol'] : 1);
    $whiteSpace = (isset($_GET['white_space']) ? $_GET['white_space'] : 1);
    if (isset($_GET['disallowed_list']) && !empty($_GET['disallowed_list'])) {
        $disallowedList = explode(',', $_GET['disallowed_list']);
    } else {
        $disallowedList = null;
    }

    $debug = (isset($_GET['debug']) ? $_GET['debug'] : 0);
    $ruleName = (isset($_GET['rule_name']) ? $_GET['rule_name'] : null);
    if ($input) {
        //TODO: allow for saved configuration to be serialized with json and loaded back into Sanity
        Sanity::configure(array(
            Sanity::MIN_LENGTH          => $minLen,
            Sanity::MAX_LENGTH          => $maxLen,
            Sanity::COMPLEXITY_REQUIRED => $complexReq,
            Sanity::ALLOW_WHITESPACES   => $allowWhiteSpace,
            Sanity::ALLOW_SYMBOLS       => $allowSymbols,
            Sanity::ALLOW_NUMBERS       => $allowNumbers,
            Sanity::IGNORE_CASE         => $ignoreCase,
            Sanity::UPPER_COUNT               => $upper,
            Sanity::LOWER_COUNT               => $lower,
            Sanity::NUMBER_COUNT              => $number,
            Sanity::WHITESPACES_COUNT         => $whiteSpace,
            Sanity::DISALLOWED_LIST     => $disallowedList,
            Sanity::DEBUG               => $debug,
        ), $ruleName);
    }



?>


<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='style.css'/>
</head>
<body>
<div id='Banner'>
    <span>Sanity</span>
</div>

<div id='Main'>
    <form name='WhatSanity' action='index.php' method='get'>
        <div id='Allows' class='box'>
            <input id="allow_numbers" name="allow_numbers" type="checkbox"
                <?php if (isset($_GET['allow_numbers'])) {
                    echo "checked='checked'";
                } ?>/>
            <label for='allow_numbers'>Allow Numbers</label><br/>

            <input id='allow_symbols' name='allow_symbols' type='checkbox'
                <?php if (isset($_GET['allow_symbols'])) {
                    echo "checked='checked'";
                } ?>/>
            <label for='allow_symbols'>Allow Symbols</label><br/>

            <input id='allow_whitespaces' name='allow_whitespaces' type='checkbox'
                <?php if (isset($_GET['allow_whitespaces'])) {
                    echo "checked='checked'";
                } ?>/>
            <label for='allow_whitespaces'>Allow Whitespaces</label><br/>

            <input id='ignore_case' name='ignore_case' type='checkbox'
                <?php if (isset($_GET['ignore_case'])) {
                    echo "checked='checked'";
                } ?>/>
            <label for='ignore_case'>Ignore Case</label><br/>

            <input id='debug' name='debug' type='checkbox'
                <?php if (isset($_GET['debug'])) {
                    echo "checked='checked'";
                } ?>/>
            <label for='debug'>Show Debug</label><br/>
        </div>

        <div id='Reqs' class='box'>
            <select id='complex_req' name='complex_req'>
                <?php
                    for ($i = 0; $i < 6; $i++) {
                        if ($complexReq == $i) {
                            echo "<option value={$i} selected='selected'>$i</option>\n";
                        } else {
                            echo "<option value={$i}>$i</option>\n";
                        }
                    }
                ?>
            </select>
            <label for='complex_req'>Complexity Required</label>
            <br/>

            <input id='upper' name='upper' type='number' size='2'
                   value='<?php (isset($_GET['upper']) ? print $_GET['upper'] : print 1) ?>'/>
            <label for='upper'>Uppercase</label>
            <br/>

            <input id='lower' name='lower' type='number' size='2'
                   value='<?php (isset($_GET['lower']) ? print $_GET['lower'] : print 1) ?>'/>
            <label for='lower'>Lowercase</label>
            <br/>

            <input id='number' name='number' type='number' size='2'
                   value='<?php (isset($_GET['number']) ? print $_GET['number'] : print 1) ?>'/>
            <label for='number'>Numbers</label>
            <br/>

            <input id='symbol' name='symbol' type='number' size='2'
                   value='<?php (isset($_GET['symbol']) ? print $_GET['symbol'] : print 1) ?>'/>
            <label for='symbol'>Symbols</label>
            <br/>

            <input id='whitespace' name='whitespace' type='number' size='2'
                   value='<?php (isset($_GET['white_space']) ? print $_GET['white_space'] : print 1) ?>'/>
            <label for='whitespace'>Whitespaces</label>
        </div>

        <div id='CharLengths' class='right'>
            <input id='min_length' name='min_length' type='number' size='2'
                   value='<?php (isset($_GET['min_length']) ? print $_GET['min_length'] : print 8) ?>'/>
            <label for='min_length'>Minimum Length</label> <br/>
            <input id='max_length' name='max_length' type='number' size='2'
                   value='<?php (isset($_GET['max_length']) ? print $_GET['max_length'] : print -1) ?>'
                   title='If <= 0, max length is ignored'/>
            <label for='max_length'>Maximum Length</label> <br/>
        </div>

        <div id='RuleName'>
            <label for="rule_name">Rule Name</label><br/>
            <input type="text" name="rule_name" id="rule_name" value="
            <?php if (isset($_GET['rule_name'])) { echo $_GET['rule_name']; } ?>
            "/>
        </div>


        <br/><br/>

        <div id='Disallowed' class='box'>
            <label for='disallowed_list'>Disallowed Sequence list</label><br/>
            <textarea id='disallowed_list' name='disallowed_list'
                      cols='45'><?php (isset($_GET['disallowed_list']) ? print $_GET['disallowed_list'] : print null) ?></textarea><br/>
                    <span>Note: This is a comma separate list. The check against this list is only evaluated after
                        checking all other options. This list, however, is not sanity checked itself.
                    </span>
        </div>
        <br/><br/>

        <div id='InputBox' class="box">
            <label for='input'>Test Input</label>
            <textarea id='input' name='input'
                      cols='45'><?php (isset($_GET['input']) ? print $_GET['input'] : print null) ?></textarea><br/>
            <input type='submit' name='submit' value='Test'/>&nbsp;&nbsp;&nbsp;
            <a href="/sanity/index.php?allow_numbers=on&allow_symbols=on&allow_whitespaces=on&debug=on&complex_req=3&upper=1&lower=1&number=1&symbol=1&whitespace=1&min_length=8&max_length=-1">Load
                default</a>
        </div>
    </form>

    <div id='Info'>
        <p id='info_text'>
            <?php
                if (isset($input) && !empty($input)) {
                    $start = microtime(true);
                    if (Sanity::check($input, $ruleName)) {
                        echo "Input passed check!<br/><br/>";
                        $end = (microtime(true) - $start);

                    } else {
                        echo "Input is invalid<br/><br/>";
                        Sanity::print_debug_info();
                    }
                    $end = (microtime(true) - $start)*1000;
                    echo "Check completed in {$end} m/s<br/>";
                }
            ?>
        </p>
    </div>

    <div id="Rules">
        <?php if (isset($ruleName)) {Sanity::print_saved_rules();} else {Sanity::print_default_rules();} ?>
    </div>
</div>

</body>
</html>






