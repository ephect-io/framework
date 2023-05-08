export default class Base {

    constructor() {
        this.foo = 'bar'
    }

    entrypoint(foo = null) {
        const baz = foo ?? this.foo
        return 'Something ' + baz
    }

}