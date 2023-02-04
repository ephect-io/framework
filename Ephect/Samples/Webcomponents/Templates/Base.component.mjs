import Base from "./Base.class.mjs"

/**
 * Rename the component
 */
class BaseComponent extends HTMLElement {

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
       const world = new Base()
       world.sayHello(this.word)
    }
}