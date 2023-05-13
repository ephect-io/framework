<template id="Base">
    <h1>
        <slot/>
        {{ foo }}
    </h1>
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
            this.shadowRoot.innerHTML = document.getElementById('Base').innerHTML

            const slots = this.shadowRoot.querySelectorAll('slot')
            if (slots.length) {
                slots[0].addEventListener('slotchange', event => {
                    console.dir(slots[0].assignedNodes())
                })
            }

        }

    <ObserveAttributes />

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

            /**
             * The magic starts here
             */
            const base = new Base()
            base.entrypoint()
        }
    }

    customElements.define('tag-name', BaseComponent);
</script>
