export default class Decomposer {
    #depths = []
    #idListByDepth = []
    #list = []
    #text = ''
    #workingText = ''
    
    constructor(html) {
        this.#text = html
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

    #createUID() {
        return Date.now() * Math.random()
    }

    doArguments(text) 
    {
        return []
    } 

    isClosedTag(tag) {
        let result = false

        let text = tag.text
        if (text === '' || tag.name === 'Fragment') {
            return result
        }
        result = text.substring(text.length - 6, 5) === '/&lg;'

        return result
    }

    isCloseTag(tag) {
        let result = false

        let text = tag.text
        if (text === '' || text === '<>') {
            return result
        }
        result = text.substring(0, 5) === '&lt;/'

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
            item.props = (item.name === 'Fragment') ? [] : this.doArguments(text)
            item.depth = depth
            item.hasCloser = hasCloser
            item.node = false
        }
        if (parentIds[depth] === undefined || parentIds[depth] === null) {
            parentIds[depth] = i - 1
        }
        item.parentId = parentIds[depth]

        return item
    }

    protect(text) {
        text = text.trim()
        text = text.replace(/\{\{/g, '<D>')
        text = text.replace(/\}\}/g, '</D>')
        text = text.replace(/\(/g, '<C>')
        text = text.replace(/\)/g, '</C>')
        text = text.replace(/\{/g, '<E>')
        text = text.replace(/\}/g, '</E>')
        text = text.replace(/\[/g, '<T>')
        text = text.replace(/\]/g, '</T>')
        text = text.replace(/<([\/\w])/gm, '&lt;$1')
        text = text.replace(/>/g, '&gt;')
        
        return text
    }

    doComponents(tag = '\\w+') {
        this.#workingText = this.protect(this.#text + "\n<Eof />")
        const html = this.#workingText 

        const re = `&lt;\\/?(${tag})((\\s|.*?)+?)\\/?&gt;`

        const regex = new RegExp(re, 'gm')
        let matches
        let list = []
        let depth = 0
        let allTags = []
        let parentIds = []
        parentIds[depth] = -1
        
        // Re-structure the list recursively
        while ((matches = regex.exec(html)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (matches.index === regex.lastIndex) {
                regex.lastIndex++
            }

            list.push(matches)
        }

        for (let i in list) {
            const match = list[i]

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

            allTags.push(tag)
            i++

        }

        this.#depths[depth] = 1

        let l = allTags.length
        let i = 0
        let isFinished = false
        let spinner = 0
        let spinnerMax = l
        let isSpinning = false
        while (allTags.length > 0 && !isFinished && !isSpinning) {

            if (i === l) {
                i = 0
                allTags = Object.values(allTags)
                l = allTags.length
                if(l === 0 ) {
                    isFinished = true
                    continue
                }

                spinner++
                isSpinning = spinner > spinnerMax + 1
            }

            let tag = allTags[i]
            if(allTags.length === 1 && tag.name === 'Eof') {
                isFinished = true
                continue
            }

            if (this.isClosedTag(tag) && tag.name !== 'Eof') {
                let item = this.makeTag(tag, parentIds, depth, false)
                list[item.id] = item
                delete allTags[i]

                i++

                continue
            }

            if (this.isCloseTag(tag)) {
                depth--
            }

            if (i + 1 < l) {
                let nextMatch = allTags[i + 1]

                if (!this.isCloseTag(tag) && this.isCloseTag(nextMatch)) {
                    let item = this.makeTag(tag, parentIds, depth, true)
                    let closer = this.makeTag(nextMatch, parentIds, depth, false, true)

                    if(item.name !== closer.name) {
                        item.hasCloser = false
                        list[item.id] = item
                        delete allTags[i]
                        this.#depths[depth] = 1
                        i++

                        continue
                    }

                    closer.contents = {}
                    closer.parentId = item.id
                    closer.contents.startsAt = item.endsAt + 1;
                    closer.contents.endsAt = closer.startsAt;
                    let contents = html.substring(closer.contents.startsAt, closer.contents.endsAt)
                    closer.contents.text = contents // encodeURIComponent (contents)

                    item.closer = closer

                    list[item.id] = item

                    delete list[closer.id]
                    delete allTags[i]
                    delete allTags[i + 1]

                    i += 2

                    continue
                }

                if (!this.isCloseTag(tag) && !this.isCloseTag(nextMatch)) {
                    depth++
                    parentIds[depth] = tag.id

                }
            }

            this.#depths[depth] = 1

            i++
        }

        list = Object.values(list)

        for (let i = l - 1; i > -1; i--) {
            // Remove useless data
            if(list[i] === undefined) {
                continue
            }

            if (list[i].isCloser) {
                delete list[i]
            } else {
               // delete list[i]['closer']
            }
        }

        let maxDepth = this.#depths.length
        for (let i = maxDepth; i > -1; i--) {
            for (const match of list) {
                if (match.depth === i) {
                    this.#idListByDepth.push(match.id)
                }
            }
        }

        this.#list = list
    }

}