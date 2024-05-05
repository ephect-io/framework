<template id="{{Base}}">
    <h1>
        ${result}
    </h1>
    <slot></slot>
</template>
<script>
    import {{Base}} from "./{{Base}}.class.mjs"
    import {{Base}}Element from "./{{Base}}Element.js"

    /**
     * Rename the component
     */
    class {{Base}}Component extends {{Base}}Element {

        renderTemplate() {
            /**
             * The magic starts here
             */
            const {{objectName}} = new {{Base}}()
            const result = {{objectName}}.{{entrypoint}}({{AttributeList}})

            this.shadowRoot.innerHTML = document.getElementById('{{Base}}').innerHTML
        }

    }

    customElements.define('{{tag-name}}', {{Base}}Component);
</script>
