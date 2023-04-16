export default class Decomposer {
    #depths = []
    #idListByDepth = []
    #list = []
    
    get list() {
        return this.#list
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
        result = text.substring(text.length - 3, 2) === '/>'

        return result
    }

    isCloseTag(tag) {
        let result = false

        let text = tag.text
        if (text === '' || text === '<>') {
            return result
        }
        result = text.substring(0, 2) === '</'

        return result
    }

    makeTag(tag, parentIds, depth, isCloser = false) {
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
            item.hasCloser = !isCloser && (text.substring(text.length - 3, 2) !== '/>')
            item.node = false
        }
        if (parentIds[depth] === undefined || parentIds[depth] === null) {
            parentIds[depth] = i - 1
        }
        item.parentId = parentIds[depth]

        return item
    }

    doComponents(text) {
        const html = text.trim()

        const regex = /<\/?(\w+)((\s|.*?)+?)>|([\{\(\[\]\}\)])/gm

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
        while (allTags.length > 0) {

            if (i === l) {
                i = 0
                let newTags = []
                for(const k in allTags ) {
                    const item  = allTags[k]
                    newTags.push(item)
                }
                allTags = newTags
                l = allTags.length

                if(l === 0 ) {
                    continue
                }  
            }

            let tag = allTags[i]

            if (this.isClosedTag(tag)) {
                let item = this.makeTag(tag, parentIds, depth)
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
                    let item = this.makeTag(tag, parentIds, depth)
                    let closer = this.makeTag(nextMatch, parentIds, depth, true)

                    closer.contents = {}
                    closer.parentId = item.id
                    closer.contents.startsAt = item.endsAt + 1;
                    closer.contents.endsAt = closer.startsAt;
                    let contents = html.substring(closer.contents.startsAt, closer.contents.endsAt)
                    closer.contents.text = '!#base64#' + contents // encodeURIComponent (contents)

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

        const newList = []
        for(const k in list) {
            const item= list[k]
            newList.push(item)
        }

        list = newList

        for (let i = l - 1; i > -1; i--) {
            // Remove useless data
            if (list[i].isCloser) {
                delete list[i]
            } else {
                delete list[i].isCloser
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

        console.log((list))
        this.#list = list
    }

}