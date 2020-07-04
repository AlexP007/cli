# PHP CLI APP

documentation:
* [English-version](#English)
* [Russian-version](#Russian)

## English
*simple and lightweight library for rapid creation php cli (command line) applications*

### Installation 
    composer require --prefer-dist alexp007/cli

## Basic usage
    use Cli\Basic\{Cli, Formatter};
    
    Cli::initialize([
        'script_file_name' => 'cli.php'
    ]);
    
    Cli::handle('sayHi', function ($name) {
       $fmt = new Formatter("hi $name!");
       return $fmt->yellow();
    });
    
    Cli::run();
    
then you could run command via terminal like:

    php cli.php sayHi Peter
    
### Initialize
    use Cli\Basic\Cli;

    Cli::initialize(array  $config) 

$config is an array of basic configurations.
For now time there is only one option you MUST pass: 
* script_file_name - name of the file

there are also additional options:
* list (enable_list => 'Y') the list command will be available

### Handling Commands
    Cli::handle(string  $command, callable  $callback, array  $flags = array(), array $env = array())
    
* $command is the name of command for execution via cli
* $callback is function that will be invoked
* $flags array of available flags for this command
* $env array of environment variables that will be passed to $callback

### Basic rules
* Flags must be passed before parameters when executing command via cli (example: php cli.php sayHi -f pete)
* Only "-" and "--" prefix available for flags
* With flags could be passed they values like -f=value
* If flag value is not passed it will be set to true
* You cannot use flags that are not specified in $flags
* The application tracks the number of parameters. 
This means that you cannot pass parameters more or less than expected (Optional parameters are not taken)
* No redeclaring available for one $command

### Run

    Cli::run() 
    
### Invoking callback

    function(Params $params, Environment $env, Flags $flags) {}
 
* Params, Flags or Environment will be passed into callback in order you specify them in the arguments.
This arguments are called "special" and they are optional
* Non-special arguments must be specified before special:


    function(param1, param2, Flags $flags) {}
    
* If you don't know exact number of arguments you could use Params, all arguments will be passed inside Params object:

    
    function(Params $params) {}
    
### Params 

    Params::getParam(int $n) - where $n is position
    Params::getArray(): array
    
### Flags

    Flags::getFlag(string $flag)
    Flags::getArray(): array
    
### Environment

    Environment::getEnv(string $key)
    Environment::getArray(): array
        
### Formatter

    use Cli\Basic\Formatter;

Is a class that helps you to format your output.
You can pass array to its constructor

Below methods are available
    
Set color to red:

    Formatter::red() : $this
    
Set color to blue:

    Formatter::blue() : $this
    
Set color to yellow:

    Formatter::yellow() : $this
    
Add line break:

    Formatter::line() : $this
    
Print:

    Formatter::printOut() 

### Here are some examples:

    Cli::handle('sayHi', function ($name, \Cli\Basic\Flags $flags) {
       $fmt = new Formatter("hi $name!");
       $fmt->yellow()->line()->printOut();
       
       $fm2 = new Formatter($flags);
       
       return $fm2;
    }, ['-f']); 
    
The output of command "php cli.php sayHi -f=flag pete"

    hi pete!
    {
        "-f": flag
    }
    
If you don't know the number of incoming parameters you could use Params object

    Cli::handle('sayHi', function (\Cli\Basic\Params $params, \Cli\Basic\Flags $flags) {
       $fmt = new Formatter($params);
       $fmt->red()->line()->printOut();
       
       $fm2 = new Formatter($flags);
       return $fm2;
    }, ['-f']);
    
The output of command "php cli.php sayHi -f=flag pete lena"    

    [
        "pete",
        "lena"
    ]
    {
        "-f": "flag"
    } 
   
### Predefined Commands
If enable_list is set to 'Y' you could list all commands to output by using 'list' command

## Russian