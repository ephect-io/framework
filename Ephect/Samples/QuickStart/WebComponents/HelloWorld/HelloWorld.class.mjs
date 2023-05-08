export default class HelloWorld {

    constructor() {
        this.foo = 'bar'
    }

    hello(foo = null) {
        const baz = foo ?? this.foo
        return 'Something ' + baz
    }

}