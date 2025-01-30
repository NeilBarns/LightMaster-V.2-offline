const SIDEBAR_STATE_KEY = "sidebar_state"; 

function getSideBarLocalStorageValue()
{
    return localStorage.getItem(SIDEBAR_STATE_KEY);
}

function setSideBarLocalStorateValue(value)
{
    localStorage.setItem(SIDEBAR_STATE_KEY, value);
}