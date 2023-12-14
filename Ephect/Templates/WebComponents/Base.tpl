<template id="Base">
    <h1>
        ${result}
    </h1>
    <slot></slot>
</template>
<script>
    import Base from "./Base.class.mjs"
    import BaseElement from "./BaseElement.js"

    /**
     * Rename the component
     */
    class BaseComponent extends BaseElement {

        renderTemplate() {
            /**
             * The magic starts here
             */
            const base = new Base()
            const result = base.entrypoint(AttributeList)

            this.shadowRoot.innerHTML = document.getElementById('Base').innerHTML
        }

    }

    customElements.define('tag-name', BaseComponent);
</script>
