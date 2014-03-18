#Sanity - A simple, configurable, and powerful Php validation script that's UTF-8 safe!


## Features
- Easy to use
- Highly configurable with the ability to name your configuration
- UTF-8 safe
- Passwords, Usernames, Emails; the choice is up to you!


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
**IGNORE_CASE** | bool | _false_ | Ignores the case of the input if true | If this option is true it forces the input to all lowercase values for checking. **UPPER_COUNT** is ignored if this value is true.
**UPPER_COUNT** | int | _2_ | Required # of uppercase letters to pass check | If < 0 this option is ignored. Ignored if **IGNORE_CASE** is true. Included in **COMPLEXITY_REQUIRED** check
**LOWER_COUNT** | int | _2_ | Required # of lowercase letters to pass check | If < 0 this option is ignored. Included in **COMPLEXITY_REQUIRED** check
**NUMBER_COUNT** | int | _2_ | Required # of numbers to pass check | If < 0 this option is ignored. Included in **COMPLEXITY_REQUIRED** check
**SYMBOL_COUNT** | int | _2_ | Required # of symbols to pass check | Included in **COMPLEXITY_REQUIRED** check
**WHITESPACE_COUNT** | int | _2_ | Required # of whitespaces to pass check | Included in **COMPLEXITY_REQUIRED** check
**COMPLEXITY_REQUIRED** | int | _3_ | Required number of options that must be met. | The maximum number this can be is the size of the array **REQUIRED_GROUPS**
**DEBUG** | bool | _false_ | Allows debug information to be printed using the function **print_debug_info()**

> Note: **REQUIRED_GROUPS** that you will see in the source, should only be used internally. I created this constant for internal ease of use.

## Sanity Functions

All functions in Sanity are static, so you must call them like such: Sanity::funcName()

Function Name | Parameters | Return Value | Description
------------- | ---------- | ------------ | ------------
configure() | ($conf = array(), $ruleName = null) | N/A | Takes an array the rule variables and a string to name your configuration. $ruleName is optional
check() | ($input = string , $ruleName = null) | bool | Returns true if $input meets the criteria of $ruleName, or default rules if $ruleName is undefined.
check_and_hash() | ($input = string, $ruleName = null) | string or false | Returns a hash of the input if it meets the ruleName criteria. The hash returned is defined in [PasswordHash](PasswordHash.php)
print_default_rules() | N/A | N/A | This echo's a formatted string that displays what Sanity's default rules currently are.
print_saved_rules() | N/A | N/A | Same as print_default_rules(), except it prints the contents of the saved_rules array.
print_debug_info() | N/A | N/A | Echo's the debug information, which will state what info needs to be met for your input to be valid. For debug purposes, not user end.

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
    // Youscript.php
    include_once "Sanity.php" // Includes your configured Sanity rules

    if (Sanity::check($userInput, "password"))
        echo "success";
    else
        echo "failure";

```


##  TODO

- [ ] **COMPLEXITY_REQUIRED** doesn't check how many of the *__COUNT variables are set to 0. So if they are all < 0, your input will always fail when **COMPLEXITY_REQUIRED** is > 0
- [ ] **COMPLEXITY_REQUIRED** also will make input fail if **ALLOW_*** variables are all false, and **COMPLEXITY_REQUIRED** is > 2
- [ ] Implement more robust debug information that can be obtain and presented to the end user.
- [ ] Increased optimization. I'm sure there is more that can be done to increase the speed of Sanity

## License

Sanity is released under the MIT license, so you are free to use it in commercial or non-commercial projects, just be sure
to include the MIT license along with it.

