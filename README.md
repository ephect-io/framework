![Ephect](docs/salamandra.png)

## What is Ephect ?

In short, it's a ReactJS-like PHP framework. It allows you to create views by declaring components just like ReactJS does *today* <sup>1</sup>.

## Requirements

Ephect can pre-compile the site using a CLI tool : *egg*. This tool uses a thread-safe parallelism mechanism to compile pages that may contain the same components. The thread-safe feature ensures that there is no so called "*class* has already been declared" error. 

It is not mandatory but recommended to enable this mechanism. Otherwise you can compile pages dynamically by calling your application in the browser.

### PHP thread-safe

If you want to enable the parallelism feature, you must have a ZTS version of PHP. This can be done quite easily by using PhpBrew.

Here is a sample of PHP compilation statement for getting a thread-safe version of php-fpm : 

    phpbrew install 8.0.9 +bcmath +bz2 +calendar +cli +ctype +dom +fileinfo +filter +fpm +ipc +json +mbregex +mbstring +mhash +pcntl +pcre +pdo +pear +phar +posix +readline +sockets +tokenizer +xml +curl +openssl +zip +sqlite -- --enable-zts

To install PhpBrew, please refer to the documentation at [https://github.com/phpbrew/phpbrew](https://github.com/phpbrew/phpbrew).

### Parallel extension

Ephect uses Parallel extension as multi-thread mechanism. At the time writing, Parallel extension ***cannot*** be installed in PhpBrew just by typing the usual statement:
   
    phpbrew ext install parallel

Instead, you need to download the [develop zip archive](https://github.com/krakjoe/parallel/archive/refs/heads/develop.zip) and add the extension to PhpBrew on your own:

    wget https://github.com/krakjoe/parallel/archive/refs/heads/develop.zip
    unzip develop.zip
    cd parallel-develop
    phpize
    ./configure --enable-parallel
    make
    make test
    make install
    
The library will be installed in the right place like: 

    ~/.phpbrew/php/php-8.0.9/lib/php/extensions/no-debug-zts-20200930/

However you still need to declare the extension:

    echo extension=parallel.so > ~/.phpbrew/php/php-8.0.9/var/db/parallel.ini

## Install the framework

Using Composer just do:

    composer create-project codephoenixorg/ephect myproject

where *myproject* is the name of your project. 

## Install the sample application

Move to *myproject* directory and type:

    php ./bin/fcc.php -s

You will see a **src** directory in which you will find the standard structure of an ephect application. Ephect doesn't really care of the actual structure provided that it is under **src** directory. It means you can organize your application tree as you wish.

## Pre-compile the application

If you setup PHP-ZTS, good choice, you can generate your application without browser by typing:

    php ./bin/fcc.php -c

You will find the generated application under the directory *cache*.

## Launch the sample

You can test the sample application by using the PHP embedded web server:

    php -S localhost:8888 -t src/public

## The sample pages 

The available page routes are :
 - http://localhost:8888/
 - http://localhost:8888/hello?name=myname
 - http://localhost:8888/second
 - http://localhost:8888/info

The main route shows how to use useEffect and useState hooks all in nesting several components in cascade.

The Hello route shows a classic query string parameter binding.

The Second route shows how to use useSlot Hook to bind a variable nested inside the parent context.

The Info route shows how to make the most simple component without hooks.

## Notes

**Ephect framework** is in work in progress stage. This means that there's a lot to do. Breaking changes are yet to come.

<sup>1</sup> by *today* it's meant that Ephect follows the ReactJS paradigm as it is in 2021. Future ReactJS changes may not be taken in account in **Ephect framework**.

Happy coding again! :)
