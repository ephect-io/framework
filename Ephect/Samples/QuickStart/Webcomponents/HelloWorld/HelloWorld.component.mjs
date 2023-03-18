import HelloWorld from "./HelloWorld.class.mjs"

/**
 * Rename the component
 */
class HelloWorldComponent extends HTMLElement {

    instantiate() {
        /**
         * Called in the constructor
         */
    }

    static get observeAttributes() {
        /**
         * Attributes passed inline to the component
         */
        return ['word']
    }

    get word() {
        return this.getAttribute('word') ?? null
    }

    async connectedCallback() {
       /**
        * The magic starts here
        */
       const world = new HelloWorld()
       world.sayHello(this.word)
    }
}