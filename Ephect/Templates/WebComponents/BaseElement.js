class BaseElement extends HTMLElement {

    constructor() {
        super();

        Properties
        this.attachShadow({mode: 'open'});
        this.renderTemplate()

        const slots = this.shadowRoot.querySelectorAll('slot')
        if (slots.length) {
            slots[0].addEventListener('slotchange', event => {
                console.dir(slots[0].assignedNodes())
            })
        }

    }

    ObserveAttributes

    attributeChangedCallback(property, oldValue, newValue) {
        if (oldValue === newValue) return;

        this[property] = newValue;
    }
    GetAttributes
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

            const parentDiv = this.shadowRoot.getElementById('Base')
            parentDiv.setAttribute('class', this.classes)
        }
    }
}

export default BaseElement
