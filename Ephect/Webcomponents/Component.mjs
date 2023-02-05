class Component extends HTMLElement {

    constructor() {
        super();

        <init />
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.innerHTML = `
        <html />
        `
        const slots = this.shadowRoot.querySelectorAll('slot')
        if(slots.length) {
            slots[0].addEventListener('slotchange', event => {
                console.dir(slots[0].assignedNodes())
            })
        }

    }

    static get observeAttributes() {
        return <attributes />
    }


    async connectedCallback() {
        <connectedCallback />
    }

}

customElements.define('<tag />', <class />);
