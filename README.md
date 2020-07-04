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
Простая и легкая библиотека для скоростной разработки приложений командной строки на php.

Версия php >= 7.1

### Установка
    composer require --prefer-dist alexp007/cli
    
### Быстрый старт
Не забудьте подключить автозагрузчик composer, например так:

    require __DIR__ . "/../vendor/autoload.php"; // путь до автозагрузчика
    
далее

    use Cli\Basic\Cli;
    
    Cli::initialize([
        'script_file_name' => 'cli.php' // название файла
    ]);
    
    Cli::handle('sayHi', function ($name) { // callable 
        return "hi " . $name; 
    });
    
    Cli::run();
    
затем можно использовать в командной строке:
    
    php cli.php sayHi pete
    
результат выполнения будет:

    hi pete

### Создание комманд
Вы можете создавать любое кол-во комманд используя:

    Cli::handle($commandName, $callable)
 
* $commandName - string название команды 
* $callable - любой валидный php колбэк, если нужно передать статический метод класса то ['Class', 'MethodName']

Расширенный синтаксис выглядит так

    Cli::handle(string $command, callable $callback, array $flags = array(), array $env = array())
    
* $flags - разрешенные флаги, используемые вместе с командой, например ['-r', '--name']
* $env - переменные окружения: любые данные, которые должны быть доступны внутри $callback (используются, чтоы избежать глобалных зависимостей)

Расширенное создание команды может выглядеть так:

    use Cli\Basic\Cli;
    use Cli\Basic\Flags;
    use Cli\Basic\Environment;

    Cli::handle('sayHi', function ($name, Flags $flags, Environment $env) { // callable
        if ($flags->getFlag('--send')) {
            return "mail sent to administrator" . $env->getEnv('email');  
        }
        return "hi " . $name;
    }, ['--send'], ['email' => 'name@mail.ru']);
    
Если вы хотите использовать Flags и Environment, то указание типа данных в агрументах обязательно.

Данная библиотека строго относится к аргумента комманд, что значит команду ожидающую один аргумент, нельзя будет вызвать без него.
Однако, если аргумент не обязательный, то при создании функции следует указать значение по умолчанию для аргумента null, например:

    Cli::handle('sayHi', function ($name = null) {
        return 'hi';
    });
    
Если вы ожидаете переменное число аргументов, то предлается использовать объект Params:
    
    use Cli\Basic\Cli;
    use Cli\Basic\Params;
    
    Cli::handle('bit', function(Params $params){
        $allParams = $params->getArray();
        return join(',', $allParams);
    });
    
При использовании специальных объектов (Params, Flags, Environment) в качестве аргументов, их порядок не имеет значений:

    use Cli\Basic\Cli;
    use Cli\Basic\Flags;
    use Cli\Basic\Params;
    use Cli\Basic\Environment;
    
    Cli::handle('sayHi', function (Flags $flags, Environment $env, Params $params) { // callable
       return $params;
    }, ['--send'], ['email' => 'name@mail.ru']);
    
Любые обычные аргументы следует указывать до специальных.
    
### Основные правила
* При вызове команды из командой строки, флаги должны быть переданы до аргументов например: 


    php cli.php sayHi -f pete
       
* Вы можете использовать флаги с префиксами  "-" или "--"
* Вместе с флагами можно передавать их значение через "="


    php cli.php sayHi --mail=pete@mail.ru pete

* Если флаг используется без значения, то ему будет установлено значение по умолчанию в объекте Flags в true


    php cli.php sayHi -f pete
    
    -f будет равно true

* Библиотека не позволит использовать флаги, которые не были указаны при создании комманды, например для следующей
команды, нельзя будет использовать флаг "-r":


     Cli::handle('sayHi', function ($name) {
            return "hi " . $name; 
        }, [-f]);

* Так же библиотека строго относится к кол-ву аргументов команды (см.выше в разделе "Создание комманд")
* Так же в системе не может быть двух команд с одинаковым именем. Библиотека за этим внимательно следит =)

### Конфигурация

При инициализации приложения можно передавать настройки параметров конфигурации, вот пример использования всех настроек:

    Cli::initialize([
        'script_file_name'            => 'cli.php',
        'enable_list'                 => 'on',
        'enable_exceptions'           => 'on',
        'enable_errors'               => 'on',
        'enable_find_command_package' => 'on',
    ]);
    
* script_file_name - имя файла в котором подключается билиотека - **обязательная настройка**
* enable_list - разрешает использования листинга (встроенная команда, выводящая список всех доступных комманд)
* enable_exceptions - включает исключения и пояснения от билиотеки (рекомендуется включать всегда)
* enable_errors - включает ошибки (рекомендуется включать только при отладке)    
* enable_find_command_package - подключает пакет встроенных команд поиска