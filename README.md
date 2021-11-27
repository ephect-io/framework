![Ephect](docs/salamandra.png)

## What is Ephect ?

Ephect is a fucntional components based framework with template syntax. It gathers some features from **ReactJS<sup>1</sup>** and **WebComponents<sup>2</sup>** like hooks and slots.

## Featured concepts

### Cascading and reusing components

The components can be declared in cascade and the same component can be reused in each cascading component.

A parent componenet with predefined slots can be *inherited* to change the content of its slots. A special component called *Slot* is dedicated to replace parent contents.

### Hooks

**Ephect** introduces *hooks* to manage the data workflow. Additionally to well-known useEffect and useState in the **ReactJS** realm, **Ephect** comes with 3 new basic hooks:

- useProps: to ensure a property can be used,
- useQueryArgument: to filter the value passed as parameter to the query,
- useSlot: to bind variables embedded in slots to the parent component.

## CLI tool *egg*

Ephect comes with a CLI tool called *egg*. The premier feature of egg is to pre-compile the code of the whole application at once. This is done in one command: php egg compile. The benefits of this approach are the errors managment and the ease of integration in a CI/CD system.

### Fatal error handling

Every syntax error is handled as a fatal error which stops the compilation process. An explicit message is displayed on the console output to help you fixing it quickly. You have no hidden error ready to break to stability of your application in a page that you don't browse. The compilation process successfully ends only when all errors are fixed.

### Headless page generator

The compiler parses the templates of every components owned by a page comprising the cascading components. Each component is translated from *Ephect* template code to *pure functional PHP code*. The code of each component is light and fast to run. 

Once the pages are prepared in cache, browsing them is as fast as possible. The only bottleneck that you should be aware of is the data resources calls.

## Requirements

To take benefits of all the CLI features you need a thread-safe version of PHP also called ZTS with Parallel extension. It is not mandatory but recommended to enable ZTS in PHP engine. Otherwise you can compile pages dynamically by calling your application in the browser just as other frameworks do.

To eneble ZTS see this [section](docs/enable-zts)

## Install the framework

There's a dedicated project named ephect-io/create-app that helps you installing all you need for a quick start. 

Using Composer just do:

    composer create-project ephect-io/create-app myproject

where *myproject* is the name of your project. 

## Install the sample application

Move to *myproject* directory and type:

    php egg make:sample

You will see a **src** directory in which you will find the standard structure of an ephect application. Ephect doesn't really care of the actual structure provided that it is under **src** directory. It means you can organize your application tree as you wish.

## Pre-compile the application

If you setup PHP-ZTS, good choice, you can generate your application without browser by typing:

    php egg compile

You will find the generated application under the directory *cache*.

## Launch the sample

You can test the sample application by using the PHP embedded web server:

    php -S localhost:8888 -t src/public

## The sample pages 

The available page routes are :
 - http://localhost:8888/
 - http://localhost:8888/second?name=myname
 - http://localhost:8888/hello/name
 - http://localhost:8888/matroichka
 - http://localhost:8888/info

The main route shows how to use useEffect and useState hooks all in nesting several components in cascade.

The Second route shows how to use useSlot Hook to bind a variable nested inside the parent context. You can also see how to manage arguments of the query string using the hook useQueryArgument.

The Hello route shows how to manage named arguments in the pathnname, ideal for dealing with API.

The Matroichka route shows how to embed components in cascade.

The Info route shows how to make the most simple component without hooks.

### Yarn support

If you're used to using yarn, Ephect comes with yarn support.

## Installation

It's simple as:

    npm i

## Available commands from yarn

You can replace php by yarn

    yarn egg help

is identical to

    php egg help

but yarn has some shortcuts too:

 - *yarn build* replaces *php egg compile*
 - *yarn serve* launches the PHP embedded server

## Notes

**Ephect framework** is in work in progress stage. This means that there's a lot to do. Breaking changes are yet to come.

<sup>1</sup> to better understand the concept of hooks you should have a look at [ReactJS hooks reference](https://reactjs.org/docs/hooks-reference.html)

<sup>2</sup> 
Happy coding again! :)
