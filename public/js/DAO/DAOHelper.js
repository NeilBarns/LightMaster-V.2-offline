function handleResponse(response) {
    if (!response.ok) {
        return response.text().then((text) => {
            try {
                const errorJson = JSON.parse(text);
                throw new Error(errorJson.message || errorJson.error || text || 'Unexpected error occurred.');
            } catch (parseError) {
                throw new Error(text || response.statusText || 'An error occurred.');
            }
        });
    }
    return response.json();
}

function handleError(error) {
    return { success: false, message: error.message };
}

function makeRequest(route, method = 'GET', body = null) {
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
    };

    const options = { method, headers };
    if (body) {
        options.body = JSON.stringify(body);
    }

    return fetch(route, options)
        .then(handleResponse)
        .catch(handleError);
}