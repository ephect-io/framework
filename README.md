![Ephect](assets/images/salamandra.png)

## What is Ephect ?

Ephect is a backend components based framework with template syntax. It gathers some features from *ReactJS<sup>1</sup>* and *WebComponents<sup>2</sup>* like hooks and slots.

## Featured concepts

### The components

Basically, components are custom HTML tags. All concepts of HTML tags on backend side are applicable to components except the naming convention: a component name must begin with a capital letter. 

The logic of the component is coded in a simple funcion returning a template.

#### Example 1

Here is an open component that can surround children components or just HTML code.

```html
<MyComponent>
    Hello World!
</MyComponent>
```

The code would look like this:

```php
<?php

namespace QuickStart;

function MyComponent($children) {

    return (<<< HTML
    <h1>My component</h1>
    <p>
    {{ children }}
    </p>
    HTML);
}
```

It will render:

```html
    <h1>My component</h1>
    <p>
    Hello World!
    </p>
```

#### Example 2

A closed component to which we pass arguments:

```html
<AnotherComponent with="Some argument" />
```

The code would look like this:

```php
<?php

namespace QuickStart;

function AnotherComponent($props) {

    return (<<<HTML
    <h2 id="with">
        {{ props->with }}
    </h2>
    HTML);
}
```

It will render:

```html
    <h2 id="with">
    Some argument
    </h2>
```
### Cascading and reusing components

The components can be declared in cascade and the same component can be reused in each cascading component.

If we combine examples 1 and 2:

```html
<MyComponent>
    <AnotherComponent with="Some argument" />
</MyComponent>
```

It will render:

```html
    <h1>My component</h1>
    <p>
    <h2 id="with">
        Some argument
    </h2>
    </p>
```

## The component Slot

A parent component with named placeholders can be inherited to change the content of its placeholders. The component in charge of managing placeholders is called *Slot*.

Here is a Mother component containing slots to be overriden:

```php
<?php

namespace QuickStart;

function Mother($children)
{

    return (<<< HTML
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>
            <Slot name="title">
                Mother!
            </Slot>
            </title>

            <Slot name="stylesheets">
            </Slot>
        </head>

        <body>
            <div class="App" >
                {{ children }}
            </div>
            <Slot name="javascripts">
            </Slot>
        </body>
    </html>
    HTML);
}
```

And here is a component inheriting from Mother component:

```php
<?php

namespace QuickStart;

function Home()
{
    return (<<< HTML
    <Mother>
        <Slot name="title">Ephect in action !</Slot>
        <Slot name="stylesheets">
            <link rel="stylesheet" href="/css/app.css" />
        </Slot>
        <!-- 
            The children container of the parent component
            is filled with all that is not nested in slots
        -->
        <div class="App-content" >
            <h1>Welcome Home!</h1>
        </div>
        <!-- end of children code -->
        <Slot name="javascripts">
            <script src="/js/ephect.js"></script>
        </Slot>
    </Mother>
    HTML);
}
```

It will render: 

```html
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>
                Ephect in action !
            </title>

            <link rel="stylesheet" href="/css/app.css" />
        </head>

        <body>
            <div class="App">
                <div class="App-content">
                    <h1>Welcome Home!</h1>
                </div>
            </div>
            <script src="/js/ephect.js"></script>
        </body>
    </html>
```


### Hooks

**Ephect** introduces *hooks* to manage the data workflow. Additionally to well-known useEffect and useState hooks in the **ReactJS** realm, **Ephect** comes with 3 new basic hooks:

- useProps: to ensure a property can be used,
- useQueryArgument: to filter the value passed as parameter to the query,
- useSlot: to bind variables nested in slots to the parent component.

## CLI tool *egg*

**Ephect** comes with a CLI tool called *egg*. The main feature of **egg** is to pre-compile the code of the whole application at once. This is done in one command: *php egg compile*. The benefits of this approach are the error managment and the increase of velocity of the application.

### Error handling

Every syntax error is handled as a fatal error which stops the compilation process. An explicit message is displayed on the console output to help you fixing it quickly.

### Headless page generator

The compiler parses the templates of every components owned by a page comprising the cascading components. Each component is translated from **Ephect** template code to pure functional **PHP** code and cached. The code of each component is light and fast to run. 

Once the pages are prepared in cache, browsing them is as fast as possible. The only bottleneck that you should be aware of is the data resources calls.

## Requirements

To take adavantage of all the CLI features you need a thread-safe version of **PHP** also called **ZTS** with **Parallel** extension. It is not mandatory but recommended to enable **ZTS** in the **PHP** engine. Otherwise you can compile pages dynamically by calling your application in the browser.

To enable ZTS see [enable-zts section](https://ephect.io/enable-zts) on *ephect.io*.

## Install the framework

There's a dedicated project you can find here **[ephect-io/create-app](https://github.com/ephect-io/create-app)** that helps you installing all you need for a quick start. 

Using Composer just do:

    composer create-project ephect-io/create-app myproject

where *myproject* is the name of your project. 

## Install the sample application

Move to *myproject* directory and type:

    php egg make:sample

You will see a **src** directory in which you will find the standard structure of an ephect application. Ephect doesn't really care of the actual structure provided that it is under **src** directory. It means you can organize your application tree as you wish.

## Pre-compile the application

If you setup **PHP-ZTS**, good choice, you can generate your application without browser by typing:

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

The Hello route shows how to manage named arguments in the pathname, ideal for dealing with API.

The Matroichka route shows how to embed components in cascade.

The Info route shows how to make the most simple component without hooks.

### Yarn support

If you're used to using yarn, **Ephect** comes with yarn support.

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

**Ephect** framework is in work in progress stage. This means that there's a lot to do. Breaking changes are yet to come.

<sup>1</sup>. The concept of hooks in **ReactJS** is explained at [ReactJS hooks reference](https://reactjs.org/docs/hooks-reference.html).

<sup>2</sup>. The concept of slots in **WebComponents** is explained at [MDN -  The Web Component Slot element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/slot).

Happy coding again! :)
