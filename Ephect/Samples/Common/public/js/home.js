function doSomething() {
    const appContent = document.querySelector("div[class='App-content']")
    appContent.addEventListener("finishedWriting", (e) => {
        appContent.innerHTML = e.detail.content
    })
}
