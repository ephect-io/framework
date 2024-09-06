class HelloWorldElement extends HTMLElement {

    constructor() {
        super();

        this.word
            this.styles
            this.classes
            
        this.attachShadow({mode: 'open'});
        this.renderTemplate()

        const slots = this.shadowRoot.querySelectorAll('slot')
        if (slots.length) {
            slots[0].addEventListener('slotchange', event => {
                console.dir(slots[0].assignedNodes())
            })
        }

    }

        static get observeAttributes() {
            /**
            * Attributes passed inline to the component
            */
            return ['word', 'styles', 'classes']
        }

    attributeChangedCallback(property, oldValue, newValue) {
        if (oldValue === newValue) return;

        this[property] = newValue;
    }
        get word() {
            return this.getAttribute('word') ?? null
        }
        get styles() {
            return this.getAttribute('styles') ?? null
        }
        get classes() {
            return this.getAttribute('classes') ?? null
        }
    
    async connectedCallback() {
        /**
         * Integrate styles and apply classes
         */
        if(this.styles !== null && this.classes !== null) {
            const $styleList = this.styles.split(',')

            $styleList.forEach($item => {
                const style = document.createElement('style')
                style.innerHTML = `@import "${$item}"`

                this.shadowRoot.appendChild(style)
            })

            const parentDiv = this.shadowRoot.getElementById('HelloWorld')
            parentDiv.setAttribute('class', this.classes)
        }
    }
}

export default HelloWorldElement
