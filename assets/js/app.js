const tableBody = document.querySelector('#crypto-table tbody');
const button = document.querySelector("[data-theme-toggle]");

function calculateSettingAsThemeString() {
    const localTheme = localStorage.getItem("theme");
    if (localTheme) return localTheme;
    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
}

function updateThemeOnHtmlEl(theme) {
    document.querySelector("html").setAttribute("data-theme", theme);
}

function updateButtonIcon(theme) {
    if (!button) return;
    const icon = theme === "dark" ? "☀️" : "🌙";
    const label = theme === "dark" ? "light" : "dark";
    button.innerHTML = `${icon} ${label}`;
}

let currentThemeSetting = calculateSettingAsThemeString();
updateThemeOnHtmlEl(currentThemeSetting);
updateButtonIcon(currentThemeSetting);

button.addEventListener("click", () => {
    const newTheme = currentThemeSetting === "dark" ? "light" : "dark";
    localStorage.setItem("theme", newTheme);
    updateThemeOnHtmlEl(newTheme);
    updateButtonIcon(newTheme);
    currentThemeSetting = newTheme;
    fetchData();
});

function fetchData() {
    fetch("data/fetch.php")
        .then(res => res.json())
        .then(data => {
            if (!data || !data.length) {
                tableBody.innerHTML = "<tr><td colspan='7'>داده‌ای برای نمایش وجود ندارد.</td></tr>";
                return;
            }
            let rows = "";
            data.forEach((coin, i) => {
                rows += `<tr>
                    <td data-label="#">${i + 1}</td>
                    <td data-label="name">${coin.name} (${coin.symbol})</td>
                    <td data-label="price">${Number(coin.price).toLocaleString()}</td>
                    <td data-label="1h%" class="${coin.percent_change_1h >= 0 ? 'positive' : 'negative'}">${coin.percent_change_1h.toFixed(2)}</td>
                    <td data-label="24h%" class="${coin.percent_change_24h >= 0 ? 'positive' : 'negative'}">${coin.percent_change_24h.toFixed(2)}</td>
                    <td data-label="7d%" class="${coin.percent_change_7d >= 0 ? 'positive' : 'negative'}">${coin.percent_change_7d.toFixed(2)}</td>
                    <td data-label="chart"><img src="charts/chart.php?prices=${coin.sparkline.join(',')}" alt="chart"></td>
                </tr>`;
                console.log(`${coin.name} => symbol:`, coin.symbol);
            });
            tableBody.innerHTML = rows;
        })
        .catch(err => {
            console.error("خطا در دریافت داده‌ها:", err);
            tableBody.innerHTML = "<tr><td id='error' colspan='7'>خطا در دریافت داده‌ها.</td></tr>";
        });
}

setInterval(fetchData, 5000);
fetchData();
