document.getElementById('scanButton').addEventListener('click', function () {
    scan()
});

async function scan() {
    const response = await fetch('/scan', {
        signal: AbortSignal.timeout(20000)
    })

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
    }
}
