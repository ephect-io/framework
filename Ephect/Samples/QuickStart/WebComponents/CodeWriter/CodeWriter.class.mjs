import Decomposer from "./lib/decomposer.mjs"

export default class CodeWriter {
    #parent = null

    constructor(parent) {
        this.#parent = parent
    }

    async writeLikeAHuman(source, target) {

        const sourceComponent = this.#parent.shadowRoot.querySelector('pre#' + source + ' code')
        const targetComponent = this.#parent.shadowRoot.querySelector('pre#' + target + ' code')
        const reg = []
        const LF = "\n"
        let indents
        let html = ''
        let lfCount = 0
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
        const speed = 100

        function delay(milliseconds) {
            return new Promise(resolve => {
                setTimeout(resolve, milliseconds)
            })
        }

        async function addChar(c, useRegistry = true) {
            const tail = reg.join("")
            let suffix = useRegistry ? tail : ''

            html += c
            targetComponent.innerHTML = html + suffix
            if (window['hljs'] !== undefined) {
                hljs.highlightElement(targetComponent)
            }

            await delay(speed)
        }

        function unshift(char) {
            reg.unshift(char)
        }

        function unshiftLF(char, indent) {
            const text = "\n" + indent + char
            reg.unshift(text)
        }

        function shift(entity) {
            let shifted = -1
            const needle = entity.trim()
            for (const k in reg) {
                let val = reg[k].trim()
                if (val === needle) {
                    shifted = k
                }
                break
            }

            if (shifted > -1) {
                delete reg[shifted]
            }
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

        function parseNextEntity(text, startIndex) {

            let result = ''
            const regex = /(&[a-z]+;)/;
            const haystack = text.substr(startIndex)

            const matches = regex.exec(haystack)
            result = matches[0] ?? ''

            return result
        }

        function parseArguments(text, startIndex) {

            const result = []
            const regex = /([\w]*)(\[\])?=(\"([\S ][^"]*)\"|\'([\S]*)\'|\{\{ ([\w]*) \}\}|\{([\S ]*)\})/m

            const haystack = text.substring(startIndex, text.length - 4)

            console.log({
                text,
                haystack,
                startIndex
            })
            let matches

            while ((matches = regex.exec(haystack)) !== null) {
                if (matches.index === regex.lastIndex) {
                    regex.lastIndex++
                }

                result.push(matches[0] ?? '')
            }

            return result
        }

        async function writeEntities(text, startIndex = 0) {

            if (text.substr(0, 5) === '&lt;/') {
                const closer = text.substr(0, text.length - 4)
                await addChar(lastLineFeed + closer)
                return
            }

            let entitiesText = text.substring(startIndex)

            if (entitiesText === '&gt;') {
                return
            }

            unshift('&gt;')

            if (entitiesText.substr(entitiesText.length - 4) === '&gt;') {
                entitiesText = entitiesText.substr(0, entitiesText.length - 4)
            }

            for (let j = 0; j < entitiesText.length; j++) {
                let char = entitiesText[j]
                if (char === '&') {
                    const nextEntity = parseNextEntity(entitiesText, j)
                    if (nextEntity !== '') {
                        char = nextEntity
                        j += char.length - 1
                    }
                }
                await addChar(char)
            }

            shift('&gt;')
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
                node = null
                return
            }

            node = stack[stack.length - 1]
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
            if (!stack.length) {
                node = null
                return
            }

            lastNode()
            if (depth === node.depth) {
                return
            }

            let isFound = false
            for (let i = stack.length - 1; i > -1; i--) {
                node = stack[i]
                if (depth === node.depth) {
                    isFound = true
                    break
                }
            }
            if (!isFound) {
                node = null
                return
            }
        }

        function translateBracket(base, node, isClosing = false) {
            let word = base
            let translated = false

            if ('CDET'.includes(node.name)) {
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
            }

            return {
                word,
                translated
            }
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
        workingText = decomposer.workingText.replace('&lt;Eof /&gt;', '')

        const emptyText = makeEmptyText(workingText)
        sourceComponent.innerHTML = emptyText

        for (let i = 0; i < workingText.length; i++) {

            let c = workingText[i]
            if (c === '<') {
                c = '&lt;'
                await addChar(c)
                continue
            }

            if (c === '&' && workingText.substring(i, i + 4) === '&gt;') {

                if (node.endsAt === i + 3) {

                    const shifted = '&gt;'
                    if (node.text.indexOf(LF) > -1) {
                        shift(LF + lastIndent + shifted)
                    } else {
                        shift(shifted)
                    }

                    await addChar(shifted)

                    nextUnshift()

                    i += 3
                    continue
                }
            }

            if (c === '&' && workingText.substring(i, i + 5) === '&lt;/') {

                findLastNodeOfDepth(depth)

                if (node === null && depth - 1 > -1) {
                    findLastNodeOfDepth(depth - 1)
                }

                c = node.closer.text
                let {
                    word,
                    translated
                } = translateBracket(c, node, true)

                c = word

                if (c !== '') {
                    if (node.closer.contents.text.indexOf(LF) > -1) {
                        shift(LF + lastIndent + c)
                    } else {
                        shift(c)
                    }

                    i = node.closer.endsAt
                    await addChar(c)
                    depth--
                    continue

                }

            }
            if (c === '&' && workingText.substring(i, i + 4) === '&lt;') {
                nextNode()
                if (node.startsAt === i) {
                    let hasLF = false
                    let unshifted = ''

                    c = node.text
                    let {
                        word,
                        translated
                    } = translateBracket(c, node)
                    c = word

                    hasLF = node.text.indexOf(LF) > -1
                    if (node.hasCloser) {
                        unshifted = node.closer.text
                        let {
                            word,
                            translated
                        } = translateBracket(unshifted, node, true)
                        unshifted = word

                        hasLF = node.closer.contents.text.indexOf(LF) > -1

                        if (translated) {
                            i = node.endsAt
                            if (hasLF) {
                                unshift(LF + lastIndent + unshifted)
                            } else {
                                unshift(unshifted)
                            }

                        } else {
                            toUnshift.push(unshifted)
                            toUnshiftHasLF.push(hasLF)
                        }

                    }

                    if (!translated) {
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

                    depth++
                    await addChar(c)
                    continue

                }
            }

            if (c === LF) {
                lfCount++
                lastIndent = indents[lfCount] ?? ''
                lastLineFeed = LF + lastIndent

                await addChar(lastLineFeed)
                continue
            }

            await addChar(c)
        }

        html = translate(html)

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