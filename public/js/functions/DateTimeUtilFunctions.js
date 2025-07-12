function formatTime(date) {
    const hours = date.getHours().toString().padStart(2, "0");
    const minutes = date.getMinutes().toString().padStart(2, "0");
    const seconds = date.getSeconds().toString().padStart(2, "0");
    return `${hours}:${minutes}:${seconds}`;
}

function convertTo12HourFormat(timeString) {
    let [hours, minutes, seconds] = timeString.split(":").map(Number);
    const ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12;
    return `${hours}:${minutes.toString().padStart(2, "0")}:${seconds
        .toString()
        .padStart(2, "0")} ${ampm}`;
}

function convertMinutesToHoursAndMinutes(minutes) {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;

    if (hours > 0 && remainingMinutes > 0) {
        return `${hours} hr(s) ${remainingMinutes} min(s)`;
    } else if (hours > 0) {
        return `${hours} hr(s)`;
    } else {
        return `${remainingMinutes} min(s)`;
    }
}

function convertSecondsToTimeFormat(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = seconds % 60;

    let timeString = "";

    if (hours > 0) {
        timeString += `${hours} hr(s) `;
    }
    if (minutes > 0) {
        timeString += `${minutes} min(s) `;
    }
    timeString += `${remainingSeconds} sec(s)`;

    return timeString;
}

function getDaysInCurrentMonth() {
    const date = new Date();
    const days = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    return Array.from(
        {
            length: days,
        },
        (_, i) => i + 1
    );
}

function formatDate(dateInput) {
    // Ensure the input is a Date object
    const date =
        typeof dateInput === "string" ? new Date(dateInput) : dateInput;

    if (isNaN(date)) {
        return "Invalid date"; // Handle invalid date strings
    }

    const months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sept",
        "Oct",
        "Nov",
        "Dec",
    ];

    const month = months[date.getMonth()]; // Full month name
    const day = String(date.getDate()).padStart(2, "0"); // Day with leading zero
    const year = date.getFullYear(); // Full year

    let hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");
    const ampm = hours >= 12 ? "PM" : "AM"; // AM or PM

    hours = hours % 12 || 12; // Convert to 12-hour format

    return `${month} ${day}, ${year} ${String(hours).padStart(
        2,
        "0"
    )}:${minutes}:${seconds} ${ampm}`;
}

function formatDateTime(input) {
    // Parse the input date string
    const date = new Date(input);

    // Extract date parts
    const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are zero-based
    const day = String(date.getDate()).padStart(2, "0");
    const year = date.getFullYear();

    // Extract time parts
    let hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");

    // Determine AM/PM
    const ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12; // Convert to 12-hour format, replacing 0 with 12

    // Combine into the desired format
    return `${month}/${day}/${year} ${String(hours).padStart(
        2,
        "0"
    )}:${minutes}:${seconds} ${ampm}`;
}

function getFormattedTimestamp() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0"); // Months are 0-indexed
    const day = String(now.getDate()).padStart(2, "0");
    const hour = String(now.getHours()).padStart(2, "0");
    const minute = String(now.getMinutes()).padStart(2, "0");
    const second = String(now.getSeconds()).padStart(2, "0");

    return `${year}${month}${day}${hour}${minute}${second}`;
}

function formatSecondsToHHMMSS(totalSeconds) {
    const hours = Math.floor(totalSeconds / 3600); // Calculate hours
    const minutes = Math.floor((totalSeconds % 3600) / 60); // Calculate minutes
    const seconds = totalSeconds % 60; // Calculate seconds

    // Pad with leading zeros if needed
    const paddedHours = String(hours).padStart(2, "0");
    const paddedMinutes = String(minutes).padStart(2, "0");
    const paddedSeconds = String(seconds).padStart(2, "0");

    return `${paddedHours}:${paddedMinutes}:${paddedSeconds}`;
}

function nowSynced() {
    return Date.now() + serverTimeOffset;
}

window.serverTimeOffset = 0;

window.nowSynced = function () {
    return Date.now() + window.serverTimeOffset;
};

window.setServerTimeOffset = function (serverTime) {
    const clientNow = Date.now();
    window.serverTimeOffset = serverTime - clientNow;
};
