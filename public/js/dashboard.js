const scanButton = document.getElementById('scanButton');
const loadingSpinner = document.getElementById('loadingSpinner');
const alertElement = document.getElementById('alert')

scanButton.addEventListener('click', function () {
    scan()
});

async function scan() {
    toggleLoadingState(true)
    hideAlert()

    const format = document.getElementById('formatInput').value
    const scanTarget = document.getElementById('scanTargetInput').value

    try {
        const response = await fetch('/scan?format=' + format + '&target=' + scanTarget, {
            signal: AbortSignal.timeout(60000)
        })

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        setAlert('Scan processed successfully', 'success')
    } catch (error) {
        setAlert('Something went wrong', 'danger')
        console.error("Error:", error);
    }

    toggleLoadingState(false)
}

function toggleLoadingState(loading) {
    if (loading === true) {
        scanButton.disabled = true
        loadingSpinner.classList.remove('d-none')

        return
    }

    scanButton.disabled = false
    loadingSpinner.classList.add('d-none')
}


function setAlert(alertText, alertType) {
    alertElement.className = ''
    alertElement.classList.add('d-flex')
    alertElement.classList.add('justify-content-center')
    alertElement.classList.add('alert')
    alertElement.classList.add('alert-' + alertType)
    alertElement.innerHTML = alertText
}


function hideAlert(alertElementId) {
    alertElement.classList.remove('d-none')
    alertElement.classList.add('d-none')
}
