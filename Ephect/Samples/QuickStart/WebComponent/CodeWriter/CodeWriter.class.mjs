export default class CodeWriter 
{
    #parent = null

    constructor(parent) {
        this.#parent = parent
    }

    async writeLikeAHuman(source, target) {

        const sourceComponent = this.#parent.shadowRoot.querySelector('pre#' + source + ' code')
        const targetComponent = this.#parent.shadowRoot.querySelector('pre#' + target + ' code')
        const reg = []
        const LF = "\n"
        let positions
        let indents
        let html =  ''
        let lfCount = 0
        let lastIndent = ''
        let lastLineFeed = ''

        function delay(milliseconds) {
            return new Promise(resolve => {
                setTimeout(resolve, milliseconds)
            })
        }

        async function addChar (c, useRegistry = true) {
            const tail = reg.join("")
            let suffix = useRegistry ? tail : ''

            html += c
            targetComponent.innerHTML = html + suffix
            if(window['hljs'] !== undefined) {
                hljs.highlightElement(targetComponent)
            }

            await delay(50)
        }

        function findObjectIndexByPosition (position) {
            let result = null
    
            for (const i in positions) {
                const bracketObject = positions[i]
                const index = Object.keys(bracketObject)[0]

                if(index === "" + position + "")  {
                    result = i
                    break 
                }
            }
            
            return result
        }

        function findObjectValueByPosition (position) {
            let result = null

            const objects = positions.filter(item => Object.keys(item)[0] === "" + position + "")
            if(objects.length === 0) {
                return result
            }

            const object = objects[0]
            result = object[Object.keys(object)[0]]

            return result
        }

        function findObjectValueByIndex (index) {
            let result = null
            
            const object = positions[index]
            const key = Object.keys(object)[0]
            result = object[key]

            return result
        }

        function findClosingBracketByPosition (opener, startIndex) {
            let followsLF = false
            let shiftMe = false
            let closer = ')'

            if('})]'.includes(opener)) {
                shiftMe = true
                closer = opener
                return {closer, followsLF, shiftMe} 
            }

            if(opener === '{') closer = '}'
            else if(opener === '[') closer = ']'
            
            const l = positions.length
            for (let i = startIndex; i < l; i++) {
                const bracketObject = positions[i]
                const value = Object.values(bracketObject)[0]
                if(value === "\n") {
                    followsLF = true
                }
                if(value === closer) {
                    break 
                }
            }

            return {closer, followsLF, shiftMe} 

        }

        function findClosingTagByPosition (opener, startIndex) {
            
            const semiPos = opener.indexOf(';')
            let tag = opener.replace('&lt;', '').replace('&gt;', '')
            const spacePos = tag.indexOf(' ')
            if(spacePos > 0 && spacePos < tag.length)  {
                tag = tag.substr(0, spacePos)
            }

            let closer = ''
            let followsLF = false
            let shiftMe = false

            if(tag[0] === '/') {
                shiftMe = true
                closer = opener
                return {closer, followsLF, shiftMe} 
            }
            if(semiPos === opener.length - 1)  {
                return {closer, followsLF, shiftMe} 
            }

            const closing = '&lt;/' + tag + '&gt;'

            const l = positions.length
            for (let i = startIndex; i < l; i++) {
                const closerObject = positions[i]
                const value = Object.values(closerObject)[0]
                if(value === "\n") {
                    followsLF = true
                }
                if(value === closing) {
                    closer = closing
                    break 
                }
            }

            return {closer, followsLF, shiftMe} 
        }

        function unshift (char) {
            reg.unshift(char)
        }

        function unshiftLF (char, indent) {
            const text = "\n" + indent + char
            reg.unshift(text)
        }

        function shift (entity) {
            let shifted = -1
            const needle = entity.trim()
            for(const k in reg) {
                let val = reg[k].trim()
                if(val === needle) {
                    shifted = k
                }
                break
            }

            if(shifted > -1)  {
                delete reg[shifted]
            }
        }

        function parseBrackets(text) {
            const result = []
            const regex = /([\(\{\[\n\]\}\)])|(&lt;\/?.+[^&gt;]&gt;)|(&lt;\/?.+[&gt;]&gt;)|(&[a-z]+;)/mg;

            let matches 

            while ((matches = regex.exec(text)) !== null) {
                if (matches.index === regex.lastIndex) {
                    regex.lastIndex++
                }

                const entry = {}
                entry[matches.index] = matches[0]

                result.push(entry)
            }

            return result
        }

        function parseIndents (text) {
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

        function parseNextEntity (text, startIndex) {

            let result = ''
            const regex = /(&[a-z]+;)/;
            const haystack = text.substr(startIndex) 

            const matches = regex.exec(haystack)
            result = matches[0] ?? ''

            return result
        }

        function parseArguments (text, startIndex) {

            const result = []
            const regex =  /([\w]*)(\[\])?=(\"([\S ][^"]*)\"|\'([\S]*)\'|\{\{ ([\w]*) \}\}|\{([\S ]*)\})/m

            const haystack = text.substring(startIndex, text.length - 4) 

            console.log({text, haystack, startIndex})
            let matches 

            while ((matches = regex.exec(haystack)) !== null) {
                if (matches.index === regex.lastIndex) {
                    regex.lastIndex++
                }

                result.push(matches[0] ?? '')
            }

            return result
        }

        async function writeEntities (text, startIndex = 0) {

            if(text.substr(0, 5) === '&lt;/') {
                const closer = text.substr(0, text.length - 4)
                await addChar(lastLineFeed + closer)
                return
            }

            let entitiesText = text.substring(startIndex)

            if(entitiesText === '&gt;') {
                return
            }

            unshift('&gt;')

            if(entitiesText.substr(entitiesText.length - 4) === '&gt;') {
                entitiesText = entitiesText.substr(0, entitiesText.length - 4)
            }

            for(let j = 0; j < entitiesText.length; j++) {
                let char = entitiesText[j] 
                if(char === '&') {
                    const nextEntity = parseNextEntity(entitiesText, j)
                    if(nextEntity !== '') {
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
            result += "<br />"

            return result
        }

        async function loadText(url) {
            let text = ''
            await fetch(url).then(response => response.text()).then((html) => {
                let lines = html.split("\n");
                lines = lines.map(line => line.trim())

                text = lines.join("\n");
            })

            return text
        }

        function protect(text) {
            text = text.replace(/</g, '&lt;')
            text = text.replace(/>/g, '&gt;')
            text = text.replace(/\( /g, '(&nbsp;')
            text = text.replace(/ \)/g, '&nbsp;)')
            text = text.replace(/\{ /g, '{&nbsp;')
            text = text.replace(/ \}/g, '&nbsp;}')

            return text
        }

        let text = ''
        // let codeSource = sourceComponent.dataset['code']
        let codeSource = this.#parent.getAttribute("source") ?? ''

        if(window['hljs'] !== undefined) {
            hljs.highlightElement(sourceComponent);
        }
        text = await loadText(codeSource)

        text = protect(text)

        indents = parseIndents(text)
        text = deleteIndents(text)
        positions = parseBrackets(text)

        console.log({ positions })
        
        const emptyText = makeEmptyText(text)
        sourceComponent.innerHTML = emptyText

        for (let i = 0; i < text.length; i++) {
            let c = text[i]
            
            if(c === "\n") {
                lfCount++
                lastIndent = indents[lfCount]
                lastLineFeed = c + lastIndent

                const value = findObjectValueByPosition(i + 1)
                if(value !== null && value.substr(0, 5) === '&lt;/') {
                    let val = ''
                    for(const k in reg) {
                        if(k === 0) {
                            val = reg[k].substr(1)
                            reg[k] = val
                        }
                    }
                    continue
                }
                
                await addChar(lastLineFeed)
                continue
            }

            if('[({})]'.includes(c)) {
                const objectIndex = findObjectIndexByPosition(i)
                // const htmle = findObjectValueByPosition(i)
                const {closer, followsLF, shiftMe} = findClosingBracketByPosition(c, objectIndex)

                if (shiftMe) {
                    shift(closer) 
                } else if(closer !== '') {
                    if(followsLF) {
                        unshiftLF(closer, lastIndent)
                    } else {
                        unshift(closer)
                    }
                }

                await addChar(c)
                continue
            }

            if(c === '&') {
                const objectIndex = findObjectIndexByPosition(i)
                const htmle = findObjectValueByPosition(i)
                const {closer, followsLF, shiftMe} = findClosingTagByPosition(htmle, objectIndex)

                let opener = closer.replace('/', '')
                opener = opener.replace('&gt;', '')

    

                let withArgs = htmle.includes('&lt;') && htmle.length > 4

                if (shiftMe) {
                    shift(closer) 
                } else if(closer !== '') {
                
                    if(followsLF) {
                        await writeEntities(opener)
                        unshiftLF(closer, lastIndent)
                    } else {
                        unshift(closer)
                    }

                    if(!withArgs) {
                        await addChar('&gt;')
                    }
                }

                i += htmle.length - 1
                
                if(withArgs) {
                    // if(opener === '') {
                    //     opener = htmle.substring(0, htmle.indexOf(' '))
                    // }

                    // const args = parseArguments(htmle, opener.length)
                    // console.log({args})
                    await writeEntities(htmle, opener.length)
                    await addChar('&gt;')

                    continue
                }

                c = htmle
            }

            await addChar(c)
        }
    }

}