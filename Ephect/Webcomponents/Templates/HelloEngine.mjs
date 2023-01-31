export default class HelloEngine  {

    constructor() {
        this.hello = 'world'
    }

    sayHello(word = null) {
        const message = word ?? this.hello
        return 'Hello ' + message
    }
}