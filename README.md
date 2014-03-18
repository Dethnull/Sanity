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
**REQUIRED_GROUPS** | array | **UPPER_COUNT**, **LOWER_COUNT**, **NUMBER_COUNT**, **SYMBOL_COUNT**, **WHITESPACE_COUNT** | This array holds the values that must be met when **COMPLEXITY_REQUIRED** does its thing
**DEBUG** | _false_ | bool | Allows debug information to be printed using the function **print_debug_info()**

> Note: **REQUIRED_GROUPS** should only be manipulated when changing the core functionality of Sanity. Adding any values when configuring Sanity is not recommended as you will definitely break it's functionality.

##  TODO

- If **ALLOW_WHITESPACES**, **ALLOW_NUMBERS**, and **ALLOW_SYMBOLS** are all false and **COMPLEXITY_REQUIRED** is > 2, then input will always be rejected. This will be fixed in 1.1
- [] if all *__COUNT variables are set to 0 and **COMPLEXITY_REQUIRED** is set to anything higher than 0, your input will always be rejected.


## License

Sanity is released under the MIT license, so you are free to use it in commercial or non-commercial projects, just be sure
to include the MIT license along with it.

