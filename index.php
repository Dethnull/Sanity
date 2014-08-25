<?php
    require_once 'init.php';

    if (Input::get('input')) {
        //TODO: allow for saved configuration to be serialized with json and loaded back into Sanity
        Sanity::configure(array(
            Sanity::MIN_LENGTH          => Input::get('min_length', 8),
            Sanity::MAX_LENGTH          => Input::get('max_length', -1),
            Sanity::COMPLEXITY_REQUIRED => Input::get('complex_req', 3),
            Sanity::ALLOW_WHITESPACES   => (Input::get('allow_whitespaces') ? 1 : 0),
            Sanity::ALLOW_SYMBOLS       => (Input::get('allow_symbols') ? 1 : 0),
            Sanity::ALLOW_NUMBERS       => (Input::get('allow_numbers') ? 1 : 0),
            Sanity::IGNORE_CASE         => (Input::get('ignore_case') ? 1 : 0),
            Sanity::UPPER_COUNT         => Input::get('upper', 1),
            Sanity::LOWER_COUNT         => Input::get('lower', 1),
            Sanity::NUMBER_COUNT        => Input::get('number', 1),
            Sanity::SYMBOL_COUNT        => Input::get('symbol', 1),
            Sanity::WHITESPACES_COUNT   => Input::get('whitespace', 1),
            Sanity::DISALLOWED_LIST     => explode(',', Input::get('disallowed_list')),
            Sanity::DEBUG               => Input::get('debug', 0),
        ), Input::get('rule_name', null));
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
    <form name='WhatSanity' action='' method='get'>
        <div id='Allows' class='box'>
            <input id="allow_numbers" name="allow_numbers" type="checkbox"
                <?php if (Input::get('allow_numbers')) {
                    echo "checked='checked'";
                } ?>/>
            <label for='allow_numbers'>Allow Numbers</label><br/>

            <input id='allow_symbols' name='allow_symbols' type='checkbox'
                <?php if (Input::get('allow_symbols')) {
                    echo "checked='checked'";
                } ?>/>
            <label for='allow_symbols'>Allow Symbols</label><br/>

            <input id='allow_whitespaces' name='allow_whitespaces' type='checkbox'
                <?php if (Input::get('allow_whitespaces')) {
                    echo "checked='checked'";
                } ?>/>
            <label for='allow_whitespaces'>Allow Whitespaces</label><br/>

            <input id='ignore_case' name='ignore_case' type='checkbox'
                <?php if (Input::get('ignore_case')) {
                    echo "checked='checked'";
                } ?>/>
            <label for='ignore_case'>Ignore Case</label><br/>

            <input id='debug' name='debug' type='checkbox'
                <?php if (Input::get('debug')) {
                    echo "checked='checked'";
                } ?>/>
            <label for='debug'>Show Debug</label><br/>
        </div>

        <div id='Reqs' class='box'>
            <select id='complex_req' name='complex_req'>
                <?php
                    for ($i = 0; $i < 6; $i++) {
                        if (Input::get('complex_req') == $i) {
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
                   value='<?php echo Input::get('upper'); ?>'/>
            <label for='upper'>Uppercase</label>
            <br/>

            <input id='lower' name='lower' type='number' size='2'
                   value='<?php echo Input::get('lower'); ?>'/>
            <label for='lower'>Lowercase</label>
            <br/>

            <input id='number' name='number' type='number' size='2'
                   value='<?php echo Input::get('number'); ?>'/>
            <label for='number'>Numbers</label>
            <br/>

            <input id='symbol' name='symbol' type='number' size='2'
                   value='<?php echo Input::get('symbol'); ?>'/>
            <label for='symbol'>Symbols</label>
            <br/>

            <input id='whitespace' name='whitespace' type='number' size='2'
                   value='<?php echo Input::get('whitespace'); ?>'/>
            <label for='whitespace'>Whitespaces</label>
            <br/>

            <input type="text" name="rule_name" id="rule_name" value="<?php echo Input::get('rule_name'); ?>"/>
            <label for="rule_name">Rule Name</label><br/>
        </div>


        <div id='CharLengths' class='right'>
            <input id='min_length' name='min_length' type='number' size='2'
                   value='<?php echo Input::get('min_length', 8) ?>'/>
            <label for='min_length'>Minimum Length</label> <br/>
            <input id='max_length' name='max_length' type='number' size='2'
                   value='<?php echo Input::get('max_length', -1) ?>'
                   title='If <= 0, max length is ignored'/>
            <label for='max_length'>Maximum Length</label> <br/>
        </div>

        <br/><br/>

        <div id='Disallowed' class='box'>
            <label for='disallowed_list'>Disallowed Sequence list</label><br/>
            <textarea id='disallowed_list' name='disallowed_list'
                      cols='45'><?php echo Input::get('disallowed_list'); ?></textarea><br/>
                    <span>Note: This is a comma separate list. The check against this list is only evaluated after
                        checking all other options. This list, however, is not sanity checked itself.
                    </span>
        </div>
        <br/><br/>

        <div id='InputBox' class="box">
            <label for='input'>Test Input</label>
            <textarea id='input' name='input'
                      cols='45'><?php echo Input::get('input') ?></textarea><br/>
            <input type='submit' name='submit' value='Test'/>&nbsp;&nbsp;&nbsp;
            <a href="index.php?allow_numbers=on&allow_symbols=on&allow_whitespaces=on&debug=on&complex_req=3&upper=1&lower=1&number=1&symbol=1&whitespace=1&min_length=8&max_length=-1">Load
                                                                                                                                                                                        default</a>
        </div>
    </form>

    <div id='Info'>
        <p id='info_text'>
            <?php
                if (Input::get('input')) {
                    $start = microtime(true);
                    if (Sanity::check(Input::get('input'), Input::get('rule_name'))) {
                        echo "Input passed check!<br/><br/>";
                        $end = (microtime(true) - $start);

                    } else {
                        echo "Input is invalid<br/><br/>";
                        Sanity::print_debug_info();
                    }
                    $end = (microtime(true) - $start) * 1000;
                    echo "Check completed in {$end} m/s<br/>";
                }
            ?>
        </p>
    </div>

    <div id="Rules">
        <?php if (Input::get('rule_name')) {
            Sanity::print_saved_rules();
        } else {
            Sanity::print_default_rules();
        } ?>
    </div>
</div>

</body>
</html>






