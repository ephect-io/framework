import HelloEngine from "./HelloEngine.mjs"

/**
 * Rename the component
 */
class Component extends HTMLElement {

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
       const engine = new HelloEngine()
       engine.sayHello(this.word)
    }
}