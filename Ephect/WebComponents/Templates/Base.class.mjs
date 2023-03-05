export default class Base 
{

    constructor() {
        this.foo = 'bar'
    }

    doSomething(foo = null) {
        const foo = foo ?? this.foo
        return 'Something ' + foo
    }

}