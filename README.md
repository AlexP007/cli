# PHP CLI APP

documentation:
* [English-version](#English)
* [Russian-version](#Russian)

## English
Simple and easy library for rapid development of command line applications in php.

Php version> = 7.1

### Installation

    composer require --prefer-dist alexp007/cli
    
### Fast start
Remember to include composer autoloader, like this:

    require __DIR__ . "/../vendor/autoload.php"; // path to autoload.php
    
then

    use Cli\Basic\Cli;
    
    Cli::initialize([
        'script_file_name' => 'cli.php' // название файла
    ]);
    
    Cli::handle('sayHi', function ($name) { // callable 
        return "hi " . $name; 
    });
    
    Cli::run();


now command can be used via cli:

    php cli.php sayHi pete
    

the result of the execution will be:

    hi pete
    

### Creating commands

You can create any number of commands using:

    Cli::handle($commandName, $callable)
  

* $commandName - (string) command name
* $callable - (callable) any valid php callback, if you need to pass a static class method then ['Class', 'MethodName']


Extended syntax looks like this:

    Cli::handle(string $command, callable $callback, array $flags = array(), array $env = array())

* $flags - (array) allowed flags used with the command, for example ['-r', '--name']
* $env - (array) environment variables: any data that should be available inside $callback (used to avoid global dependencies)

Extended command declaration example:

    use Cli\Basic\Cli;
    use Cli\Basic\Flags;
    use Cli\Basic\Environment;

    Cli::handle('sayHi', function ($name, Flags $flags, Environment $env) { // callable
        if ($flags->getFlag('--send')) {
            return "mail sent to administrator" . $env->getEnv('email');  
        }
        return "hi " . $name;
    }, ['--send'], ['email' => 'name@mail.ru']);
    
If you want to use Flags and Environment, then specifying the data type in the arguments is mandatory.

This library strictly refers to the arguments of commands, which means a command that expects a single argument cannot be called without it.
However, if the argument is optional, then when creating the function, you should specify the default value for the argument null, for example:

    Cli::handle('sayHi', function ($name = null) {
        return 'hi';
    });
    

If you expect a variable number of arguments, then you are prompted to use a Params object:

    use Cli\Basic\Cli;
    use Cli\Basic\Params;
    
    Cli::handle('bit', function(Params $params){
        $allParams = $params->getArray();
        return join(',', $allParams);
    });
    
 
When using special objects (Params, Flags, Environment) as arguments, their order does not matter:   

    use Cli\Basic\Cli;
    use Cli\Basic\Flags;
    use Cli\Basic\Params;
    use Cli\Basic\Environment;
    
    Cli::handle('sayHi', function (Flags $flags, Environment $env, Params $params) { // callable
       return $params;
    }, ['--send'], ['email' => 'name@mail.ru']);
    

Any other arguments should be specified before special ones.   

### Fundamental rules 
* When invoking a command from a command line, flags must be passed before arguments, for example:

    php cli.php sayHi -f pete
       
* You can use flags with the prefixes "-" or "--"
* Together with flags, you can pass their value through "="

    php cli.php sayHi --mail=pete@mail.ru pete

* If the flag is used without a value, then it will be set to the default value in the Flags object in true

    php cli.php sayHi -f pete
    
    -f will be true

* The library will not allow the use of flags that were not specified when creating the command, for example, for the next
commands, it will not be possible to use the "-r" flag:


    Cli::handle('sayHi', function ($name) {
            return "hi " . $name; 
        }, [-f]);
        
* Also, the library strictly refers to the number of arguments of a command (see above in the section "Creating Commands")
* Also in the system there cannot be two command with the same name. The library is closely following this =)        

### Special Objects
#### Params 

    Params::getParam(int $n) - where $n is position
    Params::getArray(): array // all params
    
#### Flags

    Flags::getFlag(string $flag)
    Flags::getArray(): array // all flags
    
#### Environment

    Environment::getEnv(string $key)
    Environment::getArray(): array // all environment vars
    

### Configuration
When initializing the application, you can set configuration settings, here is an example:    
          
    Cli::initialize([
        'script_file_name'            => 'cli.php',
        'enable_list'                 => 'on',
        'enable_exceptions'           => 'on',
        'enable_errors'               => 'on',
        'enable_find_command_package' => 'on',
    ]); 
    
* script_file_name - the name of the file in which the library is connected - ** required setting **
* enable_list - allows the use of listing (built-in command that displays a list of all available commands)
* enable_exceptions - includes exceptions and explanations from the library (always recommended)
* enable_errors - enables errors (it is recommended to enable only during debugging)
* enable_find_command_package - enables a package of built-in search commands

### Built-in Commands
* list (if 'enable_list' => 'on') allows you to use the built-in list command, which lists all
teams registered in the system and brief information about them:  


    php cli.php list
    
will return:

    +-------------+---------------+------------------+
    | Command     | Params        | Flags            |
    +-------------+---------------+------------------+
    | bit         |               |                  |
    | find:file   | path, pattern | -r               |
    | find:inFile | path, pattern | -r, --extensions |
    | list        |               |                  |
    | sayHi       |               | --send           |
    | table       |               |                  |
    +-------------+---------------+------------------+
    
    

* find: file [path to the search directory] [pattern - regular expression]
-> search for files in the system.

You can use the "-r" flag to recursively search subdirectories:

    php cli.php find:file ./ "php"
    

will find files with php extension:

    
    +--------------+----------------+
    | Filename     | Filepath       |
    +--------------+----------------+
    | autoload.php | ./autoload.php |
    | cli.php      | ./cli.php      |
    +--------------+----------------+
    
    
* find: inFile [path to the search directory] [pattern - regular expression]
-> search for matches in files.

You can use the -r flag to recursively search subdirectories and
the “--extensions” flag indicates the exact extensions, separated by commas (only these files will be searched):

    php cli.php find:inFile --extensions=php ./ "include"

will return   

    +--------------------------------+------+----------+-----------+
    | Match                          | Line | Filename | Filepath  |
    +--------------------------------+------+----------+-----------+
    | include_once "src/$class.php"; | 12   | cli.php  | ./cli.php |
    +--------------------------------+------+----------+-----------+
    
To use the find package commands, you need to set 'enable_find_command_package' => 'on' in the configuration.  

### Formatter
A class that simplifies the work with outputting the result:

    use Cli\Basic\Formatter;
    
Output color red:

    Formatter::red() : $this

Output color blue:

    Formatter::blue() : $this
    
Output color red:

    Formatter::yellow() : $this

Table view:

    Formatter::asTable(): $this

Line break:

    Formatter::line() : $this

Prints to output stream:

    Formatter::printOut()
    
Creating a new Formatter (you can pass an array or a string):

    new Formatter(array or string $data)

Special objects (Params, Flags, Environment) can be passed without additional adaptation:

    use Cli\Basic\Cli;
    use Cli\Basic\Formatter;
            
    Cli::handle('bit', function(Params $params){
        $fmt = new Formatter($params);
        return $fmt->blue();
    });
    
Example table output:

    use Cli\Basic\Cli;
    use Cli\Basic\Formatter;
    
    Cli::handle('table', function() {
        $data = [
            ['command_1', 'params', 'flags'],
            ['command_2', '[1,34,56,]', '[-f -r -d]'],
            ['special_command', '[1,string,56,]', '[-f -r -d]'],
        ];
    
        $fmt = new Formatter($data);
        return $fmt->asTable()->red();
    
    });
                

## Successful development to you!
Write to me with any questions and suggestions to <alex.p.panteleev@gmail.com>, as well as create issues.

Contributors welcome!  
        
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

### Создание команд
Вы можете создавать любое кол-во команд используя:

    Cli::handle($commandName, $callable)
 
* $commandName - (string) название команды 
* $callable - (callable) любой валидный php колбэк, если нужно передать статический метод класса то ['Class', 'MethodName']

Расширенный синтаксис выглядит так:

    Cli::handle(string $command, callable $callback, array $flags = array(), array $env = array())
    
* $flags - (array) разрешенные флаги, используемые вместе с командой, например ['-r', '--name']
* $env - (array) переменные окружения: любые данные, которые должны быть доступны внутри $callback (используются, чтоы избежать глобалных зависимостей)

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

Данная библиотека строго относится к аргументам команд, что значит команду, ожидающую один аргумент, нельзя будет вызвать без него.
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

* Библиотека не позволит использовать флаги, которые не были указаны при создании команды, например для следующей
команды, нельзя будет использовать флаг "-r":


     Cli::handle('sayHi', function ($name) {
            return "hi " . $name; 
        }, [-f]);
        

* Так же библиотека строго относится к кол-ву аргументов команды (см.выше в разделе "Создание команд")
* Так же в системе не может быть двух команд с одинаковым именем. Библиотека за этим внимательно следит =)

### Специальные объекты
#### Params 

    Params::getParam(int $n) - where $n is position
    Params::getArray(): array // all params
    
#### Flags

    Flags::getFlag(string $flag)
    Flags::getArray(): array // all flags
    
#### Environment

    Environment::getEnv(string $key)
    Environment::getArray(): array // all environment vars

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
* enable_list - разрешает использования листинга (встроенная команда, выводящая список всех доступных команд)
* enable_exceptions - включает исключения и пояснения от билиотеки (рекомендуется включать всегда)
* enable_errors - включает ошибки (рекомендуется включать только при отладке)    
* enable_find_command_package - подключает пакет встроенных команд поиска

### Встроенные команды
* list (если 'enable_list' => 'on') позволяет использовать встроенную команду list, которая выводит список всех
зарегистрированных в системе команд и краткую информацию о них:


    php cli.php list

вернет:

    +-------------+---------------+------------------+
    | Command     | Params        | Flags            |
    +-------------+---------------+------------------+
    | bit         |               |                  |
    | find:file   | path, pattern | -r               |
    | find:inFile | path, pattern | -r, --extensions |
    | list        |               |                  |
    | sayHi       |               | --send           |
    | table       |               |                  |
    +-------------+---------------+------------------+

* find:file [путь к директории поиска] [паттерн - регулярное выражение]
-> поиск файлов в системе.

Можно использовать флаг "-r" для рекурсивного поиска в поддиректориях:


    php cli.php find:file ./ "php"
    
найдет файлы c расширением php:

    +--------------+----------------+
    | Filename     | Filepath       |
    +--------------+----------------+
    | autoload.php | ./autoload.php |
    | cli.php      | ./cli.php      |
    +--------------+----------------+
    
* find:inFile [путь к директории поиска] [паттерн - регулярное выражение]
-> поиск совпадений внутри файлов.

Можно использовать флаг "-r" для рекурсивного поиска в поддиректориях и
флаг "--extensions" для указания точных расширений, через запятую (только для этих файлов будет произведен поиск):

    
    php cli.php find:inFile --extensions=php ./ "include"
    
вернет:

    
    +--------------------------------+------+----------+-----------+
    | Match                          | Line | Filename | Filepath  |
    +--------------------------------+------+----------+-----------+
    | include_once "src/$class.php"; | 12   | cli.php  | ./cli.php |
    +--------------------------------+------+----------+-----------+

Для использования команд пакета find нужно в конфигурации установить 'enable_find_command_package' => 'on'.

### Formatter

Класс, который упрощает работу с выводом результата:

    use Cli\Basic\Formatter;

    
Цвет вывода красный:

    Formatter::red() : $this
    
Цвет вывода синий:

    Formatter::blue() : $this
    
Цвет вывода красный:

    Formatter::yellow() : $this
    
Табличное представление:

    Formatter::asTable(): $this
    
Перенос строки:

    Formatter::line() : $this
  
Печатает в поток вывода:

    Formatter::printOut()
    
Создание нового Formatter(можно передавать массив или строку):

    new Formatter(array or string $data)
    
Специальные объекты (Params, Flags, Environment) можно передавать без обработки:

    use Cli\Basic\Cli;
    use Cli\Basic\Formatter;
            
    Cli::handle('bit', function(Params $params){
        $fmt = new Formatter($params);
        return $fmt->blue();
    });
    
Пример табличного вывода:

        use Cli\Basic\Cli;
        use Cli\Basic\Formatter;
        
        Cli::handle('table', function() {
            $data = [
                ['command_1', 'params', 'flags'],
                ['command_2', '[1,34,56,]', '[-f -r -d]'],
                ['special_command', '[1,string,56,]', '[-f -r -d]'],
            ];
        
            $fmt = new Formatter($data);
            return $fmt->asTable()->red();
        
        });
        
## Успешной вам разработки!
Пишете мне с любыми вопросами и предложениями на <alex.p.panteleev@gmail.com>, а так же создавайте issues.

Всем желающим контрибьютить - добро пожаловать!