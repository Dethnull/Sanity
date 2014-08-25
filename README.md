#Sanity - A simple, configurable, and powerful Php validation script that's UTF-8 safe!


## Features
- Easy to use
- Highly configurable with the ability to name your configuration
- UTF-8 safe
- Passwords, Usernames, Emails; the choice is up to you!


## Table of Contents
- [Example](#example)
- [Configuration Explained](#configuration-explained)
- [Disallowed List](#disallowed_list)
- [Functions](#sanity-functions)
- [Tip](#tip)
- [To-do List](#todo-list)
- [License](#license)

## Example

```php
<?php
    include_once "Sanity.php";

    Sanity::configuration(array(
        Sanity::MIN_LENGTH => 10, // Sets the minimum length the input string can be
        Sanity::UPPER_COUNT => 2, // Sets the required amount of uppercase letters
        Sanity::LOWER_COUNT => 2, // Sets the required amount of lowercase letters
        Sanity::COMPLEXITY_REQUIRED => 3, // This determines how many of the 5 options are needed for the check to pass
    ), "password");  // This string names the configuration so we can call it at a later time.

    $userPassword = "This is a test123!!"; // test string

    if (Sanity::check($userPassword, "password")) {
        echo "Woo, the user password meets our requirements!!";
    } else {
        echo "Password didn't meet the requirements, sorry :(";
    }

```

## Configuration Explained

The configuration() function accepts two arguments; an array with the configuration constants, and a ruleName to name your
configuration for later use. The ruleName is optional and if omitted, changes the default values of Sanity.

#### Constants accepted by Sanity::configure()
These values can be set in an arbitrary order. Most of the options explained below are fairly self explanatory, but I'll
explain how they work and their default values. The default values are assigned what they are to conform to strict security
practices.

Constant Name | Type | Default(s) | Description | Note
------------- | ---- | -------- | ----------- | ----
**MIN_LENGTH** | int |_8_ | Minimum length input is allowed to be | If < 0 this option is ignored
**MAX_LENGTH** | int |_-1_ | Maximum length input is allowed to be | If < 0 or < MIN_LENGTH this option is ignored
**ALLOW_WHITESPACES** | bool | _true_ | Allows whitespaces is true | ...
**ALLOW_NUMBERS** | bool | _true_ | Allows numbers if true | ...
**ALLOW_SYMBOLS** | bool | _true_ | Allows symbols if true | Sanity assumes that any input that is not a number and is not an uppercase/lowercase or whitespace value as defined in the [portable-utf8](portable-utf8.php) library, is a symbol.
**IGNORE_CASE** | bool | _false_ | Ignores the case of the input if true | If this option is true it forces the input to all lowercase values for checking. `UPPER_COUNT` is ignored if this value is true.
**UPPER_COUNT** | int | _2_ | Required # of uppercase letters to pass check | If < 0 this option is ignored. Ignored if `IGNORE_CASE` is true. Included in `COMPLEXITY_REQUIRED` check
**LOWER_COUNT** | int | _2_ | Required # of lowercase letters to pass check | If < 0 this option is ignored. Included in `COMPLEXITY_REQUIRED` check
**NUMBER_COUNT** | int | _2_ | Required # of numbers to pass check | If < 0 this option is ignored. Included in `COMPLEXITY_REQUIRED` check
**SYMBOL_COUNT** | int | _2_ | Required # of symbols to pass check | Included in `COMPLEXITY_REQUIRED` check
**WHITESPACE_COUNT** | int | _2_ | Required # of whitespaces to pass check | Included in `COMPLEXITY_REQUIRED` check
**COMPLEXITY_REQUIRED** | int | _3_ | Required number of options that must be met. | The maximum number this can be is the size of the array `REQUIRED_GROUPS`
**DISALLOWED_LIST** | array | null | List of strings that are not allowed in input. | This check is case insensitive, regardless of what `IGNORE_CASE`  is set to.
**DEBUG** | bool | _false_ | Allows debug information to be printed using the function `print_debug_info()`

> Note: `REQUIRED_GROUPS` that you will see in the source, should only be used internally. I created this constant for internal ease of use.

### DISALLOWED_LIST

The disallowed list is an array that accepts strings as it's value. It only checks for the values and not the keys, so putting something in there won't change anything.

#### Example

```php
<?php
    include_once "Sanity.php";

    Sanity::configure(array(
        Sanity::DISALLOWED_LIST => array("123", "abc", "password"),
    ),"password");

    $test = "mypassword123";

    if (Sanity::check($test, "password"))
        echo "This won't happen with the test input";
    else
        echo "Input contains invalid sequence of characters";

```

In the above example the string, `mypassword123` would fail because it contains two sets from the disallowed list; `123 & password`. It checks the set lists in order, so it would actually fail on the first check of, `123`.

As you can see, it doesn't matter how you format your string, if it contains any of the sequence of characters set in the `DISALLOWED_LIST` it will reject the input. This check, as stated in the constant table, is not case sensitive. So if you have `password` in the list and your input contains `PASsword` it will still fail.

## Sanity Functions

All functions in Sanity are static, so you must call them like such: Sanity::funcName()

Function Name | Parameters | Return Value | Description
------------- | ---------- | ------------ | ------------
configure() | (`array()`, `$ruleName`) | N/A | Takes an array the rule variables and a string to name your configuration. $ruleName is optional
check() | (`$input = string , $ruleName = null) | bool | Returns true if $input meets the criteria of $ruleName, or default rules if $ruleName is undefined.
check_and_hash() | ($input = string, $ruleName = null) | string or false | Returns a hash of the input if it meets the ruleName criteria. The hash returned is defined in [PasswordHash](PasswordHash.php)
print_default_rules() | N/A | N/A | This echo's a formatted string that displays what Sanity's default rules currently are.
print_saved_rules() | N/A | N/A | Same as print_default_rules(), except it prints the contents of the saved_rules array.
print_debug_info() | N/A | N/A | Echo's the debug information, which will state what info needs to be met for your input to be valid. For debug purposes, not user end.
get_debug_info() | N/A | array | Returns the errors Sanity had during its checks

## Tip

When configuring your Sanity checker, I recommend creating a separate file to put all your named configurations in. Then include this file at the end of the Sanity.php file.

Example

```php
    <?php
    // Sanity.php source...
    include_include "sanity-conf.php";
```

```php
    <?php
    // sanity-conf.php source...

    Sanity::configure(array(
        rule assignment here...
    ),"password");

    Sanity::configure(array(
        rule assignment here...
    ),"username");
```

```php
    <?php
    // Yourscript.php
    include_once "Sanity.php" // Includes your configured Sanity rules

    if (Sanity::check($userInput, "password"))
        echo "success";
    else
        echo "failure";

```


## TODO List

- [ ] `COMPLEXITY_REQUIRED` doesn't check how many of the `__COUNT` variables are set to 0. So if they are all < 0, your input will always fail when **COMPLEXITY_REQUIRED** is > 0
- [ ] `COMPLEXITY_REQUIRED` also will make input fail if `ALLOW_` variables are all false, and `COMPLEXITY_REQUIRED` is > 2
- [ ] Implement more robust debug information that can be obtained and presented to the end user.
- [ ] Increased optimization. I'm sure there is more that can be done to increase the speed of Sanity
- [ ] Add wildcard characters and/or the ability to use regex in the `DISALLOWED_LIST` -- maybe just simplified regex :P
- [ ] Add the ability to specify which saved rule to print out with `print_saved_rules()`
- [ ] Add the ability to specify whether you want to update or overwrite the `DISALLOWED_LIST` - as of now it appends to list

## License

Sanity is released under the MIT license, so you are free to use it in commercial or non-commercial projects, just be sure
to include the MIT license along with it.

