const OPEN_TAG = '&lt;'
const CLOSE_TAG = '&gt;'
const TERMINATOR = '/'

export default class Decomposer {
    #list = []
    #text = ''
    #workingText = ''
    #words = []
    #phraseStarts = []
    #phraseLengths = []
    #wordEnds = []
    #mistakes = []
    #mistakeCursors = []

    constructor(html, doMarkUpQuotes = false) {
        this.#text = html

        this.#workingText = this.#text + "\n<Eof />"

        this.protect()
        if(doMarkUpQuotes) {
            this.markupQuotes()
        }

        this.collectWords(this.#workingText)
        this.makeMistakes()

    }

    get list() {
        return this.#list
    }

    get text() {
        return this.#text
    }

    get workingText() {
        return this.#workingText
    }
    
    get words() {
        return this.#words
    }

    get mistakes() {
        return this.#mistakes
    }

    get mistakeCursors() {
        return this.#mistakeCursors
    }
    
    get phraseStarts() {
        return this.#phraseStarts
    }

    get phraseLengths() {
        return this.#phraseLengths
    }

    get wordEnds() {
        return this.#wordEnds
    }

    #createUID() {
        return Date.now() * Math.random()
    }

    translateBracket(base, name, isClosing = false) {
        // It also translate quotes
        let word = base
        let translated = false

        if ('CDETQRG'.includes(name)) {
            if (name === 'C') {
                word = isClosing ? ')' : '('
                translated = true
            }
            if (name === 'D') {
                word = isClosing ? '}}' : '{{'
                translated = true
            }
            if (name === 'E') {
                word = isClosing ? '}' : '{'
                translated = true
            }
            if (name === 'T') {
                word = isClosing ? ']' : '['
                translated = true
            }
            if (name === 'Q') {
                word = "'"
                translated = true
            }
            if (name === 'R') {
                word = '"'
                translated = true
            }
            if (name === 'G') {
                word = '`'
                translated = true
            }
        }

        return {word, translated}
    }

    markupQuotes() {

        let html = this.#workingText
        const regex = new RegExp('(["\'`])((\\s|((\\\\)*)\\\\.|.)*?)\\1', 'gm')
        let matches
        const attributes = []

        while ((matches = regex.exec(html)) !== null) {
            if (matches.index === regex.lastIndex) {
                regex.lastIndex++
            }
            attributes.push(matches)
        }

        for (let i = attributes.length - 1; i > -1; i--) {
            const attr = attributes[i]
            const quote = attr[1]
            const quoted = attr[0]
            let unQuoted = attr[2]
            const start = attr.index + 1
            const end = start + quoted.length - 1

            let letter = ''
            if (quote === '"') {
                letter = 'R'
            } else if (quote === '\'') {
                letter = 'Q'
            } else if (quote === '`') {
                letter = 'G'
            }

            unQuoted = unQuoted.replace(/&lt;/g, '&pp;')
            unQuoted = unQuoted.replace(/&gt;/g, '&pg;')
            const newValue = '&oq;' + letter + '&cq;' + unQuoted + '&oq;/' + letter + '&cq;'

            const beginBlock = html.substring(0, start - 1)
            const endBlock = html.substring(end)

            html = beginBlock + newValue + endBlock

        }

        this.#workingText = html
    }

    doAttributes(text) {

        let result = {}
        const regex = /([\w]*)(\[\])?=(\"([\S ][^"]*)\"|\'([\S]*)\'|\{\{ ([\w]*) \}\}|\{([\S ]*)\})/gm

        let matches
        const attributes = []

        while ((matches = regex.exec(text)) !== null) {
            if (matches.index === regex.lastIndex) {
                regex.lastIndex++
            }

            attributes.push(matches)
        }
        for (let attr of attributes) {
            const key = attr[1]
            const brackets = attr[2]
            const quote = attr[3].substring(0, 1)
            const value = attr[4]


            if (brackets === '[]') {
                if (result[key] === undefined) {
                    result[key] = []
                }
                result[key].push(quote + value)
            } else {
                result[key] = quote + "" + value
            }
        }

        return result
    }

    isClosedTag(tag) {
        let result = false

        let text = tag.text
        if (text === '') {
            return result
        }
        result = text.substring(text.length - 5, text.length) === TERMINATOR + CLOSE_TAG

        return result
    }

    isCloserTag(tag) {
        let result = false

        let text = tag.text
        if (text === '') {
            return result
        }
        result = text.substring(0, 5) === OPEN_TAG + TERMINATOR

        return result
    }

    makeTag(tag, parentIds, depth, hasCloser, isCloser = false) {
        let text = tag.text
        let name = tag.name

        let i = this.list.length
        let item = {}

        item.id = tag.id
        item.name = name === '' ? 'Fragment' : name
        item.text = text
        item.startsAt = tag.startsAt
        item.endsAt = tag.endsAt
        if (!isCloser) {
            item.uid = this.#createUID
            item.method = 'echo'
            item.props = (item.name === 'Fragment') ? [] : [] //this.doAttributes(text)
            item.depth = depth
            item.hasCloser = hasCloser
            item.node = false
            item.isSingle = false
        }
        if (parentIds[depth] === undefined || parentIds[depth] === null) {
            parentIds[depth] = i - 1
        }
        item.parentId = parentIds[depth]

        return item
    }

    protect() {
        let text = this.#workingText
        text = text.trim()
        text = text.replace(/\{\{/g, '<D>')
        text = text.replace(/\}\}/g, '</D>')
        text = text.replace(/\(/g, '<C>')
        text = text.replace(/\)/g, '</C>')
        text = text.replace(/\{/g, '<E>')
        text = text.replace(/\}/g, '</E>')
        text = text.replace(/\[/g, '<T>')
        text = text.replace(/\]/g, '</T>')
        text = text.replace(/<([\/\w])/g, OPEN_TAG + '$1')
        text = text.replace(/>/g, CLOSE_TAG)

        this.#workingText = text
    }

    collectTags(text, rule = '[\\w]+') {
        const result = []
        let list = []

        const re = OPEN_TAG + `\\/?(${rule})((\\s|.*?)*?)\\/?` + CLOSE_TAG

        const regex = new RegExp(re, 'gm')
        let matches

        // Re-structure the list recursively
        while ((matches = regex.exec(text)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (matches.index === regex.lastIndex) {
                regex.lastIndex++
            }

            list.push(matches)
        }

        let i = 0
        list.forEach(match => {

            let tag = match
            tag.id = i
            tag.text = match[0]
            tag.name = match[1] === null ? 'Fragment' : match[1]
            tag.startsAt = match.index
            tag.endsAt = match.index + tag.text.length - 1

            delete tag[0]
            delete tag[1]
            delete tag[2]
            delete tag[3]

            result.push(tag)
            i++
        })

        return result
    }

    collectWords(text) {
        let result = []
        let list = []
        let regex = /([&oqpglt;]{4})[\w \/]+([&cqppgt;]{4})/gm;
        let matches

        while ((matches = regex.exec(text)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (matches.index === regex.lastIndex) {
                regex.lastIndex++
            }

            list.push(matches)
        }

        for (let i = list.length - 1; i > -1; i--) {
            const tag = list[i][0]
            const start = list[i].index + 1
            const end = start + tag.length - 1

            const spaces = "•".repeat(tag.length)

            const beginBlock = text.substring(0, start - 1)
            const endBlock = text.substring(end)

            text = beginBlock + spaces + endBlock
        }

        regex = /((?!•)\S[^•\n]*)/g;
        while ((matches = regex.exec(text)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (matches.index === regex.lastIndex) {
                regex.lastIndex++
            }

            this.#phraseStarts.push(matches.index)
            this.#phraseLengths.push(matches[0].length)
        }

        regex = /((?!•)\S[^•\n ]*)/g;
        while ((matches = regex.exec(text)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (matches.index === regex.lastIndex) {
                regex.lastIndex++
            }

            const expression = {}
            expression.text = matches[0]
            expression.startsAt =  matches.index
            expression.endsAt = expression.startsAt + matches[0].length - 1

            result.push(expression)
            this.#wordEnds.push(expression.endsAt)
        }

        this.#words = result

    }

    makeMistakes() {

        // Prevent always starting mistakes on first word
        let i = Math.ceil(Math.random() * 2) - 2
        // Select between 3 and 5 the probabitity of mistakes
        let probability = Math.ceil(Math.random() * 3) + 2

        this.#words.forEach(item => {
            i++
            if(i % probability !== 0 || item.text.length < 4) {
                return
            }
            const needleCharPos = Math.ceil(Math.random() * item.text.length) - 1 
            const mistake = String.fromCharCode(Math.ceil(Math.random() * 26) + 96)

            this.#mistakeCursors.push(item.startsAt + needleCharPos)
            this.#mistakes.push(mistake)

        })

    }

    makeFaultyText() {
        let text = this.#workingText

        this.#mistakes.forEach(item => {
            const begin = text.substring(0, item.startsAt)
            const end  = text.substring(item.endsAt + 1)
            text = begin + item.text + end
        })

        this.#workingText = text
    }

    splitTags(allTags) {

        let tags = [...allTags]

        let l = tags.length
        let i = 0
        let isFinished = false
        let spinner = 0
        let spinnerMax = l
        let isSpinning = false
        const singleTags = []
        const regularTags = []

        while (tags.length > 0 && !isFinished && !isSpinning) {

            if (i === l) {
                i = 0
                tags = Object.values(tags)
                l = tags.length
                if (l === 0) {
                    isFinished = true
                    continue
                }

                spinner++
                isSpinning = spinner > spinnerMax + 1
            }

            let tag = tags[i]
            if (tags.length === 1 && tag.name === 'Eof') {
                isFinished = true
                continue
            }

            if (this.isClosedTag(tag) && tag.name !== 'Eof') {
                regularTags[i] = tags[i]
                delete tags[i]
                i++
                continue
            }

            if (i + 1 < l) {
                let nextMatch = tags[i + 1]

                if (!this.isCloserTag(tag) && this.isCloserTag(nextMatch)) {
                    if (tag.name !== nextMatch.name) {
                        singleTags.push(tag)
                        delete tags[i]
                        i++
                        continue
                    }

                    regularTags[i] = tags[i]
                    regularTags[i + 1] = tags[i + 1]
                    delete tags[i]
                    delete tags[i + 1]

                    i += 2
                    continue
                }
            }
            i++
        }
        return {regularTags, singleTags}
    }

    replaceTags(text, tags) {
        let result = text
        const list = []

        tags.forEach(item => {
            list[item.id] = item
        })

        list.sort()
        tags = Object.values(list)

        for (let i = tags.length - 1; i > -1; i--) {
            const tag = tags[i]
            tag.text = tag.text.substring(0, tag.text.length - 4) + TERMINATOR + CLOSE_TAG;

            const begin = result.substring(0, tag.startsAt)
            const end = result.substring(tag.endsAt + 1)

            result = begin + tag.text + end
        }

        return result;
    }

    doComponents(rule = '[\\w]+') {
        let html = this.#workingText
        const allTags = this.collectTags(html, rule)
        const singleIdList = []
        let list = []
        let depth = 0
        let parentIds = []
        let l = allTags.length
        let i = 0
        let isFinished = false
        let spinner = 0
        let spinnerMax = l
        let isSpinning = false

        parentIds[depth] = -1

        const {regularTags, singleTags} = this.splitTags(allTags)

        let workTags = allTags

        if (singleTags.length) {
            singleTags.forEach(item => singleIdList.push(item.id))
            html = this.replaceTags(html, singleTags)
            workTags = this.collectTags(html, rule)
        }

        while (workTags.length > 0 && !isFinished && !isSpinning) {

            if (i === l) {
                i = 0
                workTags = Object.values(workTags)
                l = workTags.length
                if (l === 0) {
                    isFinished = true
                    continue
                }

                spinner++
                isSpinning = spinner > spinnerMax + 1
            }

            let tag = workTags[i]
            if (workTags.length === 1 && tag.name === 'Eof') {
                isFinished = true
                continue
            }

            if (this.isClosedTag(tag) && tag.name !== 'Eof') {
                let item = this.makeTag(tag, parentIds, depth, false)
                item.isSingle = singleIdList.includes(tag.id)
                list[item.id] = item
                delete workTags[i]
                i++

                continue
            }

            if (this.isCloserTag(tag)) {
                depth--
            }

            if (i + 1 < l) {
                let nextMatch = workTags[i + 1]

                if (!this.isCloserTag(tag) && this.isCloserTag(nextMatch)) {
                    let item = this.makeTag(tag, parentIds, depth, true)
                    let closer = this.makeTag(nextMatch, parentIds, depth, false, true)

                    closer.contents = {}
                    closer.parentId = item.id
                    closer.contents.startsAt = item.endsAt + 1;
                    closer.contents.endsAt = closer.startsAt;
                    let contents = html.substring(closer.contents.startsAt, closer.contents.endsAt)
                    closer.contents.text = contents

                    item.closer = closer

                    list[item.id] = item

                    delete workTags[i]
                    delete workTags[i + 1]

                    i += 2

                    continue
                }

                if (!this.isCloserTag(tag) && !this.isCloserTag(nextMatch)) {
                    depth++
                    parentIds[depth] = tag.id
                }
            }

            i++
        }

        list = Object.values(list)

        for (let i = l - 1; i > -1; i--) {
            // Remove useless data
            if (list[i] === undefined) {
                continue
            }

            if (list[i].isCloser) {
                delete list[i]
            }
        }

        this.#workingText = html
        this.#list = list
    }

}