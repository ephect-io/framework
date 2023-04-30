import Decomposer from "./lib/decomposer.mjs"

export default class CodeWriter {
    #parent = null

    constructor(parent) {
        this.#parent = parent
    }

    async writeLikeAHuman(source, target) {

        const sourceComponent = this.#parent.shadowRoot.querySelector('pre#' + source + ' code')
        const targetComponent = this.#parent.shadowRoot.querySelector('pre#' + target + ' code')
        const speed = 60
        const LF = "\n"
        let reg = []
        let indents
        let html = ''
        let lastIndent = ''
        let lastLineFeed = ''
        let node = null
        let nodes = []
        let stack = []
        let text = ''
        let workingText = ''
        let depth = -1
        let toUnshift = []
        let toUnshiftHasLF = []
        let canContinue = true
        let indentCount = 0

        function delay(milliseconds) {
            return new Promise(resolve => {
                setTimeout(resolve, milliseconds)
            })
        }

        async function addChar(c, removeLF  = false) {
            let tail = reg.join("")
            if(removeLF) {
                tail = tail.trim()
            }

            html += c
            targetComponent.innerHTML = html + tail
            if (window['hljs'] !== undefined) {
                hljs.highlightElement(targetComponent)
            }

            await delay(speed)
        }

        function unshift(char) {
            reg.unshift(char)
        }

        function shift() {
            delete reg[0]
            reg = Object.values(reg)
        }

        function parseIndents(text) {
            const result = []
            const regex = /^([^\S][ \s]+)*/mg;

            let matches

            while ((matches = regex.exec(text)) !== null) {
                if (matches.index === regex.lastIndex) {
                    regex.lastIndex++
                }

                result.push(matches[0] ?? '')
            }

            return result
        }

        function deleteIndents(text) {
            const regex = /^([^\S][ \s]+)*/mg
            return text.replace(regex, '');
        }

        function makeEmptyText(text) {
            const lines = text.split("\n")
            let result = ''
            for (const k in lines) {
                result += "<br />"
            }

            return result
        }

        async function loadText(url) {
            let text = ''
            await fetch(url).then(response => response.text()).then((html) => {
                text = html
            })

            return text
        }

        function protect(text) {
            text = text.replace(/<([\/\w])/gm, '&lt;$1')
            text = text.replace(/>/g, '&gt;')

            return text
        }

        function translate(text) {
            text = text.replaceAll('&lt;', '<')
            text = text.replaceAll('&gt;', '>')

            return text
        }


        function nextNode() {
            if (nodes.length) {
                node = null
            }

            node = nodes.shift()
            if (node.hasCloser) {
                stack.push(node)
            }
        }

        function lastNode() {
            if (!stack.length) {
                return null
            }

            return stack[stack.length - 1]
        }

        function nextUnshift() {
            if (!toUnshift.length) {
                return null
            }


            const closer = toUnshift.pop()
            const contentHasLF = toUnshiftHasLF.pop()
            if (contentHasLF) {
                unshift(LF + lastIndent + closer)
            } else {
                unshift(closer)
            }

        }

        function findLastNodeOfDepth(depth) {
            let result = null
            if (!stack.length) {
                return result
            }

            result = lastNode()
            if (depth === result.depth) {
                return result
            }

            let isFound = false
            for (let i = stack.length - 1; i > -1; i--) {
                result = stack[i]
                if (depth === result.depth) {
                    isFound = true
                    break
                }
            }
            if (!isFound) {
                return null
            }

            return result
        }

        function translateBracket(base, node, isClosing = false) {
            let word = base
            let translated = false

            if ('CDETQRG'.includes(node.name)) {
                if (node.name === 'C') {
                    word = isClosing ? ')' : '('
                    translated = true
                }
                if (node.name === 'D') {
                    word = isClosing ? '}}' : '{{'
                    translated = true
                }
                if (node.name === 'E') {
                    word = isClosing ? '}' : '{'
                    translated = true
                }
                if (node.name === 'T') {
                    word = isClosing ? ']' : '['
                    translated = true
                }
                if (node.name === 'Q') {
                    word = "'"
                    translated = true
                }
                if (node.name === 'R') {
                    word = '"'
                    translated = true
                }
                if (node.name === 'G') {
                    word = '`'
                    translated = true
                }
            }

            return {word, translated}
        }

        let codeSource = this.#parent.getAttribute("source") ?? ''

        if (window['hljs'] !== undefined) {
            hljs.highlightElement(sourceComponent);
        }
        text = await loadText(codeSource)
        text = protect(text)

        indents = parseIndents(text)
        text = deleteIndents(text)

        const decomposer = new Decomposer(text)
        decomposer.doComponents()
        nodes = decomposer.list

        workingText = decomposer.workingText.replace('\n&lt;Eof /&gt;', '')

        const emptyText = makeEmptyText(workingText)
        sourceComponent.innerHTML = emptyText

        const firstIndent = indents[indentCount] ?? ''
        await addChar(firstIndent)
        indentCount++

        for (let i = 0; i < workingText.length; i++) {

            let c = workingText[i]
            if (c === '<') {
                c = '&lt;'
                await addChar(c)
                continue
            }

            // Encountering a "greater than" character 
            // potentially closing a single parsed tag
            if (c === '&' && workingText.substring(i, i + 4) === '&gt;') {

                if (node.endsAt === i + 3) {

                    const shifted = '&gt;'
                    shift()

                    await addChar(shifted)
                    nextUnshift()
                    i += 3

                    continue
                }
            }

            // Encountering a "lower than" character 
            // potentially closing an open parsed tag
            if (c === '&' && workingText.substring(i, i + 5) === '&lt;/') {

                node = findLastNodeOfDepth(depth)

                if (node === null && depth - 1 > -1) {
                    node = findLastNodeOfDepth(depth - 1)
                }

                c = node.closer.text
                let {word, translated} = translateBracket(c, node, true)

                c = word

                if (c !== '') {
                    shift()
                    i = node.closer.endsAt
                    await addChar(c)
                    depth--
                    node = null
                    continue
  
                }

            }
            // Encountering an ampersand character 
            // potentially starting an HTML entity
            if (c === '&' && workingText.substring(i, i + 4) !== '&lt;') {

                const scpos = workingText.substring(i).indexOf(';')
                if (scpos > 8) {
                    await addChar(c)
                    continue
                }
                const entity = workingText.substring(i, i + scpos) 
                await addChar(entity)
                i += entity.length - 1
                continue
                
            }
            // Encountering a "lower than" character 
            // potentially starting an open parsed tag
            if (c === '&' && workingText.substring(i, i + 4) === '&lt;') {

                // We don't take the next node if the last 
                // "lower than" character was not a parsed tag
                if(canContinue) {
                    nextNode()
                }

                // The "lower than" character is actually not
                // the beginning a parsed tag
                if (node.startsAt !== i) {
                    // Write it and prevent taking the next node
                    await addChar('&lt;')
                    i += 3
                    canContinue = false
                    continue
                }

                // The "lower than" character is 
                // the start of a parsed tag      
                canContinue = true
                let hasLF = false
                let unshifted = ''
                depth++

                c = node.text
                // Is the tag name a bracket?
                let {word, translated} = translateBracket(c, node)
                c = word 

                // Does the tag string contain an LF character?
                hasLF = node.text.indexOf(LF) > -1
                // Is it an open tag?
                if (node.hasCloser) {
                    unshifted = node.closer.text

                    // Is the tag name a bracket?
                    let {word, translated} = translateBracket(unshifted, node, true)
                    unshifted = word


                    // Does the tag body contain an LF character?
                    hasLF = node.closer.contents.text.indexOf(LF) > -1

                    // Is the tag name a bracket?
                    if(translated) {
                        // Store the closing bracket 
                        // to write it after each new character
                        i = node.endsAt
                        if (hasLF) {
                            unshift(LF + lastIndent + unshifted)
                        } else {
                            unshift(unshifted)
                        }
                    
                    } else {
                        // Store the tag closser after the opener is written
                        toUnshift.push(unshifted)
                        toUnshiftHasLF.push(hasLF)
                    }

                }

                // The tag name is not a bracket
                // The tag is not open
                if(!translated) {
                    hasLF = node.text.indexOf(LF) > -1

                    c = '&lt;'
                    i += 3
                    unshifted = '&gt;'
                    if (hasLF) {
                        unshift(LF + lastIndent + unshifted)
                    } else {
                        unshift(unshifted)
                    }
                }

                // Write the actual sttring 
                // and continue to next character 
                await addChar(c)
                continue

            }

            // Encounter the line feed character
            if (c === LF) {
                // Add the lined feed 
                // followed by the indent
                // of the next line

                lastIndent = indents[indentCount] ?? ''
                lastLineFeed = LF + lastIndent

                const reg0 = reg.length ? reg[0].trim() : ''
                const nextString = workingText.substring(i + 1, i + reg0.length + 1)

                indentCount++

                await addChar(lastLineFeed, reg0 === nextString)
                continue
            }

            await addChar(c)
        }

        // Set back the code text in pure HTML
        html = translate(html)

        // Raise an event outside the shadow DOM 
        // when all is done and ready
        const finishedEvent = new CustomEvent("finishedWriting", {
            bubbles: true,
            composed: true,
            detail: {
                content: html
            }
        })
        this.#parent.dispatchEvent(finishedEvent)
    }

}