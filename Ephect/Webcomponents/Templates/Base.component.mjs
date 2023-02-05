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
        return ['foo']
    }

    get foo() {
        return this.getAttribute('foo') ?? null
    }

    async connectedCallback() {
       /**
        * The magic starts here
        */
       const base = new Base()
       base.doSomething(this.foo)
    }
}