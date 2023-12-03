<template id="Base">
    <h1>
        ${result}
    </h1>
    <slot></slot>
</template>
<script>
    import Base from "./Base.class.mjs"

    /**
     * Rename the component
     */
    class BaseComponent extends HTMLElement {

        constructor() {
            super();

            <Properties />
            this.attachShadow({mode: 'open'});
            this.renderTemplate()

            const slots = this.shadowRoot.querySelectorAll('slot')
            if (slots.length) {
                slots[0].addEventListener('slotchange', event => {
                    console.dir(slots[0].assignedNodes())
                })
            }

        }

        renderTemplate() {
            /**
             * The magic starts here
             */
            const base = new Base()
            const result = base.entrypoint(<AttributeList />)

            this.shadowRoot.innerHTML = document.getElementById('Base').innerHTML
        }

    <ObserveAttributes />

        attributeChangedCallback(property, oldValue, newValue) {
            if (oldValue === newValue) return;

            this[property] = newValue;
        }
    <GetAttributes />
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

    customElements.define('tag-name', BaseComponent);
</script>
