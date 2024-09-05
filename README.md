**![Ephect](assets/images/salamandra.png)

## What is Ephect ?

**E**ffective **P**rogramming in **H**ybrid **E**nvironment using **C**omponents-based **T**emplates.

It's a comprehensive solution that combines a lightweight PHP framework and a JavaScript library.

Ephect allows you to design backend pages and components a fluent way as well as web components.

It integrates a build process based on webpack.

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

**Ephect** comes with a CLI tool called *egg*. The main feature of **egg** is to build the code of the whole application at once. This is done in one command: *php egg build*. The benefits of this approach are the errors management and the increase of velocity of the application.

### Error handling

Every syntax error is handled as a fatal error which stops the compilation process. An explicit message is displayed on the console output to help you fix it quickly.

### Headless page generator

The compiler parses the templates of every component owned by a page comprising the cascading components. Each component is translated from **Ephect** template code to pure functional **PHP** code and cached. The code of each component is light and fast to run.

Once the pages are prepared in cache, browsing them is as fast as possible. The only bottleneck that you should be aware of is the data resources calls.

## Requirements

Since Ephect is an hybrid environment, you need both PHP and NodeJS installed.

It's highly recommended to use the last versions.

Ephect is compatible with PHP 8.3 and Node.js LTS 20.x.x.

You may also need to install some PHP extensions, if it's not done yes, like PDO/Sqlite, cUrl, Iconv, etc.

## Install the framework

It's recommended to use the composer create-project command in order to get all the needed stuff.

There's a dedicated project you can find here **[ephect-io/create-app](https://github.com/ephect-io/create-app)** that will help you for a quick start.

Just do:

    composer create-project ephect-io/create-app myproject

where *myproject* is the name of your project.

## Install the sample application

Move to *myproject* directory and type:

    php egg make:quickstart

You will see a **app** directory in which you will find the standard structure of an Ephect application and a **public** directory in which is stored the index.php.

## Build the application

First, install all needed modules.

```bash
npm install
```

To run the application without setting up a web server, you need to serve the application in a separate terminal.

Open another terminal, move to your project directory and type:

```bash
php egg serve
```

Come back to the first terminal and type
```bash
npm run dev
```

If you installed the QuickStart application as said previously, you should see something like this:


```bash
> create-app@0.7.0 dev
> run-script-os

> create-app@0.7.0 dev:darwin:linux
> bash scripts/unix/dev.sh all

scripts/unix/dev.sh: line 13: warning: here-document at line 11 delimited by end-of-file (wanted `LIST')
Running webpack...
asset app.min.js 2.32 KiB [emitted] (name: main)
runtime modules 274 bytes 1 module
./app/JavaScripts/index.js 212 bytes [built] [code generated]
webpack 5.91.0 compiled successfully in 306 ms

Publishing assets...
'app/Assets/bootstrap.php' -> 'public/bootstrap.php'
'app/Assets/css/setup.css' -> 'public/css/setup.css'
'app/Assets/css/index.css' -> 'public/css/index.css'
'app/Assets/css/app.css' -> 'public/css/app.css'
'app/Assets/favicon.ico' -> 'public/favicon.ico'
'app/Assets/img/salamandra.png' -> 'public/img/salamandra.png'
'app/Assets/index.php' -> 'public/index.php'
'app/Assets/web.config' -> 'public/web.config'

Sharing modules...
'node_modules/human-writes/dist/web/human-writes.min.js' -> 'public/modules/human-writes.min.js'

Building the app...
Compiling App ... 037ms
Compiling Home, querying http://localhost:8000/ ... 065ms
Compiling Hello, querying http://localhost:8000/hello/1/2 ... 013ms
Compiling World, querying http://localhost:8000/world ... 011ms
Compiling Matriochka0, querying http://localhost:8000/matriochka/1 ... 052ms
Compiling Info, querying http://localhost:8000/info ... 007ms
Compiling Callback, querying http://localhost:8000/callback ... 037ms
```

## The sample pages

The available page routes are:

A simple page with 2 children components; the first passes values to the second with useState hook.
- http://localhost:8000/

A sample to show how to manage parameters passed in the URL path.
- http://localhost:8000/hello/arg1/arg2

A sample to show how to manage parameters passed in the query string with a parent component for the style.
- http://localhost:8000/world?name=arg1&lname=arg2

A sample to show how to use the same component in cascade.
- http://localhost:8000/matriochka/arg1

A sample to show PHP info
- http://localhost:8000/info

A sample to show how to make a JavaScript callback with a PHP JSON response.
- http://localhost:8000/callback


## To be continued...
