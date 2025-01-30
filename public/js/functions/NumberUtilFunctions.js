function validateAndFormatNumber(value) {
    // Convert value to a number
    const number = typeof value === 'string' ? parseFloat(value) : value;

    // Validate the number
    if (typeof number !== 'number' || isNaN(number)) {
        console.error('Invalid number:', value);
        return null; // Handle invalid numbers
    }

    // Format the number to two decimal places
    return number.toFixed(2);
}


// Function to format numbers
function formatNumberToDecimal(number, decimals) {
    if (typeof number !== 'number' || isNaN(number)) {
        number = 0; // Default to 0 if the value is not a number
    }
    return number.toFixed(decimals);
    // return number;
}