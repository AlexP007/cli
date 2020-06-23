# PHP Cli application library
*for rapid creating php cli (command line) applications*

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
    
    